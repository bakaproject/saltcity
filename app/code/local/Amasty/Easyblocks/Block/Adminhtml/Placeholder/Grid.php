<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ameasyblocksPlaceholderGrid');
        $this->setDefaultSort('alias');
        $this->setDefaultDir('ASC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ameasyblocks/placeholder')->getCollection();
        /* @var $collection Amasty_Easyblocks_Model_Mysql4_Placeholder_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    => Mage::helper('ameasyblocks')->__('Placeholder Title'),
            'index'     => 'title',
        ));
        
        $this->addColumn('alias', array(
            'header'    => Mage::helper('ameasyblocks')->__('Alias'),
            'index'     => 'alias',
        ));
        
        $this->addColumn('place', array(
            'header'    => Mage::helper('ameasyblocks')->__('Position'),
            'sortable'  => true,
            'index'     => 'place',
            'type'      => 'options',
            'options'   => Mage::helper('ameasyblocks')->getPlaceholderPlaces(true),
        ));
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('ameasyblocks')->__('Enabled'),
            'sortable'  => true,
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('ameasyblocks')->__('Yes'),
                '0' => Mage::helper('ameasyblocks')->__('No'),
            ),
            'align' => 'center',
            'width' => '120px',
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('placeholder_id' => $row->getId()));
    }
}