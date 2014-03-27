<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Showroom Grid Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Profile_Edit_Tab_Showrooms extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('showroomsgrid');
		$this->setDefaultSort('showroom_id');
        $this->setUseAjax(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareCollection(){
		$id=$this->getRequest()->getParam('id');
		$collection = Mage::getModel('showroom/showroom')->getCollection()
					->addFieldToFilter('profile_id',$id);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('showroom_id', array(
			'header'	=> Mage::helper('showroom')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'showroom_id',
			'filter_index'  =>  'showroom_id'
		));

		$array=array();
		$group_stores = Mage::getModel('core/store_group')->getCollection();
		foreach ($group_stores as $group_store) {
			$array[$group_store->getGroupId()]=$group_store->getName();
		}
		$this->addColumn('store_group_id', array(
			'header'	=> Mage::helper('showroom')->__('Store group'),
			'width'	 => '150px',
			'index'	 => 'store_group_id',
			'filter_index'  =>  'store_group_id',
			'width'	 =>'20%',
			'type'		=> 'options',
			'options'	=> $array,
		));

		$this->addColumn('type', array(
			'header'	=> Mage::helper('showroom')->__('Type'),
			'width'	 => '150px',
			'index'	 => 'type',
			'filter_index'  =>  'type',
			'type'		=> 'options',
			'options'	 => array(
				0 => 'Personal',
				1 => 'Stylist'
			),
		));

		$this->addColumn('is_private', array(
			'header'	=> Mage::helper('showroom')->__('Is Private'),
			'width'	 => '150px',
			'index'	 => 'is_private',
			'filter_index'  =>  'is_private',
			'type'		=> 'options',
			'options'	 => array(
				0 =>'No',
				1 => 'Yes'
			),
		));

		$this->addColumn('follow_number', array(
			'header'	=> Mage::helper('showroom')->__('Follows'),
			'width'	 => '150px',
			'index'	 => 'follow_number',
			'filter_index'  =>  'follow_number',
			'type'	 =>'number',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('showroom')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'filter_index'  =>  'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Processing',
				2 => 'Active',
				3=>'Disable'
			),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('showroom')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('showroom')->__('Edit'),
						'url'		=> array('base'=> 'showroomadmin/adminhtml_showroom/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));
		return parent::_prepareColumns();
	}
	
	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getRowUrl($row){
		return $this->getUrl('showroomadmin/adminhtml_showroom/edit', array('id' => $row->getId()));
	}
	public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/showroomsgrid', array('_current'=>true));
    }
}