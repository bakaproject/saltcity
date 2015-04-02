<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Model_Mysql4_Placeholder extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('ameasyblocks/placeholder', 'entity_id');
    }
    
    /**
    * Remove all assigned blocks
    * 
    * @param integer $placeholderId
    */
    public function clearBlocks($placeholderId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $sql = 'DELETE FROM `' . $this->getTable('ameasyblocks/block_place') . '` WHERE `placeholder_id` = "' . $placeholderId . '" ' ;
        $connection->query($sql);
    }
    
    /**
    * Assign array of blocks to specified placeholder
    * 
    * @param integer $placeholderId
    * @param array $blockIds
    */
    public function assignBlocks($placeholderId, $blockIds)
    {
        foreach ($blockIds as $blockId) {
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            $sql = ' INSERT INTO `' . $this->getTable('ameasyblocks/block_place') . '` (`block_id`, `placeholder_id`) VALUES ("' . $blockId . '", "' . $placeholderId . '") ' ;
            $connection->query($sql);
        }
    }
    
    public function getBlocks($placeholderId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
        $sql = ' SELECT block_id FROM `' . $this->getTable('ameasyblocks/block_place') . '` AS p ' . 
               ' INNER JOIN ' . $this->getTable('ameasyblocks/block') . ' AS b ON (p.block_id = b.entity_id) ' .
               ' WHERE `placeholder_id` = "' . $placeholderId . '" ' . 
               ' AND (  ( b.from_date <= "' . date('Y-m-d') . '" AND b.to_date >= "' . date('Y-m-d') . '" ) OR  ( b.from_date IS NULL AND b.to_date IS NULL )  ) ' ;
        $blockIds = $connection->fetchCol($sql);
        return $blockIds;
    }
    
    public function getBlockId($placeholderId, $loadLogic = Amasty_Easyblocks_Model_Placeholder::LOGIC_LOAD_RANDOM)
    {
        $blocks  = $this->getBlocks($placeholderId);
        if (empty($blocks))
        {
            return 0;
        }
        switch ($loadLogic)
        {
            case Amasty_Easyblocks_Model_Placeholder::LOGIC_LOAD_RANDOM:
                $blockId = $blocks[rand(0, count($blocks) - 1)];
            break;
            default:
                $blockId = $blocks[0];
            break;
        }
        return $blockId;
    }
}