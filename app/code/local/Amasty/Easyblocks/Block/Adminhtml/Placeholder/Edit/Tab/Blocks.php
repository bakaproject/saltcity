<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Easyblocks
*/
class Amasty_Easyblocks_Block_Adminhtml_Placeholder_Edit_Tab_Blocks extends Mage_Adminhtml_Block_Widget_Grid implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('placeholder_blocks_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        /*if ($this->_getPlaceholder() && $this->_getPlaceholder()->getId()) {
            $this->setDefaultFilter(array('in_blocks' => 1));
        }*/
    }
    
    protected function _getPlaceholder()
    {
        return Mage::registry('ameasyblocks_placeholder');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ameasyblocks/block')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_blocks', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_blocks',
            'values'            => $this->_getSelectedBlocks(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));
        
        $this->addColumn('block_title', array(
            'header'    => Mage::helper('ameasyblocks')->__('Block Title'),
            'index'     => 'title',
        ));
        
        $this->addColumn('block_is_active', array(
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
        
        return parent::_prepareColumns();
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in blocks flag
        if ($column->getId() == 'in_blocks') {
            $productIds = $this->_getSelectedBlocks();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            } else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/blocksGrid', array('_current'=>true));
    }
    
    /**
     * Retrieve selected blocks
     *
     * @return array
     */
    protected function _getSelectedBlocks()
    {
        $blocks = $this->getSelectedBlocks();
        if (!is_array($blocks)) {
            $blocks = $this->getSelectedBlockBlocks();
        }
        return $blocks;
    }
    
    public function getSelectedBlockBlocks()
    {
        return $this->_getPlaceholder()->getBlocks();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ameasyblocks')->__('Active Blocks');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ameasyblocks')->__('Active Blocks');
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