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

class Magestore_Showroom_Block_Componentedit extends Mage_Core_Block_Template
{
	protected $_showromproduct=null;
	protected $_isloadmore=true;
    public function _construct() {
        return parent::_construct();
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
	public function getProductShowroomTemp(){
		$products= $this->getRequest()->getParam('products');
		$arr_pre=explode(",",$products);
		$array=array();
		$count=count($arr_pre);$i=1;
		for($i;$i<$count;) {
			$array[$arr_pre[$i]]=$arr_pre[$i+1];
			$i=$i+2;
		}
		$nins_temp=array();
		$ins_temp=array();
		foreach ($array as $key => $value) {
			if($value==-1){
				$nins_temp[]=$key;
			}
			if($value==1){
				$ins_temp[]=$key;
			}
			$i=$i+2;
		}
		$productIds = $this->getShowroomProduct();
		if(count($ins_temp)){
			$productIds = array_merge($productIds,$ins_temp);
		}
		if(count($nins_temp)){
			$productIds = array_diff($productIds,$nins_temp);
		}
		return $productIds;
	}
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('showroom/componentedit.phtml');
		return $this;
	}
	public function getCollection(){
		$this->setShowroomProduct();
		$p = $this->getRequest()->getParam('page');
		$columncount=Mage::helper("showroom")->getShowroomConfig('number_column_product');
		$rows=Mage::helper("showroom")->getShowroomConfig('number_row_loadajax');
		$config=$rows*$columncount;
		$productIds=$this->getProductShowroomTemp();
		
		$view = $this->getRequest()->getParam('view');
		if($view=='in'){
			$collection =Mage::getResourceModel('catalog/product_collection')
											->setStoreId($this->getStoreId())
											->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
								
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($collection);
			$collection->addFieldToFilter('entity_id',array('in'=>$productIds));
		}elseif($view=='nin') {
			$productCollection = Mage::getModel('catalog/product')->getCollection();
				$productCollection->addFieldToFilter('entity_id',array('in'=>$productIds));
			$collection = Mage::helper('showroom')->_getProductCollection();
			if($productCollection->getSize())
				$collection->addFieldToFilter('entity_id',array('nin'=>$productCollection->getAllIds()));	
		}else {
			$collection = Mage::helper('showroom')->_getProductCollection();	
		}
		$p_max=$collection->getSize()/$config;
		if(($p)>=$p_max+1){
			$collection=null;
			$this->_isloadmore=false;
		}else{
			$collection->setPage($p,$config);
		}
		return $collection;
	}
	public function isFromShowroom($product){
		$productIds=$this->getProductShowroomTemp();
		if (in_array($product,$productIds)){
			return true;
		}
		return false;
    }
    public function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }
    public function isLoadMore(){
    	return $this->_isloadmore;
    }
    public function setIsLoadMore($var){
    	$this->_isloadmore=$var;
    }
}