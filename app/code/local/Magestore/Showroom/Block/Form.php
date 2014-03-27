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
 * Showroom Form Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Form extends Mage_Customer_Block_Form_Register

{
	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Showroom_Block_Showroom
	 */
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('showroom/form.phtml');
		return $this;
	}
	
	public function getStoreId(){
		$storeId = Mage::app()->getStore()->getId();
		return $storeId;
	}
	
	/*
	** get question collection
	*/
	
	public function getQuestionCollection(){
		$collection = Mage::getModel('showroom/question')
							->getCollection()
							->addFieldToFilter('status',Magestore_Showroom_Model_Question::STATUS_ENABLED)
							->setStoreId($this->getStoreId())
							;
		return $collection;
		
	}
}