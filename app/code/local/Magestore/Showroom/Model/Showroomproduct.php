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
 * Showroom Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Model_Showroomproduct extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/showroomproduct');
	}
	
	/*
	** load showroom product by showroom id and product id
	*/
	
	public function loadByShowroomAndProduct($showroomId,$productId)
	{
		$collection = $this	->getCollection()
							->addFieldToFilter('showroom_id',$showroomId)
							->addFieldToFilter('product_id',$productId)
						;
		if($collection->getSize()){
			$this->load($collection->getFirstItem()->getId());
		}
		return $this;
	}
}