
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
class Magestore_Showroom_Block_Adminhtml_Showroom_Edit_Tab_Preferredproducts extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('preferredproductsgrid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
    	$showroom= $this->getShowroom();
        /*$collection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect('*');
        $collection->getSelect()
                    ->join(array('sp'=>Mage::getModel('core/resource')->getTableName('showroom_product')),'e.entity_id=sp.product_id AND sp.type !=0 AND sp.showroom_id ='.$showroom->getShowroomId(),array('point'));
		*/
		$collection = $showroom->getProductCollection()->addAttributeToSelect('*');
		//Zend_Debug::dump($collection->getAllIds());
        $this->setCollection($collection);
		
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('showroom')->__('ID'),
            'width'     => 60,
            'index'     => 'entity_id'
        ));

        /*$this->addColumn('point', array(
            'header'    => Mage::helper('showroom')->__('Point'),
            'width'     => 60,
            'index'     => 'point',
            'type'      => 'number',
            'filter_index'  =>  'sp.showroom_id'
        ));*/

        $this->addColumn('name_product', array(
            'header'    => Mage::helper('showroom')->__('Name'),
            'index'     => 'name'
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

        $this->addColumn('status_product', array(
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


        return parent::_prepareColumns();
    }
	
	public function getRowUrl(){
		return;
	}
	
    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/preferredproductsgrid', array('_current'=>true));
    }
    public function getShowroom()
	{
		return Mage::getModel('showroom/showroom')
			->load($this->getRequest()->getParam('id'))
			;		
	}
}