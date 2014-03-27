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
 * Showroom Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_CreateController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function indexAction(){
		$qp = $this->getRequest()->getParam('qp',1);
		if($qp == 1){
			$session  = Mage::getSingleton('showroom/session');
			$session->setStyle(array());
		}
		$this->loadLayout();
		 $this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('create_showroom',
                array('label' => $this->__('Create showroom'),
				));
		$this->renderLayout();
	}
	
	public function pre_save_surveyAction(){
		$qp = $this->getRequest()->getParam('qp');
		$session = Mage::getSingleton('showroom/session');
		$style = $session->getStyle();
		if($data = $this->getRequest()->getPost()){
			if(isset($data['showroom']['question']) && is_array($data['showroom']['question'])){
				foreach($data['showroom']['question'] as $question_id=>$answer_id){
					$style[$question_id] = $answer_id;
				}
				$session->setStyle($style);
			}
		}
		if($qp){
			$url = Mage::getUrl('showroom/create/index',array('qp'=>$qp));
		}else{
			if($session->isLoggedIn()){
				$url = Mage::getUrl('showroom/create/saveshowroom');
			}else{
				$url = Mage::getUrl('showroom/create/sign_up');
			}
		}
		
		$this->_redirectUrl($url);
	}
	
	public function sign_upAction(){
		$authUrl = Mage::getUrl('showroom/create/saveshowroom');
		Mage::getSingleton('customer/session')->setBeforeAuthUrl($authUrl);
		$session = Mage::getSingleton('showroom/session');
		$this->loadLayout();
		 $this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('servey',
                array('label' => $this->__('Showroom Create'),
				));
		$this->renderLayout();
	}
	
	public function saveshowroomAction(){
		$session = Mage::getSingleton('showroom/session');
		$showroom = Mage::getModel('showroom/showroom');
		$profile = Mage::getModel('showroom/showroomprofile');
		$customer = $session->getCustomer();
		$store = Mage::app()->getStore();
		if($customer->getId()){
			$styles = $session->getStyle();
			if(is_array($styles)){
				$profile = Mage::helper('showroom')->getProfileByCustomerId($customer->getId());
				try{
					if(!$profile->getId()){
						$profile->setName($customer->getFirstname().' '.$customer->getLastname());
						$profile->setCustomerId($customer->getId());
						$profile->setCustomerEmail($customer->getEmail());
						$profile->setId(null)
								->save();
					}
					if($profile->getId()){
						$showroom = Mage::helper('showroom')->getShowroomByProfileId($profile->getId());
						if(!$showroom->getId()){
							$showroom->setProfileId($profile->getId());
							$showroom->setStoreGroupId($store->getGroupId());
							$showroom->setCreatedTime(date('Y-m-d H:i:s'));
							$showroom->setModifiedTime(date('Y-m-d H:i:s'));
							try{
								$showroom->setId(null)
										->save();
							}catch(Exception $e){
							
							}
						}else{
							Mage::helper('showroom')->deleteShowroomAnswers($showroom->getId());
							//Mage::helper('showroom')->deleteProducts($showroom->getId());
							//Mage::getSingleton('core/session')->addError('Create showroom unsuccessful. You had a showroom !');
							//$url = Mage::getUrl('showroom/index/overview');
							//$this->_redirectUrl($url);
							//return;
						}
					}
					if($showroom->getId()){
						
						foreach($styles as $questionId=>$answerId){
							$showroomanswer = Mage::getModel('showroom/showroomanswer')->loadByShowroomAndQuestion($showroom->getId(),$questionId);
							$showroomanswer->setShowroomId($showroom->getId());
							$showroomanswer->setAnswerId($answerId);
							try{
								$showroomanswer->save();
								$answer = Mage::getModel('showroom/answer')->load($answerId);
								/*$productCollection = $answer->getProductCollection();
								if(count($productCollection)){
									foreach($productCollection as $product){
										$showroomproduct = Mage::getModel('showroom/showroomproduct')->loadByShowroomAndProduct($showroom->getId(),$product->getId());
										if($showroomproduct->getId()){
											$showroomproduct->setPoint($showroomproduct->getPoint()+1)
															->save();
											$showroomproduct->setId(null);
										}else{
											$showroomproduct->setShowroomId($showroom->getId())
														->setProductId($product->getId())
														->setId(null)
														->save();
										}
									}
								}*/
								$showroomanswer->setId(null);
							}catch(Exception $e){
							}
						}
						$url = Mage::getUrl('showroom/index/overview');
						$this->_redirectUrl($url);
					}
				}catch(Exception $e){
					
				}
			}
		}
		Mage::dispatchEvent('showroom_change_product', array('showroom' => $showroom->getId()));
		$url = Mage::getUrl('showroom/index/overview');
		$this->_redirectUrl($url);
	}
}