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
 * Showroom Servey Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Survey extends Mage_Core_Block_Template

{
	/**
	 * prepare block's layout
	 *
	 * @return Magestore_Showroom_Block_Showroom
	 */
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('showroom/survey.phtml');
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
							->setOrder('sort',ASC)
							;
		return $collection;
		
	}
	
	/*
	** get answer collection by question id
	*/
	
	public function getAnswerCollectionByQuestionId($questionId){
		$collection = Mage::getModel('showroom/answer')
						->getCollection()
						->addFieldToFilter('question_id',$questionId)
						->addFieldToFilter('status',Magestore_Showroom_Model_Answer::ENABLE_STATUS)
						->setOrder('sort',ASC)
						;
		return $collection;
	}
	
	/*
	** get answer image
	*/
	
	public function getAnswerImage($answer){
		$path = Mage::getBaseUrl('media').'/showroom/question/'.$answer->getQuestionId().'/'.$answer->getImage();
		return $path;
	}
	
	/*
	** get showroom
	*/
	
	public function getShowroom(){
		$session = Mage::getSingleton('showroom/session');
		if($session->isLoggedIn()){
			$customer = $session->getCustomer();
			$profile = Mage::helper('showroom')->getProfileByCustomerId($customer->getId());
			$showroom = Mage::helper('showroom')->getShowroomByProfileId($profile->getId());
			if($showroom->getId())
				return $showroom;
		}
		return null;
	}

}