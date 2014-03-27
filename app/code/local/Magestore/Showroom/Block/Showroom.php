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

class Magestore_Showroom_Block_Showroom extends Mage_Catalog_Block_Product_List

{
	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Showroom_Block_Showroom
	 */
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}
	
	protected function _beforeToHtml()
	{
		$id  = $this->getRequest()->getParam('id');
		$showroom=Mage::helper('showroom')->getShowroomCurrent($id);
		$toolbar = $this->getToolbarBlock();

		// called prepare sortable parameters
		$collection = $this->_getProductCollection();
		//Zend_Debug::dump($toolbar->getAvailableOrders());
		// use sortable parameters
		if ($orders = $toolbar->getAvailableOrders()) {
			unset($orders['position']);
			$orders = array_merge(array(
				'rele' => $this->__('Relevance')
			), $orders);
			$toolbar->setAvailableOrders($orders);
		}
		if ($sort = $this->getSortBy()) {
			$toolbar->setDefaultOrder($sort);
		}
		if ($modes = $this->getModes()) {
			$toolbar->setModes($modes);
		}

		// set collection to tollbar and apply sort
		$toolbar->setCollection($collection);

		$this->setChild('toolbar', $toolbar);
		Mage::dispatchEvent('catalog_block_product_list_collection', array(
			'collection'=>$this->_getProductCollection(),
		));
		$productIds = $showroom->getProductIds();
		
		$order = $this->getRequest()->getParam('order');
		$dir = $this->getRequest()->getParam('dir');
		$page = $this->getRequest()->getParam('p',1);
		$limit = $this->getRequest()->getParam('limit',9);
		
		if($dir == 'asc'){
			asort($productIds,SORT_NUMERIC);
		}
		if($order == 'rele'){			
			$productIds = $this->getProductIdsByPage($productIds,$page,$limit);
			$this->setProductIds($productIds);
		}
		//Zend_Debug::dump($productIds);
		$this->_getProductCollection()->load();
		Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
		return parent::_beforeToHtml();
	}
	
	public function getProductIdsByPage($productIds,$page,$limit){
		$start = ($page-1) * $limit + 1;
		$i = 0;
		$result =array();
		
		foreach($productIds as $productId=>$point){
			$i++;
			if($i < $start) continue;
			if($i > (($page-1) * $limit + $limit)) break;
			$result[$productId] = $point;
			
		}
		return $result;
	}
	
	protected function _getProductCollection()
    {
		if (is_null($this->_productCollection)) {
            $layer = Mage::getSingleton('catalog/layer');
			$id  = $this->getRequest()->getParam('id');
            $showroom=Mage::helper('showroom')->getShowroomCurrent($id);

            $cat=$this->getRequest()->getParam('cat');
			if ($cat){
            	$productIds = Mage::getModel('catalog/category')->load($cat)->getProductCollection()->getAllIds();
                $this->_productCollection = $layer->getProductCollection()
                        ->addFieldToFilter('entity_id',array('in'=>$productIds));

        	}else{
        		$cat=Mage::app()->getStore()->getRootCategoryId();
				$productIds = Mage::getModel('catalog/category')->load($cat)->setIsAnchor(1)->getProductCollection()->getAllIds();
                $this->_productCollection = $layer->getProductCollection()
                        ->addFieldToFilter('entity_id',array('in'=>$productIds));
                $this->_productCollection->addCategoryFilter(Mage::getModel('catalog/category')->load($cat)->setIsAnchor(1));
	        }

            if($showroom->getId())
				$this->_productCollection->addFieldToFilter('entity_id',array('in'=>$showroom->getProductCollection()->getAllIds()));
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($this->_productCollection);

     	}
        return $this->_productCollection;
	}
	protected function _getProductCollection2()
    {
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
            ->addAttributeToSelect('*');
         return $categorys;
    }
}