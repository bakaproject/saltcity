<?php
class Lightspeed_Connector_Model_Core_Api extends Mage_Api_Model_Resource_Abstract
{
    public function listOrders($date = null, $offset=null,$limit = null){
        
        $collection = Mage::getModel("sales/order")->getCollection();
        
        if (is_null($date) == False){
            $collection->addFieldToFilter('updated_at', array('from'=>$date));
        }
        if (is_null($offset) == False and is_null($limit)==False){
            $collection->setPage($offset,$limit);
        }
        $collection->setOrder('updated_at','asc');
        $collection->load();
        $result = array();
        
        if ($offset <= $collection->getLastPageNumber()){
            foreach ($collection as $order) {
                $order_array = array();
                $order_array['increment_id']=$order->increment_id;
                $result[] = $order_array;
            }
        }
        return $result;
    }
	public function listPaymentMethods(){
		$payment_helper = Mage::helper('payment');
		$payment_method_list = $payment_helper->getPaymentMethods();
		$payment_methods=array();
		foreach ($payment_method_list as $key => $payment_method){
			$code=$key;
			$title=$payment_method['title'];
			$payment_methods[$code]=$title;
		}
		return $payment_methods;
	}
	
    public function listProductTaxClasses(){
        $tax_classes=Mage::getModel('Mage_Tax_Model_Class_Source_Product')->getAllOptions();    
        $product_taxes=array();
        foreach($tax_classes as $key=>$tax_class)
        {
            if(!isset($product_taxes[$tax_class["label"]]))
            {
                $product_taxes["".$tax_class["label"].""] = $tax_class["value"];
            }
        }
        return $product_taxes;
    }
    
	public function listTaxesForOrder($order_id){
	        $collection = Mage::getResourceModel('sales/order_tax_collection')
            ->addFieldToFilter('order_id',$order_id);
            
            $taxes_for_order=array();
            foreach ($collection as $value){
            	$tax=array();
            	$tax['tax_rate_identifier'] = $value['title'];
            	$tax['rate'] = $value['percent'];	
            	array_push($taxes_for_order,$tax);
            }
            return $taxes_for_order;
	}
	
	public function createConfigAttribute($productSku,$attributeId,$label){
	
		// Load the product
		$product = Mage::getModel('catalog/product')->setStoreId(0);
		$product=Mage::getModel('catalog/product')->loadByAttribute('sku',$productSku);
		
        if (!$product) {
            $this->_fault('configurable_product_does_not_exist');
        }
		
        $attribute = Mage::getModel('catalog/entity_attribute')->setStoreId(0);
        $attribute->load($attributeId);
            
		if ($product && $attribute){
			// Check that the configurable attribute doesn't already exist for the configurable product.
			$product_configurable_attributes = $product->getTypeInstance()->getConfigurableAttributesAsArray();
			foreach($product_configurable_attributes as $attrib){
				if ($attributeId==$attrib["attribute_id"]){
					$this->_fault('configurable_attribute_already_exists');
				}
			}
			$configurableAttribute = Mage::getModel("catalog/product_type_configurable_attribute");
			$configurableAttribute->setProductId($product->getId());
			$configurableAttribute->setAttributeId($attributeId);
	    	$configurableAttribute->setPosition(0);
	    	$configurableAttribute->setStoreId(0);
			$configurableAttribute->setLabel($label);
			$configurableAttribute->save();
    		$configurableAttribute->setValues(null);
    		$configurableAttribute->save();
			return $configurableAttribute->getId();
		}
		return false;
	}
	
	public function assignProductsToConfigurable($configurableProductSku,array $productSkus){
		$configurable_product=Mage::getModel('catalog/product')->loadByAttribute('sku',$configurableProductSku);
        if (!$configurable_product) {
            $this->_fault('configurable_product_does_not_exist');
        }
		
		$children_id_array=Mage::getResourceSingleton('catalog/product_type_configurable')->getChildrenIds($configurable_product->getId());
		
		$childrenIds=$children_id_array[0];
		/**
		foreach ($children_id_array as $key->$value){
			$childrenIds[$value]=$value;
		}
		*/
		$product = Mage::getModel('catalog/product');
		
		
		foreach($productSkus as $sku){
			$productId = $product->getIdBySku($sku);
			if ($productId){
				if(!array_key_exists($productId,$childrenIds)){
					$childrenIds[$productId]=$productId;
				} 						
			}
		}
		$configurable_product->setConfigurableProductsData($childrenIds);
		$configurable_product->save();
		return true;
		
	}
	public function createAttributeOption($attributeId,$option_label){
        $attribute = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->getResource()
            ->getAttribute($attributeId);
        if (!$attribute->getId()) {
            $this->_fault('attribute_does_not_exist');
        }
		foreach ($attribute->getSource()->getAllOptions(true) as $option){
			if (strcmp($option_label,$option['label']) == 0){
				$this->_fault('attribute_option_already_exists');
			}
		}
		$option=array();
		$option['attribute_id'] = $attributeId; 
		$option['value']['new_option'][0] = $option_label;
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeOption($option);
		// Since the addAtributeOption does not return a value, we will have to parse out the new option by name and return it's id.
		
		$attribute->save();
		
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->addFieldToFilter('attribute_id',$attributeId)
        	->join('attribute_option_value','main_table.option_id=attribute_option_value.option_id')
            ->addFieldToFilter('value',$option_label);
		$new_option = $collection->getFirstItem();
		
		return $new_option->getId();
	}
	
