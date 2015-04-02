<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tab_Config_Category extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_categoryIds;
    protected $_selectedNodes = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('ameasyblocks/placeholder/categories.phtml');
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }

    protected function getCategoryIds()
    {
        $model = Mage::registry('ameasyblocks_placeholder');
        if ($model)
        {
            return explode(',', $model->getCategoryIds());
        }
        return array();
    }
    
    /**
    * Tab related methods
    */

    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Configuration: Categories');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Configuration: Categories');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
}