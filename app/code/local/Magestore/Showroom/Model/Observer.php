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
 * Showroom Observer Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Model_Observer
{
	/**
	 * process controller_action_predispatch event
	 *
	 * @return Magestore_Showroom_Model_Observer
	 */
	public function controllerActionPredispatch($observer){
		$modules = Mage::helper('showroom')->getModuleArray();
		$controllers = Mage::helper('showroom')->getControllerArray();		
		$controllerAction = $observer->getEvent()->getControllerAction();
		$session = Mage::getSingleton('showroom/session');
		$expiredTime = Mage::helper('showroom')->getShowroomConfig('cookie_expired_time');
		$expiredTime = $expiredTime ? $expiredTime : 86400;
		$cookie = Mage::getSingleton('core/cookie');
		if($expiredTime)
			$cookie->setLifeTime(intval($expiredTime)*86400);
		$display_type = Mage::helper('showroom')->getGeneralConfig('required_register');
		if(($display_type == Magestore_Showroom_Model_Popup::REQUIRED) || (($display_type == Magestore_Showroom_Model_Popup::CAN_TURN_OFF))){
			$module = Mage::app()->getRequest()->getModuleName();
			$controller = Mage::app()->getRequest()->getControllerName();			
			$action = Mage::app()->getRequest()->getActionName();
			if(((!in_array($module,$modules)) || (!in_array($controller,$controllers)) )&& (!$session->isLoggedIn()) && (!$cookie->get('not_required')) ){
				$block = Mage::app()->getLayout()->createBlock('showroom/popup');
				$controllerAction->getResponse()->setBody($block->toHtml());
			}
		}
		return $this;
	}
	public function sendMailFollows($observer){
		$showroomId=$observer->getShowroom();
		$link='showroom/index/overview/id/'.$showroomId;
		$showroom=Mage::getModel('showroom/showroom')->load($showroomId);
		$profile=Mage::getModel('showroom/showroomprofile')->load($showroom->getProfileId());
		$sendermail== Mage::getStoreConfig('showroom/email/sender');
		$message='test showroom';
        $mailSubject = 'News from '.$profile->getName().' \'s Showroom';
		$sender=array(
			'name'=>'admin',
			'email'=>'admin@gamil.com');

		$follows=Mage::getModel('showroom/followers')->getCollection()
				->addFieldToFilter('showroom_id',$showroomId);
		foreach ($follows as $follow) {
			$pro=Mage::helper('showroom')->getProfileByCustomerId($follow->getCustomerId());
			if ($pro->getIsReceiveMail()==0)
				break;
			$customer=Mage::getModel('customer/customer')->load($follow->getCustomerId());
			Mage::getModel('showroom/showroom')->sendEmail($message,$customer,$sender,$link,$profile,$mailSubject);
		}
	}
	
	/* protected function _helper()
	{
		return Mage::helper('showroom/ajax');
	}
    
    
    protected function _getListBlock($blocks)
    {
        foreach ($blocks as $block){
            if (get_class($block) == 'Magestore_Showroom_Block_Ajax'){
                return $block;
            }
        }
    } */
	
	/* public function generateBlocksAfter($event)
    {                         
        $layout = $event->getLayout();    
        # Get list
        $list = $this->_getListBlock($layout->getAllBlocks());                
        if ($list){
            $children = $list->getSortedChildren();                       
            $parentBlock = $list->getParentBlock();                        
            $name = $list->getNameInLayout();
            $type = $list->getType();        
            $alias = $list->getBlockAlias();
			
            $newBlock = $list->getLayout()->createBlock('showroom/catalog_product_container',$name)->setBlockAlias($alias)->setLayout($layout);            
            
            $newBlock->setToolbarBlockName($list->getToolbarBlockName());       
            $nativeList = $list->getLayout()->createBlock('showroom/ajax')->setBlockAlias($alias)->setLayout($layout);                                    
            $this->_helper()->setNativeTemplate($list->getTemplate());                 
            $newBlock->setNameInLayout($name);                                           
            $newBlock->setChild('native_list', $nativeList->setNativeTemplate());                                    
            $newBlock->setChild('native_resource', $list);                                    
                  
            foreach ($children as $child){               
                $newBlock->setChild($layout->getBlock($child)->getBlockAlias(), $layout->getBlock($child) );                                                               
            }
            $parentBlock->setChild('showroom_product', $newBlock); 
			 
            
            $parentBlock->setListCollection();
        }
    } */
	
	public function showroom_question_answer_save_before($observer){
		$answer = $observer->getShowroomQuestionAnswer();
		$answer = Mage::getModel('showroom/answer')->load($answer->getId());
		if(!Mage::registry('showroom_question_answer_'.$answer->getId().'_conditions')){
			Mage::register('showroom_question_answer_'.$answer->getId().'_conditions',$answer->getData('conditions_serialized'));
		}
	}
	
	public function showroom_question_answer_save_after($observer){
		/*$answer = $observer->getShowroomQuestionAnswer();
		//Zend_Debug::dump(Mage::registry('showroom_question_answer_'.$answer->getId().'_conditions'));die();
		if(Mage::registry('showroom_question_answer_'.$answer->getId().'_conditions')){
			$oldconditions = Mage::registry('showroom_question_answer_'.$answer->getId().'_conditions');
			//Zend_Debug::dump($oldconditions);
			
			if($oldconditions){
				if($oldconditions != $answer->getData('conditions_serialized')){
					Zend_Debug::dump($oldconditions);
					Zend_Debug::dump($answer->getData('conditions_serialized'));
					die('1');
					Mage::register('showroom_question_answer_'.$answer->getId().'_conditions','');
				}
			}
		}
		die('abc');*/
	}
}