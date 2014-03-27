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
 * Showroom Answer Resource Collection Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Model_Mysql4_Answer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_store_id = null;
	
	public function setStoreId($value){
		$this->_store_id = $value;
		return $this;
	}
	
	public function getStoreId(){
		return $this->_store_id;
	}
	
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/answer');
	}
	
	protected function _afterLoad(){
    	parent::_afterLoad();
    	if ($storeId = $this->getStoreId())
    	foreach ($this->_items as $item){
    		$item->setStoreId($storeId)->loadStoreValue();
    	}
    	return $this;
    }
}