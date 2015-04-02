<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Helper_Display extends Mage_Core_Helper_Abstract
{
    public function getPlaceholdersForPlaces($places)
    {
        $placeCodes = Mage::helper('ameasyblocks')->getPlaceholderPlacesCodes();
        $placeIds = array();
        // detecting place ids for place codes (place codes comes from layout)
        foreach ($places as $place)
        {
            if (isset($placeCodes[$place]))
            {
                $placeIds[] = $placeCodes[$place];
            }
        }
        if (!$placeIds)
        {
            return array();
        }

        $placeholders = array();
        
        // getting placeholders collection for place ids detected
        $placeholderCollection = Mage::getModel('ameasyblocks/placeholder')->getCollection();
        $placeholderCollection->addFieldToFilter('is_active', 1);
        $placeholderCollection->addFieldToFilter('place', array('in' => $placeIds));
        $placeholderCollection->load();
        if ($placeholderCollection->getSize())
        {
            foreach ($placeholderCollection as $placeholder)
            {
                if ($placeholder->shouldDisplay())
                {
                    $placeholder->populateContent();
                    if ($placeholder->getBlockContent())
                    {
                        $placeholders[] = $placeholder;
                    }
                }
            }
        }
     
        return $placeholders;
    }
}