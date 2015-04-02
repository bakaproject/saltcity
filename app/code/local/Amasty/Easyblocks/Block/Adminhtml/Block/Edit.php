<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Block_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'block_id';
        $this->_blockGroup = 'ameasyblocks';
        $this->_controller = 'adminhtml_block';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('ameasyblocks')->__('Save Block'));
        $this->_updateButton('delete', 'label', Mage::helper('ameasyblocks')->__('Delete Block'));
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('ameasyblocks_block')->getId()) {
            return Mage::helper('cms')->__("Edit Block '%s'", $this->htmlEscape(Mage::registry('ameasyblocks_block')->getAlias()));
        }
        else {
            return Mage::helper('cms')->__('New Block');
        }
    }
}