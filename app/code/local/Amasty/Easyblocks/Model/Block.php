<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Model_Block extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ameasyblocks/block');
    }
    
    protected function _beforeSave()
    {
        if (!$this->getId())
        {
            $this->setCreatedAt(date('Y-m-d H:i:s', Mage::app()->getLocale()->date()->get()));
        }
        $this->setUpdatedAt(date('Y-m-d H:i:s', Mage::app()->getLocale()->date()->get()));
        
        return parent::_beforeSave();
    }
    
    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $this->getResource()->clearPlacedBlocks($this->getId());
        return $this;
    }
}
