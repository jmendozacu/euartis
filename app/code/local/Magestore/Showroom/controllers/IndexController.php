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
class Magestore_Showroom_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function indexAction(){
		$id  = $this->getRequest()->getParam('id');
		$customer= Mage::getSingleton('customer/session')->getCustomer();
    	$customer_id=$customer->getId();
    	$profile=Mage::helper('showroom')->getProfileByCustomerId($customer_id);
    	if($profile)
    	$showroom=Mage::helper('showroom')->getShowroomByProfileId($profile->getProfileId());
    	if(!$id&&!$showroom->getId()){
    		$url = Mage::getUrl('showroom/listing/index');
			$this->_redirectUrl($url);
			return;
    	}
		$this->loadLayout();
		$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('breadcrumbs-showroom_listing',
                array('label' => $this->__('Showroom Listing'),
                    'title' => $this->__('Go to Showroom Listing'),
                    'link' => Mage::getUrl('showroom/listing/index')
				))
            ->addCrumb('breadcrumbs-showroom',
                array('label' => $this->__('Showroom'),
				));
		$this->renderLayout();
	}
	public function camerasAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function overviewAction(){
		$id  = $this->getRequest()->getParam('id');
		$customer= Mage::getSingleton('customer/session')->getCustomer();
    	$customer_id=$customer->getId();
    	$profile = Mage::helper('showroom')->getProfileByCustomerId($customer_id);
    	if($profile)
    	$showroom = Mage::helper('showroom')->getShowroomByProfileId($profile->getProfileId());
    	if(!$id && !$showroom->getId()){
    		$url = Mage::getUrl('showroom/listing/index');
			$this->_redirectUrl($url);
			return;
    	}
		$this->loadLayout();
		$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('breadcrumbs-showroom_listing',
                array('label' => $this->__('Showroom Listing'),
                    'title' => $this->__('Go to Showroom Listing'),
                    'link' => Mage::getUrl('showroom/listing/index')
				))
            ->addCrumb('breadcrumbs-showroom',
                array('label' => $this->__('Showroom'),
				));
		$this->renderLayout();
	}
	
	public function closeAction(){
		$session = Mage::getSingleton('showroom/session');
		$cookie = Mage::getSingleton('core/cookie');
		if(!$cookie->get('not_required')){
			$cookie->set('not_required',1);
			$result = '1';
		}else{
			$result = '';
		}
		$this->getResponse()->setBody($result);
	}
	
	/**
     * facebook share action
     */
    public function facebookAction(){	
		$isAuth = $this->getRequest()->getParam('auth');
		$session = Mage::getSingleton('showroom/session');
		$facebook = Mage::helper('showroom/facebook')->createFacebook();
		$userId = $facebook->getUser();
		
		if($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied'){
			echo("<script>window.close()</script>");
		}elseif ($isAuth && !$userId){
			$loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}

		$user = Mage::helper('showroom/facebook')->getCurrentUser();
		//Zend_Debug::dump($user);die('1');
		if($user && $user['id']){
			if($session->isLoggedIn()){
				$profile = Mage::helper('showroom')->getProfileByCustomerId($session->getCustomer()->getId());
				if($profile->getId()){
					$profile ->setFacebookId($user['id'])
							->save();
				}
			}
		}
		//if ($isAuth && $user){
			$nextUrl = Mage::getUrl('showroom/index/friends');
			die("<script type=\"text/javascript\">window.opener.location.href=\"".$nextUrl."\"; window.close();</script>");
    	//}
		//$this->_redirectUrl(Mage::helper('showroom/facebook')->getFbloginUrl());
		
    }
	
	public function friendsAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
}