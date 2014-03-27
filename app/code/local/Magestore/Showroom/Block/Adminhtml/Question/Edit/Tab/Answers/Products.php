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
 * Showroom Answer Product Grid Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Block_Adminhtml_Question_Edit_Tab_Answers_Products extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('productGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}
	
	/**
	 * prepare collection for block to display
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareCollection(){		
		$answer_id = $this->getRequest()->getParam('answer_id');
		$answer = Mage::getModel('showroom/answer')->load($answer_id);
		$collection = $answer->getProductCollection($this->getStore()->getId());
		//Zend_Debug::dump($collection->getAllIds());die();
		if($collection){
			$collection->addAttributeToSelect('*');
			if ($storeId = $this->getStore()->getId())
				$collection->setStoreId($storeId);
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	/**
	 * prepare columns for this grid
	 *
	 * @return Magestore_Showroom_Block_Adminhtml_Showroom_Grid
	 */
	protected function _prepareColumns()
    {

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('showroom')->__('ID'),
            'sortable'  => true,
            'width'     => 60,
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('showroom')->__('Name'),
            'index'     => 'name'
        ));
		
		$this->addColumn('image', array(
            'header'    => Mage::helper('showroom')->__('Image'),
            'index'     => 'image',
			'renderer'	=>	'showroom/adminhtml_question_edit_tab_answers_renderer_products'
        ));

        $this->addColumn('type', array(
            'header'    => Mage::helper('catalog')->__('Type'),
            'width'     => 100,
            'index'     => 'type_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name', array(
            'header'    => Mage::helper('catalog')->__('Attrib. Set Name'),
            'width'     => 130,
            'index'     => 'attribute_set_id',
            'type'      => 'options',
            'options'   => $sets,
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'width'     => 90,
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility', array(
            'header'    => Mage::helper('catalog')->__('Visibility'),
            'width'     => 90,
            'index'     => 'visibility',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => 80,
            'index'     => 'sku'
        ));

        $this->addColumn('price', array(
            'header'        => Mage::helper('catalog')->__('Price'),
            'type'          => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price'
        ));

        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'width'             => 60,
            'editable'          => true,
            'edit_only'         => true
        ));

        return parent::_prepareColumns();
    }
	public function getRowUrl(){
		return;
	}
    public function getGridUrl()
    {
        return $this->getData('grid_url')
            ? $this->getData('grid_url')
            : $this->getUrl('*/*/index', array('_current'=>true,'answer_id'=>$this->getRequest()->getParam('answer_id')));
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
	 * get currrent store
	 *
	 * @return Mage_Core_Model_Store
	 */
	public function getStore(){
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return Mage::app()->getStore($storeId);
	}
	
	protected function _getSelectedProducts()
    {
        $products = $this->getProducts();
        return $products;
    }
}