<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId   = 'placeholder_id';
        $this->_blockGroup = 'ameasyblocks';
        $this->_controller = 'adminhtml_placeholder';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('ameasyblocks')->__('Save Placeholder'));
        $this->_updateButton('delete', 'label', Mage::helper('ameasyblocks')->__('Delete Placeholder'));
        
        $place = Mage::helper('ameasyblocks')->getCurrentPlace();
        if (!$place || !in_array($place, array_keys(Mage::helper('ameasyblocks')->getPlaceholderPlaces()))) {
            $this->_removeButton('save');
        }
        
        $this->_formScripts[] = '
            var templateSyntax = /(^|.|\r|\n)({{(\w+)}})/;
            function setSettings(urlTemplate, placeElement) {
                var template = new Template(urlTemplate, templateSyntax);
                setLocation(template.evaluate({place:$F(placeElement)}));
            }
            function saveAndContinueEdit() {
                editForm.submit($(\'edit_form\').action+\'back/edit/\');
            }
        ';
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('ameasyblocks_placeholder')->getId()) {
            return Mage::helper('ameasyblocks')->__("Edit Placeholder '%s'", $this->htmlEscape(Mage::registry('ameasyblocks_placeholder')->getTitle()));
        }
        else {
            return Mage::helper('ameasyblocks')->__('New Placeholder');
        }
    }
}