<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getCurrentPlace()
    {
        $place = Mage::app()->getRequest()->getParam('place', 0);
        $model = Mage::registry('ameasyblocks_placeholder');
        if ($model && $model->getPlace())
        {
            $place = $model->getPlace();
        }
        return $place;
    }
    
    public function checkConditionType($place, $type)
    {
        return (   
                      in_array($place, array(1, 2, 3, 4, 16, 17)) && 'category'               == $type
                   || in_array($place, array(5))                  && 'fullscreen'             == $type
                   || in_array($place, array(10))                 && 'fullscreen-precheckout' == $type
               );
    }

    /**
    * @see Amasty_Easyblocks_Model_Placeholder::shouldDisplay for conditions validation
    */
    public function getPlaceholderPlaces($asAssociative = false)
    {
        $places = array(
            1   => array(
                'code'  => 'right-top-category',
                'title' => $this->__('Top of the right column on category page'),
            ),
            2   => array(
                'code'  => 'left-top-category',
                'title' => $this->__('Top of the left column on category page'),
            ),
            3   => array(
                'code'  => 'right-bottom-category',
                'title' => $this->__('Bottom of the right column on category page'),
            ),
            4   => array(
                'code'  => 'left-bottom-category',
                'title' => $this->__('Bottom of the left column on category page'),
            ),
            6   => array(
                'code'  => 'right-top',
                'title' => $this->__('Top of the right column'),
            ),
            7   => array(
                'code'  => 'left-top',
                'title' => $this->__('Top of the left column'),
            ),
            8   => array(
                'code'  => 'right-bottom',
                'title' => $this->__('Bottom of the right column'),
            ),
            9   => array(
                'code'  => 'left-bottom',
                'title' => $this->__('Bottom of the left column'),
            ),
            11   => array(
                'code'  => 'content',
                'title' => $this->__('Above the main content block'),
            ),
            12   => array(
                'code'  => 'menu-bottom',
                'title' => $this->__('Under the main menu'),
            ),
            13   => array(
                'code'  => 'menu-top',
                'title' => $this->__('Above the main menu'),
            ),
            14   => array(
                'code'  => 'footer-bottom',
                'title' => $this->__('Under the footer links'),
            ),
            15   => array(
                'code'  => 'footer-top',
                'title' => $this->__('Above the footer links'),
            ),
            16   => array(
                'code'  => 'above-list-category',
                'title' => $this->__('Above the product list on category page'),
            ),
            17   => array(
                'code'  => 'under-list-category',
                'title' => $this->__('Under the product list on category page'),
            ),
        );
        if ($asAssociative)
        {
            $placesAssoc = array();
            foreach ($places as $id => $data)
            {
                $placesAssoc[$id]   = $data['title'];
            }
            asort($placesAssoc, SORT_LOCALE_STRING);
            return $placesAssoc;
        }
        return $places;
    }
    
    public function getPlaceholderPlacesCodes()
    {
        $places = $this->getPlaceholderPlaces();
        $codes = array();
        foreach ($places as $id => $place)
        {
            $codes[$place['code']] = $id;
        }
        return $codes;
    }
}