	public function deleteAttributeOption($optionId){
		$attribute_option = Mage::getModel("eav/entity_attribute_option")->load($optionId);
        if(!$attribute_option->getOptionId())
        {
            $this->_fault('option_does_not_exist');
        }
        $attribute_id=$attribute_option->getAttributeID();
        $attribute = Mage::getModel('eav/entity_attribute');
        $attribute->load($attribute_id);

        if (!$attribute->getId()) {
            $this->_fault('attribute_does_not_exist');
        }

        $oData = array();
        $oData['value'][$optionID] = true;
        $oData['delete'][$optionID] = true;

        $attribute->setOption($oData);

        $attribute->save();
		
		$attribute_option->delete();
        return true;
	}
	
    private function getProductEntityTypeId()
    {
        return Mage::getModel('catalog/product')->getResource()->getEntityType();
    }
    
	public function createLightSpeedAttributeSet(){
		// Find the Default Product attribute set. 
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter($this->getProductEntityTypeId()->getId())
            ->addFieldToFilter('attribute_set_name','Default');
		$default_attribute_set = $collection->getFirstItem();
		
		// Create a attribute set called "LIGHTSPEED_PRODUCT_ATTRIBUTE_SET" based off of the Default product attribute set.
		$lightspeed_attribute_set_name="LIGHTSPEED_PRODUCT_ATTRIBUTE_SET";
		$attrSet = Mage::getModel('eav/entity_attribute_set');
		$attrSet->setAttributeSetName($lightspeed_attribute_set_name);
		$attrSet->setEntityTypeId($this->getProductEntityTypeId()->getId());
		try{
			$attrSet->save();
		} catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
		
            
            
		$attrSet=$attrSet->initFromSkeleton($default_attribute_set->getId());
		$attrSet->save();
		
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		
		// Create a group called LIGHTSPEED_ATTRIBUTES for the 
		$setup->addAttributeGroup('catalog_product',$attrSet->getId(),'LIGHTSPEED_ATTRIBUTES');
        
        $attrSet->save();
		
		// Create the attributes to be put into the LIGHTSPEED_ATTRIBUTES group.
		$product_colour_code='LIGHTSPEED_PRODUCT_COLOR';
		$product_size_code='LIGHTSPEED_PRODUCT_SIZE';
		
		$this->createAttribute($product_colour_code,'Color'); // Colour is spelt Color, the American way.
		$this->createAttribute($product_size_code,'Size');
		
		$attribute_group=$setup->getAttributeGroup('catalog_product',$attrSet->getId(),'LIGHTSPEED_ATTRIBUTES');
		$colour_attribute=$setup->getAttribute('catalog_product', $product_colour_code);
		$size_attribute=$setup->getAttribute('catalog_product', $product_size_code);
		
		$attrSet->save();
		
		// Add attributes to attribute set.
		$setup->addAttributeToSet($entityTypeId='catalog_product',$attrSet->getId(), $attribute_group['attribute_group_id'], $colour_attribute['attribute_id']);
		$setup->addAttributeToSet($entityTypeId='catalog_product',$attrSet->getId(), $attribute_group['attribute_group_id'], $size_attribute['attribute_id']);
		
		return $attrSet->getId();
	}

	public function assignProductToCategory($categoryId, $productSku, $position = null){
	
        $category = Mage::getModel('catalog/category')
	        ->setStoreId(0)
	        ->load($categoryId);
	        
	    if (!$category->getId()) {
            $this->_fault('category_does_not_exist');
        }
        $positions = $category->getProductsPosition();
        
        $product=Mage::getModel('catalog/product')->loadByAttribute('sku',$productSku);
        if (!$product){
        	$this->_fault('product_does_not_exist');
        }
        
        $positions[$product->getId()] = $position;
        $category->setPostedProducts($positions);

        try {
            $category->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
		
	}
	private function createAttribute($code, $label){
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$data=array(
		             'label'             => $label,
		             'type'              => 'varchar',
		             'input'             => 'select',
		             'backend'           => 'eav/entity_attribute_backend_array',
		             'frontend'          => '',
		             'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
		             'visible'           => true,
		             'required'          => true,
		             'user_defined'      => true,
		             'searchable'        => false,
		             'filterable'        => false,
		             'comparable'        => false,
		             'option'            => array (
		                                            'value' => array()
		                                        ),
		             'visible_on_front'  => false,
		             'visible_in_advanced_search' => false,
		             'unique'            => false,
		             'configurable' =>true
		);
		$attribute = $setup->addAttribute('catalog_product', $code,$data);
		return $attribute;
	}
	
	
}
?>
