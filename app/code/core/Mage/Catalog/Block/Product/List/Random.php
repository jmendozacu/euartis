<?php
class Mage_Catalog_Block_Product_List_Random extends Mage_Catalog_Block_Product_List
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $categoryID = $this->getCategoryId();
            if($categoryID)
            {
              $category = new Mage_Catalog_Model_Category();
              $category->load($categoryID); // this is category id
              $collection = $category->getProductCollection();
            } else
            {
              $collection = Mage::getResourceModel('catalog/product_collection');
            }
            Mage::getModel('catalog/layer')->prepareProductCollection($collection);
            $collection->getSelect()->order('rand()');
            $collection->addStoreFilter();
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 3;
            $collection->setPage(1, $numProducts)->load();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
} 
?>