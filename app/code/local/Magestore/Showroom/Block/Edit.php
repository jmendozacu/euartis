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
 * Showroom Edit Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Edit extends Mage_Catalog_Block_Product_List
{
	protected $_showromproduct=null;
    public function _construct() {
        return parent::_construct();
    }
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		//$this->setTemplate('showroom/edit.phtml');
		return $this;
	}
	public function setShowroomProduct(){
		$showroom=Mage::helper('showroom')->getShowroomLogin();
		$productIds = array_keys($showroom->getProductIds());
		$this->_showromproduct=$productIds;
	}
	public function getShowroomProduct(){
		if ($this->_showromproduct==null)
			$this->setShowroomProduct();
		return $this->_showromproduct;
	}
	public function _getProductCollection(){
		$columncount=Mage::helper("showroom")->getShowroomConfig('number_column_product');
		$rows=Mage::helper("showroom")->getShowroomConfig('number_row_loadajax');
		$config=$rows*$columncount;
		if(true){
			$collection = Mage::helper('showroom')->_getProductCollection();		
			$collection->getSelect()->limit($config);
			$this->_productCollection = $collection;
		}
		return $this->_productCollection;
	}
	public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
    public function isFromShowroom($product){
		$productIds=$this->getShowroomProduct();
		if (in_array($product,$productIds)){
			return true;
		}
		return false;
    }
    public function isLoadMore(){
    	$columncount=Mage::helper("showroom")->getShowroomConfig('number_column_product');
		$rows=Mage::helper("showroom")->getShowroomConfig('number_row_loadajax');
		$config=$rows*$columncount;
    	$collection = Mage::helper('showroom')->_getProductCollection();
    	if($collection->getSize()>$config)
    		return true;
    	return false;
    }
}
