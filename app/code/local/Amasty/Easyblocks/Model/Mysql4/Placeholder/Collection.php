<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Model_Mysql4_Placeholder_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ameasyblocks/placeholder');
    }
}