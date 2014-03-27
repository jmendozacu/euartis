<?php
class Magestore_Showroom_Block_Friends extends Mage_Core_Block_Template
{
	public function _construct() {
        
        return parent::_construct();
    }
	
	public function _prepareLayout(){
		parent::_prepareLayout();
		$this->setTemplate('showroom/friends.phtml');
		return $this;
	}
	
	/*
	** get link facebook login
	*/
	
	public function getFbLoginButtonUrl(){
		$isSecure = Mage::getStoreConfig('web/secure/use_in_frontend');
		$authUrl = $this->getUrl('showroom/index/facebook', array('_secure'=>$isSecure, 'auth'=>1));
		
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
	/*
	** get showrooms of friends on facebook
	*/
	
	public function getFriendShowroom(){
		$store = Mage::app()->getStore();
		$friendIds = Mage::helper('showroom/facebook')->getFriends();
		$collection = Mage::getModel('showroom/showroom')->getCollection()
							->addFieldToFilter('store_group_id',$store->getGroupId())
							;				
		$collection->getSelect()
					->join(array('profile_table'=>Mage::getModel('core/resource')->getTableName('showroom_profile')),'main_table.profile_id = profile_table.profile_id',array('facebook_id'=>'profile_table.facebook_id'));
		$collection->addFieldToFilter('facebook_id',array('in'=>$friendIds));
		return $collection;		
	}
	
	
}