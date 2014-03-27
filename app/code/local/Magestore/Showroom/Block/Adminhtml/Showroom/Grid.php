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
class Magestore_Showroom_Block_Adminhtml_Showroom_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('showroomGrid');
		$this->setDefaultSort('showroom_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('showroom/showroom')->getCollection();
		$collection->getSelect()
                    ->join(array('sp'=>Mage::getModel('core/resource')->getTableName('showroom_profile')),'main_table.profile_id=sp.profile_id',array('name','customer_email'));
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
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('showroom')->__('Showroom Owner'),
			'align'	 =>'left',
			'index'	 => 'name',
			'width'	 =>'20%',
			'type'	 =>'text'
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
			'width'	 =>'20%',
			'type'		=> 'options',
			'options'	=> $array,
		));

		$this->addColumn('customer_email', array(
			'header'	=> Mage::helper('showroom')->__('Email'),
			'width'	 => '150px',
			'index'	 => 'customer_email',
			'width'	 =>'20%',
			'type'	 =>'text',
		));

		$this->addColumn('type', array(
			'header'	=> Mage::helper('showroom')->__('Type'),
			'width'	 => '150px',
			'index'	 => 'type',
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
			'type'	 =>'number',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('showroom')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
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
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('showroom')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('showroom')->__('XML'));

		return parent::_prepareColumns();
	}
	
	/**
	 * prepare mass action for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('showroom_id');
		$this->getMassactionBlock()->setFormFieldName('showroom');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('showroom')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('showroom')->__('Are you sure?')
		));

		$statuses = array(
				1 => 'Processing',
				2 => 'Active',
				3 =>'Disable'
			);

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('showroom')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('showroom')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}
	
	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}