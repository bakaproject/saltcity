<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Easyblockss_Model_Block */
        $model = Mage::registry('ameasyblocks_placeholder');
        
        if (!Mage::app()->isSingleStoreMode()) {
            $model->setData('stores', explode(',', $model->getStoreIds()));
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('placeholder_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('ameasyblocks')->__('Placeholder Details')));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }
        
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

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('ameasyblocks')->__('Title'),
            'title'     => Mage::helper('ameasyblocks')->__('Title'),
            'required'  => true,
        ));
        
        $fieldset->addField('alias', 'text', array(
            'name'      => 'alias',
            'label'     => Mage::helper('ameasyblocks')->__('Alias'),
            'title'     => Mage::helper('ameasyblocks')->__('Alias'),
            'note'      => Mage::helper('ameasyblocks')->__('Will be used for css style postfix. Please avoid spaces.'),
            'required'  => true,
        ));
        
        $fieldset->addField('place', 'select', array(
            'name'      => 'place',
            'label'     => Mage::helper('ameasyblocks')->__('Position'),
            'title'     => Mage::helper('ameasyblocks')->__('Position'),
            'values'    => Mage::helper('ameasyblocks')->getPlaceholderPlaces(true),
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('stores', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('ameasyblocks')->__('Store View'),
                'title'     => Mage::helper('ameasyblocks')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        else {
            $fieldset->addField('stores', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }
        
        $fieldset->addField('width', 'text', array(
            'name'      => 'width',
            'label'     => Mage::helper('ameasyblocks')->__('Width'),
            'title'     => Mage::helper('ameasyblocks')->__('Width'),
            'note'      => Mage::helper('ameasyblocks')->__('px or % required, for example: "200px". Leave empty if width is not required or specified within block block content. If set, placeholder will be set to this specific width. Adds "width: xxx" to css style.'),
        ));
        
        $fieldset->addField('height', 'text', array(
            'name'      => 'height',
            'label'     => Mage::helper('ameasyblocks')->__('Height'),
            'title'     => Mage::helper('ameasyblocks')->__('Height'),
            'note'      => Mage::helper('ameasyblocks')->__('px or % required, for example: "200px". Leave empty if height is not required or specified within block block content. If set, placeholder will be set to this specific height. Adds "height: xxx" to css style.'),
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'name'      => 'is_active',
            'label'     => Mage::helper('ameasyblocks')->__('Enabled'),
            'title'     => Mage::helper('ameasyblocks')->__('Enabled'),
            'values'    => $yn,
        ));

        if (!$model->getPlace()) {
            $model->setPlace(Mage::helper('ameasyblocks')->getCurrentPlace());
        }
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Placeholder Information');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Placeholder Information');
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
