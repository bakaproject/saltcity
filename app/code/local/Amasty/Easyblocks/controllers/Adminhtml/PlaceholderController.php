<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Adminhtml_PlaceholderController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/ameasyblocks')
            ->_addBreadcrumb(Mage::helper('ameasyblocks')->__('CMS'),       Mage::helper('ameasyblocks')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('ameasyblocks')->__('Placeholders'), Mage::helper('ameasyblocks')->__('Placeholders'))
        ;
        return $this;
    }
    
    public function indexAction()
    {
        $this->_title($this->__('Placeholders'));
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder'))
            ->renderLayout();
    }
    
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
    
    protected function _initPlaceholder()
    {
        $id   = $this->getRequest()->getParam('placeholder_id');
        $placeholder = Mage::getModel('ameasyblocks/placeholder');
        if ($id) 
        {
            $placeholder->load($id);
            if (!$placeholder->getId()) 
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ameasyblocks')->__('This placeholder no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        Mage::register('ameasyblocks_placeholder', $placeholder);
    }
    
    public function editAction()
    {
        $this->_title($this->__('Placeholders'))->_title($this->__('Edit Placeholder'));
        
        $this->_initPlaceholder();
        
        // Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) 
        {
            $placeholder->setData($data);
        }
        
        
        $this->_initAction();
        // sinse 1.5.1
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        
        $this->_addContent($this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit'))
            ->_addLeft($this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tabs'))
            ->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            if (isset($data['selected_blocks']))
            {
                $data['should_save_selected_blocks'] = true;
            }
            $placeholderId = $this->getRequest()->getParam('placeholder_id');
            $model  = Mage::getModel('ameasyblocks/placeholder');
            if ($placeholderId) 
            {
                $model->load($placeholderId);
            }
            
            if (isset($data['stores'])) {
                $data['store_ids'] = implode(',', $data['stores']);
            }
            
            if (isset($data['category_ids'])) {
                $data['category_ids'] = explode(',', $data['category_ids']);
                if (is_array($data['category_ids'])) {
                    foreach ($data['category_ids'] as $i => $categoryId) {
                        if (!$categoryId) {
                            unset($data['category_ids'][$i]);
                        }
                    }
                    $data['category_ids'] = array_unique($data['category_ids']);
                }
                $data['category_ids'] = implode(',', $data['category_ids']);
            }
            
            $model->setData($data);
            
            try {
                $model->save();
                $placeholderId = $model->getId();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ameasyblocks')->__('The placeholder has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('ameasyblocks')->__('An error occurred while saving the placeholder: ') . $e->getMessage());
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('placeholder_id' => $placeholderId));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('placeholder_id')) 
        {
            try 
            {
                $model = Mage::getModel('ameasyblocks/placeholder');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ameasyblocks')->__('The placeholder has been deleted.'));
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('placeholder_id' => $id));
                return;
            }
        }
    }
    
    public function categoriesJsonAction()
    {
        $this->_initPlaceholder();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_config_category')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
    
    public function blocksGridAction()
    {
        $this->_initPlaceholder();
        
        $grid = $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_blocks')->setSelectedBlocks($this->getRequest()->getPost('selected_blocks', null));
        $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serializer->initSerializerBlock($grid, 'getSelectedBlockBlocks', 'selected_blocks', 'selected_blocks');
        
        $this->getResponse()->setBody(
            $grid->toHtml() . $serializer->toHtml()
        );
    }
}
