<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Block_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        /* @var $model Amasty_Easyblockss_Model_Block */
        $model = Mage::registry('ameasyblocks_block');

        $form = new Varien_Data_Form();
        
        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('ameasyblocks')->__('Block Details'), 'class' => 'fieldset-wide'));

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', array(
                'name' => 'entity_id',
            ));
        }
        
        $yn = array(
            array(
                'value' => '1',
                'label' => $this->__('Yes'),
            ),
            array(
                'value' => '0',
                'label' => $this->__('No'),
            ),
        );

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => Mage::helper('ameasyblocks')->__('Title'),
            'title'     => Mage::helper('ameasyblocks')->__('Title'),
            'required'  => true,
        ));

        // we need all these replaces to direct wysiwyg to the cms url
        $mceConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $mceConfig->setData('files_browser_window_url', str_replace('ameasyblocks', 'admin', $mceConfig->getData('files_browser_window_url')));
        $mceConfig->setData('directives_url', str_replace('ameasyblocks', 'admin', $mceConfig->getData('directives_url')));
        $mceConfig->setData('directives_url_quoted', str_replace('ameasyblocks', 'admin', $mceConfig->getData('directives_url_quoted')));
        $mceConfig->setData('widget_window_url', str_replace('ameasyblocks', 'admin', $mceConfig->getData('widget_window_url')));
        $plugins = $mceConfig->getPlugins();
        if ($plugins)
        {
            foreach ($plugins as $i => $plugin)
            {
                if (isset($plugin['options']['url']))
                {
                    $plugins[$i]['options']['url'] = str_replace('ameasyblocks', 'admin', $plugin['options']['url']);
                }
                if (isset($plugin['options']['onclick']['subject']))
                {
                    $plugins[$i]['options']['onclick']['subject'] = str_replace('ameasyblocks', 'admin', $plugin['options']['onclick']['subject']);
                }
            }
        }
        $mceConfig->setPlugins($plugins);

        $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'label'     => Mage::helper('ameasyblocks')->__('Content'),
            'title'     => Mage::helper('ameasyblocks')->__('Content'),
            'style'     => 'height:36em',
            'required'  => true,
            'config'    => $mceConfig,
        ));
        
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('ameasyblocks')->__('From Date'),
            'title'  => Mage::helper('ameasyblocks')->__('From Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('ameasyblocks')->__('To Date'),
            'title'  => Mage::helper('ameasyblocks')->__('To Date'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso
        ));
        
        $fieldset->addField('is_active', 'select', array(
            'name'      => 'is_active',
            'label'     => Mage::helper('ameasyblocks')->__('Enabled'),
            'title'     => Mage::helper('ameasyblocks')->__('Enabled'),
            'values'    => $yn,
        ));

        if ('0000-00-00' == $model->getFrom())
        {
            $model->setFrom('');
        }
        if ('0000-00-00' == $model->getTo())
        {
            $model->setTo('');
        }
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Block Information');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Block Information');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }
}