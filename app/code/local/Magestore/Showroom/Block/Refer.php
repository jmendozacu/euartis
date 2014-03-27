<?php
class Magestore_Showroom_Block_Refer extends Mage_Core_Block_Template
{
	/** Facebook */
	public function getFbLoginUrl(){
		try {
			if (!class_exists('Facebook'))
				require_once(Mage::getBaseDir('lib') . DS .'Facebookv3'. DS .'facebook.php');
			$facebook = new Facebook(array(
				'appId'		=> Mage::helper('showroom')->getReferConfig('facebook_app_id'),
				'secret'	=> Mage::helper('showroom')->getReferConfig('facebook_app_secret'),
				'cookie'	=> true
			));
			$loginUrl = $facebook->getLoginUrl(array(
				'display'		=> 'popup',
				'redirect_uri'	=> $this->getUrl('showroom/index/facebook',array('auth' => 1)),
				'scope' 		=> 'publish_stream,email',
			));
			return $loginUrl;
		} catch (Exception $e){
			
		}
	}
	
	public function getFriendShowroom(){
		$store = Mage::app()->getStore();
		$friendIds = Mage::helper('showroom/facebook')->getFriends();
		//Zend_Debug::dump($friendIds);
		$collection = Mage::getModel('showroom/showroom')->getCollection()
							->addFieldToFilter('store_group_id',$store->getGroupId())
							;			
		$collection->getSelect()
					->join(array('profile_table'=>Mage::getModel('core/resource')->getTableName('showroom_profile')),'main_table.profile_id = profile_table.profile_id',array('facebook_id'=>'profile_table.facebook_id'));
					//Zend_Debug::dump($collection->toArray());
		$collection->addFieldToFilter('facebook_id',array('in'=>$friendIds));
		//Zend_Debug::dump($collection->getAllIds());die('1');
		return $collection;		
	}
}