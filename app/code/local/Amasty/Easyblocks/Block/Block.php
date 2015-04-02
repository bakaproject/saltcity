<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Block extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        $this->setTemplate('ameasyblocks/block.phtml');
        return parent::_prepareLayout();
    }
    
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        
        // assign blocks
        $places = array();
        $places[] = $this->getPlace();
        
        if (Mage::registry('current_category') && !Mage::registry('current_product'))
        {
            $places[] = $this->getPlace() . '-category';
        }
        
        if ('checkout' == Mage::app()->getRequest()->getModuleName() && 'index' == Mage::app()->getRequest()->getActionName()
                && ('multishipping' == Mage::app()->getRequest()->getControllerName() || 'onepage' == Mage::app()->getRequest()->getControllerName())
                && empty($_POST))
        {
            $places[] = $this->getPlace() . '-precheckout';
        }

        $placeholders      = Mage::helper('ameasyblocks/display')->getPlaceholdersForPlaces($places);
        $placeholderBlocks = array();
        if ($placeholders)
        {
            foreach ($placeholders as $placeholder)
            {
                $block = $this->getLayout()->createBlock('ameasyblocks/block_component', 'block_placeholder_' . $placeholder->getId())->setPlaceholder($placeholder);
                $placeholderBlocks[] = $block;
            }
        }
        $this->setPlaceholderBlocks($placeholderBlocks);
        
        return $this;
    }
    
    protected function _toHtml()
    {
        if (!$this->getPlaceholderBlocks())
        {
            return '';
        }
        return parent::_toHtml();
    }
}