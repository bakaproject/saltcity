<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Model_Placeholder extends Mage_Core_Model_Abstract
{
    const LOGIC_LOAD_RANDOM = 1;
    
    protected function _construct()
    {
        $this->_init('ameasyblocks/placeholder');
    }
    
    protected function _afterSave()
    {
        parent::_afterSave();
        
        /**
        * Saving related blocks, if any selected
        */
        if ($this->getData('should_save_selected_blocks'))
        {
            $this->getResource()->clearBlocks($this->getId());
            if ($this->getSelectedBlocks())
            {
                $blockIds = explode('&', $this->getSelectedBlocks());
                if (!is_array($blockIds)) {
                    $blockIds = array($blockIds);
                }
                $this->getResource()->assignBlocks($this->getId(), $blockIds);
            }
        }
        
        return $this;
    }
    
    public function getBlocks()
    {
        return $this->getResource()->getBlocks($this->getId());
    }
    
    public function populateContent()
    {
        $blockId = $this->getResource()->getBlockId($this->getId(), self::LOGIC_LOAD_RANDOM);
        if ($blockId)
        {
            $block = Mage::getModel('ameasyblocks/block')->load($blockId);
            if ($block->getId())
            {
                $processor = Mage::helper('cms')->getPageTemplateProcessor();
                $html = $processor->filter($block->getContent());
                $this->setBlockContent($html);
                $this->setBlockId($blockId);
            }
        }
        return $this;
    }
    
    /**
    * Will check if we should display this placeholder on the current page.
    */
    public function shouldDisplay()
    {
        // first checking store view
        $storeIdsAllowed = explode(',', $this->getStoreIds());
        if ($storeIdsAllowed)
        {
            if (!in_array(0, $storeIdsAllowed)) // 0 means All Store Views, so if 0, we should allow
            {
                $currentStoreId = Mage::app()->getStore()->getId();
                if (!in_array($currentStoreId, $storeIdsAllowed))
                {
                    return false;
                }
            }
        }
        
        // checking category condition for placeholders that are assigned to category
        if (Mage::helper('ameasyblocks')->checkConditionType($this->getPlace(), 'category'))
        {
            if (Mage::registry('current_category') && !Mage::registry('curent_product'))
            {
                $categoriesAllowed = explode(',', $this->getCategoryIds());
                if (in_array(Mage::registry('current_category')->getId(), $categoriesAllowed))
                {
                    return true;
                }
            }
            // we are not on a category page, or category does not match
            return false;
        }
        
        return true;
    }
}
