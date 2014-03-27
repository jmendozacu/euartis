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
class Magestore_Showroom_Model_Showroomanswer extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('showroom/showroomanswer');
	}
	
	public function getShowroom(){
		$showroom = Mage::getModel('showroom/showroom');
		if($this->getShowroomId()){
			$showroom->load($this->getShowroomId());
		}
		return $showroom;
	}
	
	public function loadByShowroomAndAnswer($showroomId,$answerId){
		$collection = $this	->getCollection()
							->addFieldToFilter('showroom_id',$showroomId)
							->addFieldToFilter('answer_id',$answerId)
							;
		if($collection->getSize()){
			$this->load($collection->getFirstItem()->getId());
		}
		return $this;
	}
	
	/*
	** get showroom answer by showroom id and question id
	*/
	
	public function loadByShowroomAndQuestion($showroomId,$questionId){
		$answerCollection = Mage::getModel('showroom/answer')
									->getCollection()
									->addFieldToFilter('question_id',$questionId)
							;
		if($answerCollection->getSize()){
			$answerIds = $answerCollection->getAllIds();
			$showroomAnswerCollection = $this
										->getCollection()
										->addFieldToFilter('showroom_id',$showroomId)
										->addFieldToFilter('answer_id',array('in'=>$answerIds))
										;
			if($showroomAnswerCollection->getSize()){
				$this->load($showroomAnswerCollection->getFirstItem()->getId());
			}
		}
		return $this;
	}
}