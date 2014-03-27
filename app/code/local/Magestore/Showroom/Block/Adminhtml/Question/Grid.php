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
class Magestore_Showroom_Block_Adminhtml_Question_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('questionGrid');
		$this->setDefaultSort('question_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('showroom/question')->getCollection();
		if ($storeId = $this->getStore()->getId())
			$collection->setStoreId($storeId);
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('question_id', array(
			'header'	=> Mage::helper('showroom')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'question_id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('showroom')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
		));

		$this->addColumn('type', array(
			'header'	=> Mage::helper('showroom')->__('Is Required'),
			'width'	 => '150px',
			'type'	 => 'options',
			'index'	 => 'type',
			'options'	=> Mage::helper('showroom')->getQuestionTypeOptionArray()
		));
		$this->addColumn('sort', array(
			'header'	=> Mage::helper('showroom')->__('Sort'),
			'align'	 =>'left',
			'index'	 => 'sort',
		));
		$this->addColumn('status', array(
			'header'	=> Mage::helper('showroom')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Enabled',
				2 => 'Disabled',
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
		$this->setMassactionIdField('question_id');
		$this->getMassactionBlock()->setFormFieldName('question');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('showroom')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('showroom')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('showroom/status')->getOptionArray();

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
	
	/**
	 * get currrent store
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getStore(){
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
}