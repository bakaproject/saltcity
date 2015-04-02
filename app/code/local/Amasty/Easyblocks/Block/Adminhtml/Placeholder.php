<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'ameasyblocks';
        $this->_controller = 'adminhtml_placeholder';
        $this->_headerText = Mage::helper('ameasyblocks')->__('Placeholders');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('ameasyblocks')->__('Add New Placeholder'));
    }
}