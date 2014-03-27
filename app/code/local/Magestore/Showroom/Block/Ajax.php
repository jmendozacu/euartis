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
 * Showroom Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Ajax extends Magestore_Showroom_Block_Catalog_Product_List

{
	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Showroom_Block_Showroom
	 */
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

	protected function _getProductCollection()
    {
		die('abc');
        if (is_null($this->_productCollection)) {
            
			$id  = $this->getRequest()->getParam('id');
            $showroom=Mage::helper('showroom')->getShowroomCurrent($id);

            $cat=$this->getRequest()->getParam('cat');
            if(!$cat)
                $cat=Mage::app()->getStore()->getRootCategoryId();
			if ($cat){
                $layer = $this->getLayer();
            	$productIds = Mage::getModel('catalog/category')->load($cat)->setIsAnchor(1)->getProductCollection()->getAllIds();
                $this->_productCollection = $layer->getProductCollection()
                        ->addFieldToFilter('entity_id',array('in'=>$productIds));

        	}else{
				$this->_productCollection = Mage::getResourceModel('catalog/product_collection')
											->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes());
	        }

            if($showroom->getId())
            $this->_productCollection->getSelect()
                    ->join(array('sp'=>Mage::getModel('core/resource')->getTableName('showroom_product')),'e.entity_id=sp.product_id AND sp.type !=0 AND sp.showroom_id ='.$showroom->getShowroomId(),array('point'=>'sp.point'));

            $this->_productCollection->addCategoryFilter(Mage::getModel('catalog/category')->load($cat)->setIsAnchor(1));

     	}
		//Zend_Debug::dump($this->_productCollection->toArray());
        return $this->_productCollection;
    }
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
    public function getShowroomCategorys(){
    	$store_id=$this->getStoreId();
        $category_str = Mage::getModel('catalog/category')
                        ->load(Mage::app()->getStore($store_id)->getRootCategoryId())
                        ->getAllChildren();
        $category_arr=explode(",",$category_str);
        $config_cat=explode(",",Mage::helper('showroom')->getShowroomConfig('category'));
        $categorys=Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('entity_id',array('in'=>$config_cat))
            ->addFieldToFilter('entity_id',array('in'=>$category_arr))
            ->addAttributeToSelect('name');
         return $categorys;
    }
}