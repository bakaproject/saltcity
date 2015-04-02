<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Adminhtml_BlockController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/ameasyblocks')
            ->_addBreadcrumb(Mage::helper('ameasyblocks')->__('CMS'),       Mage::helper('ameasyblocks')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('ameasyblocks')->__('Blocks'), Mage::helper('ameasyblocks')->__('Blocks'))
        ;
        return $this;
    }
    
    public function indexAction()
    {
        $this->_title($this->__('Blocks'));
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('ameasyblocks/adminhtml_block'))
            ->renderLayout();
    }
    
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $this->_title($this->__('Blocks'))->_title($this->__('Edit Block'));
        
        $id   = $this->getRequest()->getParam('block_id');
        $block = Mage::getModel('ameasyblocks/block');
        if ($id) 
        {
            $block->load($id);
            if (!$block->getId()) 
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ameasyblocks')->__('This block no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        
        // Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) 
        {
            $block->setData($data);
        }
        
        Mage::register('ameasyblocks_block', $block);
             
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('ameasyblocks/adminhtml_block_edit'))
            ->_addLeft($this->getLayout()->createBlock('ameasyblocks/adminhtml_block_edit_tabs'))
            ->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            $data = $this->_filterDates($data, array('from_date', 'to_date'));
            $blockId = $this->getRequest()->getParam('block_id');
            $model  = Mage::getModel('ameasyblocks/block');
            if ($blockId) 
            {
                $model->load($blockId);
            }
            $model->setData($data);
            try 
            {
                $model->save();
                $blockId = $model->getId();
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ameasyblocks')->__('The block has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                $this->_getSession()->addException($e, Mage::helper('ameasyblocks')->__('An error occurred while saving the block: ') . $e->getMessage());
            }
            
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('block_id' => $blockId));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('block_id')) 
        {
            try 
            {
                $model = Mage::getModel('ameasyblocks/block');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ameasyblocks')->__('The block has been deleted.'));
                $this->_redirect('*/*/');
                return;
                
            } catch (Exception $e) 
            {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('block_id' => $id));
                return;
            }
        }
    }
}
