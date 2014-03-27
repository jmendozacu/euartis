
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
 * @category    Magestore
 * @package     Magestore_Showroom
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

 /**
 * Showroom Block
 * 
 * @category    Magestore
 * @package     Magestore_Showroom
 * @author      Magestore Developer
 */

class Magestore_Showroom_Block_Overview extends Mage_Catalog_Block_Product_List
{
    static $i=0;
    static $productOnLoaded=array();
    public function _construct() {
        self::$productOnLoaded[]='0';
        return parent::_construct();
    }
    public function set($i){
        self::$i=$i;
    }
    public function get(){
        return self::$i;
    }
    public function getCount(){
        return count(self::$productOnLoaded);
    }
    public function _productCollectionCat($category){
        $limit=Mage::helper('showroom')->getShowroomConfig('limit_product_display');
        $id  = $this->getRequest()->getParam('id');

        $showroom=Mage::helper('showroom')->getShowroomCurrent($id);
        $category->setStoreId($this->getStoreId());
        $_productCollectionCat = $category->getProductCollection()
                                ->addAttributeToSelect('*')
                                ->addFieldToFilter('entity_id',array('nin'=>self::$productOnLoaded))
								;
        if($showroom->getId()){
			$productIds = $showroom->getProductIds();
			//Zend_Debug::dump($showroom->getProductCollection()->getAllIds());
            $_productCollectionCat->addFieldToFilter('entity_id',array('in'=>array_keys($productIds)));
			$_productCollectionCat->getSelect()->limit($limit);
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollectionCat);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollectionCat);
		}
        foreach($_productCollectionCat as $product) {
            self::$productOnLoaded[]=$product->getId();
        }
		//Zend_Debug::dump($_productCollectionCat->getAllIds());
        return $_productCollectionCat;
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