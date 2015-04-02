<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tab_Config_Fullscreenprecheckout extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Easyblockss_Model_Block */
        $model = Mage::registry('ameasyblocks_placeholder');
        
        if (!Mage::app()->isSingleStoreMode()) {
            $model->setData('stores', explode(',', $model->getStoreIds()));
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('fullscreen_fieldset', array('legend'=>Mage::helper('ameasyblocks')->__('Fullscreen Pre-Checkout Configuration')));

        $yn = array(
            array(
                'value' => '1',
                'label' => $this->__('Yes'),
            ),
            array(
                'value' => '0',
                'label' => $this->__('No'),
            ),
        );
        
        $fieldset->addField('fullscreen_onlyonce', 'select', array(
            'name'      => 'fullscreen_onlyonce',
            'label'     => Mage::helper('ameasyblocks')->__('Display Once Per Visitor'),
            'title'     => Mage::helper('ameasyblocks')->__('Display Once Per Visitor'),
            'note'      => Mage::helper('ameasyblocks')->__('If set to "Yes", blockisement will appear at the first checkout for current user session.'),
            'values'    => $yn,
        ));
        
        $fieldset->addField('fullscreen_effect', 'text', array(
            'name'      => 'fullscreen_effect',
            'label'     => Mage::helper('ameasyblocks')->__('Effect Duration'),
            'title'     => Mage::helper('ameasyblocks')->__('Effect Duration'),
            'note'      => Mage::helper('ameasyblocks')->__('In seconds. Can be decimal. Set to 0 for instant display.'),
        ));
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Configuration: Fullscreen Pre-Checkout');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Configuration: Fullscreen Pre-Checkout');
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