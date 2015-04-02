<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Block_Component extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        
        // this is default template
        $this->setTemplate('ameasyblocks/block/component.phtml');
        
        return $this;
    }
    
    protected function _toHtml()
    {
        if (Mage::helper('ameasyblocks')->checkConditionType($this->getPlaceholder()->getPlace(), 'fullscreen'))
        {
            // checking if we should display fullscreen block only on the homepage
            if ($this->getPlaceholder()->getFullscreenOnlyhome())
            {
                $page = Mage::app()->getFrontController()->getRequest()->getRouteName();
                if (!($page == 'cms' && 'home' == Mage::getSingleton('cms/page')->getIdentifier()))
                {
                    return '';
                }
            }
        }
        
        if (Mage::helper('ameasyblocks')->checkConditionType($this->getPlaceholder()->getPlace(), 'fullscreen')
            || Mage::helper('ameasyblocks')->checkConditionType($this->getPlaceholder()->getPlace(), 'fullscreen-precheckout'))
        {
            // checking if we should display fullscreen block only once per visitor session
            if ($this->getPlaceholder()->getFullscreenOnlyonce())
            {
                $session   = Mage::getModel('core/session');
                $displayed = $session->getPlaceholdersDisplayed();
                if (is_array($displayed) && in_array($this->getPlaceholder()->getId(), $displayed))
                {
                    return '';
                }
                $displayed[] = $this->getPlaceholder()->getId();
                $session->setPlaceholdersDisplayed($displayed);
            }
        }
        
        // tracking block view
        if ($this->getPlaceholder()->getBlockId())
        {
            $block = Mage::getModel('ameasyblocks/block')->load($this->getPlaceholder()->getBlockId());
            if ($block->getId())
            {
                $block->setViews($block->getViews() + 1)->save();
            }
        }
        return parent::_toHtml();
    }
}