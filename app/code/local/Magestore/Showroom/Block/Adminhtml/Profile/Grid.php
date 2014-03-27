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
class Magestore_Showroom_Block_Adminhtml_Profile_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('profileGrid');
		$this->setDefaultSort('profile_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Profile_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('showroom/showroomprofile')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Profile_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('profile_id', array(
			'header'	=> Mage::helper('showroom')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'profile_id',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('showroom')->__('Profile Owner'),
			'align'	 =>'left',
			'index'	 => 'name',
			'width'	 =>'40%',
			'type'	 =>'text'
		));
		$this->addColumn('customer_email', array(
			'header'	=> Mage::helper('showroom')->__('Email'),
			'width'	 => '150px',
			'index'	 => 'customer_email',
			'width'	 =>'40%',
			'type'	 =>'text',
		));

		$this->addColumn('is_receive_mail', array(
			'header'	=> Mage::helper('showroom')->__('Configured to receive mail'),
			'width'	 => '150px',
			'index'	 => 'is_receive_mail',
			'width'	 =>'40%',
			'type'		=> 'options',
			'options'	=>array(
							0=>'Not receiving mail',
							1=>'Receive mail',
							)
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
		$this->setMassactionIdField('profile_id');
		$this->getMassactionBlock()->setFormFieldName('profile');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('showroom')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('showroom')->__('Are you sure?')
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