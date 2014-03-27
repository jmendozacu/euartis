<?php
class Magestore_Showroom_Model_Session extends Mage_Core_Model_Session
{
	public function __construct(){
		$this->init('showroom');
	}
	
	public function isLoggedIn(){
		if($this->getCustomer()->getId())
			return true;
		return false;
	}

	public function getCustomer(){
		return Mage::getSingleton('customer/session')->getCustomer();
	}
}
