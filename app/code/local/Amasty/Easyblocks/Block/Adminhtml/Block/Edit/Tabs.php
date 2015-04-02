<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Block_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ameasyblocks_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ameasyblocks')->__('Block Information'));
    }
    
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('ameasyblocks')->__('General Information'),
            'title'     => Mage::helper('ameasyblocks')->__('General Information'),
            'content'   => $this->getLayout()->createBlock('ameasyblocks/adminhtml_block_edit_tab_main')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}