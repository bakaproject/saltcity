<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."', 'place')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }
    
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('catalog')->__('Create Placeholder Settings')));

        $fieldset->addField('place', 'select', array(
            'label' => Mage::helper('catalog')->__('Placeholder Position'),
            'title' => Mage::helper('catalog')->__('Placeholder Position'),
            'name'  => 'type',
            'value' => '',
            'values'=> Mage::helper('ameasyblocks')->getPlaceholderPlaces(true),
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'place'     => '{{place}}',
        ));
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Settings');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Settings');
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