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
 * Showroom Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Followers extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('listfollowgrid');
		$this->setDefaultSort('showroom_following_id');
		$this->setUseAjax(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareCollection(){
		$firstname = Mage::getResourceSingleton('customer/customer')->getAttribute('firstname');
        $lastname = Mage::getResourceSingleton('customer/customer')->getAttribute('lastname');
        $customerAttributeTable = Mage::getModel('core/resource')->getTableName('customer_entity_varchar');
		$collection = Mage::getModel('showroom/followers')->getCollection()->addFieldToFilter('main_table.showroom_id',$this->getRequest()->getParam('id'));
		$collection->getSelect()
                    ->join(array('ce'=>Mage::getModel('core/resource')->getTableName('customer_entity')),'ce.entity_id=main_table.customer_id',array('status_following'=>'main_table.status','ce.email','customer_id'=>'main_table.customer_id'))
                    ->joinLeft(array('firstname_table' => $customerAttributeTable), "firstname_table.entity_id = main_table.customer_id AND firstname_table.attribute_id = ".$firstname->getAttributeId(), 
        array('firstname' => 'value'))
            		->joinLeft(array('lastname_table' => $customerAttributeTable), "lastname_table.entity_id = main_table.customer_id AND lastname_table.attribute_id = ".$lastname->getAttributeId(), 
        array('lastname' => 'value','name' => 'CONCAT(firstname_table.value, " ", lastname_table.value)'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareColumns(){

		$this->addColumn('customer_id', array(
			'header'	=> Mage::helper('showroom')->__('ID'),
			'align'	 =>'right',
			'width'	 => '10px',
			'index'	 => 'customer_id',
		));
		
		$this->addColumn('name', array(
			'header'	=> Mage::helper('showroom')->__('Name'),
			'align'	 =>'left',
			'index'	 => 'name',
			'filter_index'	=> 'CONCAT(firstname_table.value, " ", lastname_table.value)',
			'width'	 =>'40%',
			'type'	 =>'text'
		));
		
		$this->addColumn('email', array(
			'header'	=> Mage::helper('showroom')->__('Email'),
			'index'	 => 'email',
			'filter_index'	 => 'email',
			'width'	 =>'40%',
			'type'	 =>'text',
		));


		$this->addColumn('status_following', array(
			'header'	=> Mage::helper('showroom')->__('Status'),
			'align'	 => 'left',
			'width'	 => '150px',
			'index'	 => 'status_following',
			'type'	 => 'options',
			'options'=> Mage::getSingleton('showroom/status')->getOptionArray(),
			'filter_index'  =>  'main_table.status'
		));
		return parent::_prepareColumns();
	}
	
	public function getRowUrl(){
		return;
	}

	/**
	 * get url for each row in grid
	 *
	 * @return string
	 */
	public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/listfollowgrid', array('_current'=>true));
    }
}