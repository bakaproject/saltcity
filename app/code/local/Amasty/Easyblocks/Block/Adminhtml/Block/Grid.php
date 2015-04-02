<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ameasyblocksBlockGrid');
        $this->setDefaultSort('alias');
        $this->setDefaultDir('ASC');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ameasyblocks/block')->getCollection();
        /* @var $collection Amasty_Easyblocks_Model_Mysql4_Block_Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    => Mage::helper('ameasyblocks')->__('Block Title'),
            'index'     => 'title',
        ));
        
        $this->addColumn('from_date', array(
            'header'    => Mage::helper('ameasyblocks')->__('From Date'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('ameasyblocks')->__('To Date'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));
        
        $this->addColumn('is_active', array(
            'header'    =>Mage::helper('ameasyblocks')->__('Enabled'),
            'sortable'  =>true,
            'index'     =>'is_active',
            'type'      => 'options',
            'options' => array(
                '1' => Mage::helper('ameasyblocks')->__('Yes'),
                '0' => Mage::helper('ameasyblocks')->__('No'),
            ),
            'align' => 'center',
            'width' => '120px',
        ));
        
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('ameasyblocks')->__('Last Modified'),
            'index'     => 'updated_at',
        ));
        
        $this->addColumn('views', array(
            'header'    => Mage::helper('ameasyblocks')->__('Number Of Views'),
            'index'     => 'views',
        ));

        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('block_id' => $row->getId()));
    }
}