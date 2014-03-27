<?php
class Magestore_Showroom_Block_Fbinvite extends Mage_Core_Block_Template
{
	public function _construct() {
        
        return parent::_construct();
    }
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		return $this;
	}
	
	/*
	** get link facebook login
	*/
	
	public function getFbLoginButtonUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		$authUrl = $this->getUrl('showroom/index/invitelogin', array('_secure'=>$isSecure, 'auth'=>1));
		
		$facebook = Mage::helper('showroom/facebook')->createFacebook();
		$loginUrl = $facebook->getLoginUrl(
			array(
				'display'   => 'popup',
				'redirect_uri'      => $authUrl,
				'scope' => 'publish_stream,email,read_stream',
			)
  		);
		return $loginUrl;
	}
	
	public function getFriend(){
		$friendIds = Mage::helper('showroom/facebook')->getFriends();
		return $friendIds;		
	}
	public function getAvatar($facebookId){
		$facebookAvatar = Mage::helper('showroom/facebook')->getAvatar($facebookId);
		return $facebookAvatar;
	}
	public function getFBinvites(){
		$friends = Mage::helper('showroom/facebook')->getFBinvites();
		return $friends;
	}
}