<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ameasyblocks_placeholder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ameasyblocks')->__('Placeholder Information'));
    }
    
    protected function _beforeToHtml()
    {
        $place = Mage::helper('ameasyblocks')->getCurrentPlace();
        if (!$place || !in_array($place, array_keys(Mage::helper('ameasyblocks')->getPlaceholderPlaces())))
        {
            $this->addTab('form_section', array(
                'label'     => Mage::helper('ameasyblocks')->__('Settings'),
                'title'     => Mage::helper('ameasyblocks')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_settings')->toHtml(),
            ));
        } else 
        {
            $this->addTab('form_section', array(
                'label'     => Mage::helper('ameasyblocks')->__('General Information'),
                'title'     => Mage::helper('ameasyblocks')->__('General Information'),
                'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_main')->toHtml(),
            ));
            
            $this->addTab('blocks_section', array(
                'label'     => Mage::helper('ameasyblocks')->__('Active Blocks'),
                'title'     => Mage::helper('ameasyblocks')->__('Active Blocks'),
                'url'       => $this->getUrl('*/*/blocksGrid', array('_current' => true)),
                'class'     => 'ajax',
            ));
            
            
            
            /**
            * @see Amasty_Easyblocks_Model_Placeholder::shouldDisplay for conditions validation
            */
            
            // tab for category-related positions
            if (Mage::helper('ameasyblocks')->checkConditionType($place, 'category'))
            {
                $this->addTab('category_section', array(
                    'label'     => Mage::helper('ameasyblocks')->__('Configuration: Categories'),
                    'title'     => Mage::helper('ameasyblocks')->__('Configuration: Categories'),
                    'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_config_category')->toHtml(),
                ));
            }
            
            // tab for fullscreen configuration
            if (Mage::helper('ameasyblocks')->checkConditionType($place, 'fullscreen'))
            {
                $this->addTab('category_section', array(
                    'label'     => Mage::helper('ameasyblocks')->__('Configuration: Fullscreen'),
                    'title'     => Mage::helper('ameasyblocks')->__('Configuration: Fullscreen'),
                    'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_config_fullscreen')->toHtml(),
                ));
            }
            
            // tab for fullscreen-precheckout configuration
            if (Mage::helper('ameasyblocks')->checkConditionType($place, 'fullscreen-precheckout'))
            {
                $this->addTab('category_section', array(
                    'label'     => Mage::helper('ameasyblocks')->__('Configuration: Fullscreen Pre-Checkout'),
                    'title'     => Mage::helper('ameasyblocks')->__('Configuration: Fullscreen Pre-Checkout'),
                    'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_placeholder_edit_tab_config_fullscreenprecheckout')->toHtml(),
                ));
            }
        }
        return parent::_beforeToHtml();
    }
}