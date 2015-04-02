<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Model_Mysql4_Block extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ameasyblocks/block', 'entity_id');
    }
    
    public function clearPlacedBlocks($blockId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = 'DELETE FROM `' . $this->getTable('ameasyblocks/block_place') . '` WHERE `block_id` = "' . $blockId . '" ' ;
        $connection->query($sql);
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getFromDate()) {
            $object->setFromDate(new Zend_Db_Expr('NULL'));
        }
        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        parent::_beforeSave($object);
    }
}