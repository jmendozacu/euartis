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
 * Showroom Facebook Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Helper_Facebook extends Mage_Core_Helper_Abstract
{

	public function getAppId(){
		return Mage::helper('showroom')->getReferConfig('facebook_app_id');
	}
	
	public function getFbloginUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		return $this->_getUrl('showroom/index/facebook', array('_secure'=>$isSecure));  
	}
	
	public function getSecretId(){
		return Mage::helper('showroom')->getReferConfig('facebook_app_secret');
	}
	
	public function createFacebook(){
		try{
			require_once Mage::getBaseDir('lib').DS.'Facebookv3'.DS.'facebook.php';
		}catch(Exception $e){}
		
		// Create our Application instance.
		$facebook = new Facebook(array(
			'appId'  => $this->getAppId(),
			'secret' => $this->getSecretId(),
			'cookie' => true,
		));
		return $facebook;
	}
	
	public function getCurrentUser(){
		$facebook = $this->createFacebook();
		
    	$userId = $facebook->getUser();
		$fbme = NULL;
		if ($userId) {
			try {
				$fbme = $facebook->api('/me');
			} catch (FacebookApiException $e) {}
		}
		return $fbme;
	}
	
	public function getFbUser(){
		$facebook = $this->createFacebook();
		
    	/*$userId = $facebook->getUser();
		$fbme = NULL;
		if ($userId) {
			try {
				$fbme = $facebook->api('/me');
			} catch (FacebookApiException $e) {}
		}*/
		$session = Mage::getSingleton('showroom/session');
		if($session->isLoggedIn()){
			$profile = Mage::helper('showroom')->getProfileByCustomerId($session->getCustomer()->getId());
			$facebookId = $profile->getFacebookId();
			try {
				$fbme = $facebook->api("/$facebookId");
			} catch (FacebookApiException $e) {}
		}
		//Zend_Debug::dump($fbme);
		return $fbme;	
	}
	
	public function getFacebookId(){
		$session = Mage::getSingleton('showroom/session');
		$facebookId = null;
		if($session->isLoggedIn()){
			$profile = Mage::helper('showroom')->getProfileByCustomerId($session->getCustomer()->getId());
			$facebookId = $profile->getFacebookId();
		}else{
			$facebook = $this->createFacebook();
			$facebookId = $facebook->getUser();
		}
		return $facebookId;
	}
	
	public function getFriends(){
		$facebook = $this->createFacebook();
    	$facebookId = $this->getFacebookId();
		$friendIds = array();
		if($facebookId){
			try{
				$fs = $facebook->api("/$facebookId/friends");
				
				$friends = $fs['data'];
				foreach($friends as $friend){
					$friendIds[] = $friend['id'];
				}
			}catch(FacebookApiException $e){
			}
		}
		//Zend_Debug::dump($friendIds);
		return $friendIds;
	}
	public function getFriends2(){
		$facebook = $this->createFacebook();
    	$userId = $facebook->getUser();
		$friends = array();
		if($userId){
			$fs = $facebook->api('/me/friends');
			$friends = $fs['data'];
		}
		return $friends;
	}
	
	public function getAvatar($facebookId){
		$facebook = $this->createFacebook();
		$userId = $facebook->getUser();
		$avatar = "";
		if($userId){
			$fdata = $facebook->api(array( 'method' => 'fql.query', 'query' => "SELECT pic_square FROM user WHERE uid = $facebookId"));
			$avatar = $fdata[0]['pic_square'];
		}
		return $avatar;
	}
	public function getFBinvites(){
		$facebook = $this->createFacebook();
    	$facebookId = $this->getFacebookId();
		$friends = array();
		if($facebookId){
			try{
			$fs = $facebook->api("/$facebookId/friends");
			$friendids = $fs['data'];
			foreach($friendids as $friend){
				$friends[] = array(
						'id'=>$friend['id'],
						'name'=>$friend['name']
					);
			}
			}catch(FacebookApiException $e){
			}
		}
		return $friends;

	}
}