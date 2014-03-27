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
class Magestore_Showroom_Model_Showroom extends Mage_Core_Model_Abstract
{
	protected $_productIds;
	protected $_storeId;
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/showroom');
	}
	public function getStylist(){
		$stylist=$this->getShowrooms()
				->addFieldToFilter('type',1);
		return $stylist;
	}
	public function getShowrooms(){
        return $this->getCollection()
                    ->addFieldToFilter('status',2)
                    ->addFieldToFilter('is_private',0)
                    ->addFieldToFilter('store_group_id',Mage::app()->getStore()->getGroupId());
    }
	
	public function getStoreId(){
		if($this->getId()){
			if(!$this->_storeId){
				$storeGroupId = $this->getStoreGroupId();
			}
		}
			
	}
	
	
	/*
	** get Answer Id by Question Id
	*/
	public function getAnswerIdByQuestion($questionId){
		//Zend_Debug::dump($questionId);
		if($this->getId()){
			$answerCollection = Mage::getModel('showroom/answer')
								->getCollection()
								->addFieldToFilter('question_id',$questionId)
								;
			if($answerCollection->getSize()){
				$answerIds = $answerCollection->getAllIds();
				$showroomAnswerCollection = Mage::getModel('showroom/showroomanswer')
											->getCollection()
											->addFieldToFilter('showroom_id',$this->getId())
											->addFieldToFilter('answer_id',array('in'=>$answerIds))
											;
				if($showroomAnswerCollection->getSize()){
					//Zend_Debug::dump($showroomAnswerCollection->getFirstItem()->getId());
					return $showroomAnswerCollection->getFirstItem()->getAnswerId();
				}
			}
		}
		return null;
	}
	public function sendEmail($message,$customer,$sender,$link,$profile,$mailSubject){
	  	$templateId = Mage::getStoreConfig('showroom/email/email_template');
	 
    	$email = $customer['email'];
	 
	  	$name = $sender['name'];
	  	if($profile->getImage())
            $image=Mage::getBaseUrl('media').$profile->getImage();
        else{
            $image=Mage::getBaseUrl('media').'showroom/avatar.jpg';
        }
	  
	  	$vars = Array(	'customer'=>$customer,
	                	'message' =>$message,
                        'admin'=>$name,
                        'link'=>$link,
                        'profile'=>$profile,
                        'image'=>$image,
                       );
	  	$storeId = Mage::app()->getStore()->getId();
	 
	  	$translate  = Mage::getSingleton('core/translate');
	  	Mage::getModel('core/email_template')
	      	->setTemplateSubject($mailSubject)
	      	->sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
	  	$translate->setTranslateInline(true);
    }
	
	/*
	** get answer collection
	*/
	
	public function getAnswerCollection(){
		$collection = Mage::getModel('showroom/answer')->getCollection();
		if($this->getId()){
			$showroomanswerCollection = Mage::getModel('showroom/showroomanswer')->getCollection()
							->addFieldToFilter('showroom_id',$this->getId());
			if($showroomanswerCollection->getSize()){
				$answerIds = array();
				foreach($showroomanswerCollection as $showroomanswer){
					$answerIds = $showroomanswer->getAnswerId();
				}
				if(count($answerIds))
						$collection->addFieldToFilter('answer_id',array('in'=>$answerIds));
			}				
		}
		return $collection;
	}
	
	
	/*
	** get product ids
	*/
	
	public function getProductIds(){
		if(!$this->_productIds){
			$productCollection = Mage::getModel('catalog/product')->getCollection();
			if($this->getId()){
				$answerIds = array();
				$shanswers =  Mage::getModel('showroom/showroomanswer')->getCollection()
								->addFieldToFilter('showroom_id',$this->getId());
				foreach($shanswers as $showroomanswer){
						$answerIds[] = $showroomanswer->getAnswerId();
				}
				
				if(count($answerIds)){
					$collection = Mage::getModel('showroom/answer')->getCollection();
					$collection->addFieldToFilter('answer_id',array('in'=>$answerIds));
					
					if($collection->getSize()){
						foreach($collection as $answer){
							$answer = Mage::getModel('showroom/answer')->load($answer->getId());
							//Zend_Debug::dump($storeId);
							if($answer->getProductCollection($this->getStoreGroupId())){
								$pIds = $answer->getProductCollection($this->getStoreGroupId())->getAllIds();
								//Zend_Debug::dump($pIds);
								foreach($pIds as $productId){
									$this->_productIds[$productId] = $this->_productIds[$productId] + 1;									
								}
							}
						}
					}
				}
			}
			$nins = Mage::getModel('showroom/showroomproduct')
					->getCollection()
					->addFieldToFilter('showroom_id',$this->getId())
					->addFieldToFilter('type',0);
			$ins = Mage::getModel('showroom/showroomproduct')
					->getCollection()
					->addFieldToFilter('showroom_id',$this->getId())
					->addFieldToFilter('type',1);
			foreach ($nins as $value) {
				if(isset($this->_productIds[$value->getProductId()]))
					unset($this->_productIds[$value->getProductId()]);
			}
			foreach ($ins as $value) {
				$this->_productIds[$value->getProductId()]=$value->getPoint();
			}
		}
		//Zend_Debug::dump($this->_productIds);
		return $this->_productIds;
	}
	
	/*
	** get product collection
	*/
	
	public function getProductCollection(){
		$productIds = array_keys($this->getProductIds());
		$productCollection = Mage::getModel('catalog/product')->getCollection();
		$productCollection->addFieldToFilter('entity_id',array('in'=>$productIds))
							;
		return $productCollection;
	}
}