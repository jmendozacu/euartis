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
 * Showroom Setting Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_SettingController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	protected function _initAction(){
		$customer= Mage::getSingleton('customer/session')->getCustomer();
    	$customer_id=$customer->getId();
    	$profile=Mage::helper('showroom')->getProfileByCustomerId($customer_id);
    	if($profile)
    	$showroom=Mage::helper('showroom')->getShowroomByProfileId($profile->getProfileId());
    	if(!$showroom->getId()){
    		$url = Mage::getUrl('showroom/listing/index');
			$this->_redirectUrl($url);
			return;
    	}
		return $this;
	}
	public function indexAction(){
		$this->_initAction();
		$this->loadLayout();
		$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('breadcrumbs-showroom',
                array('label' => $this->__('My Showroom'),
                	'title' => $this->__('Go to My Showroom'),
                	'link' => Mage::getUrl('showroom/index/overview')
				))
            ->addCrumb('breadcrumbs-setting',
                array('label' => $this->__('Setting'),
				));
		$this->renderLayout();
	}
	/**
	 * save action
	 */
	public function saveAction(){
		$this->_initAction();
		$customer= Mage::getSingleton('customer/session')->getCustomer();
       	$customer_id=$customer->getId();
        $profile=Mage::helper('showroom')->getProfileByCustomerId($customer_id);
        $showroom=Mage::helper('showroom')->getShowroomByProfileId($profile->getProfileId());
        $params=$this->getRequest()->getParams();

        if(isset($_FILES['images']['name']) && $_FILES['images']['name'] != '') {
				try {
					$image=Mage::helper('showroom')->upload('images','showroom'.DS.'profile'.DS.$profile->getId());
					$profile->setImage($image);
				} catch (Exception $e) {
				}
			}
		$is_receive_mail=0 ;
		if ($params['is_receive_mail']) $is_receive_mail=$params['is_receive_mail'];
		$profile->setIsReceiveMail($is_receive_mail);
		$profile->setName($params['name']);
		$profile->setCustomerEmail($params['customer_email']);
		$profile->save();
		$showroom->setIsPrivate($params['is_private'])->save();
		$this->_redirect('*/*/index');
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function addToShowroomAction(){
		$id = $this->getRequest()->getParam('id');
		$customer= Mage::getSingleton('customer/session')->getCustomer();
    	$customer_id=$customer->getId();
		$profile=Mage::helper('showroom')->getProfileByCustomerId($customer_id);
		if($profile)
 			$showroom=Mage::helper('showroom')->getShowroomByProfileId($profile->getProfileId());
 		if(!$showroom->getId()){
		Mage::getSingleton('core/session')->addNotice(Mage::helper('showroom')->__('You have not had a showroom yet. Please create your own showroom.'));
    		$this->_redirect('showroom/create/index');
    		return;
    	}
 		try{
			Mage::getModel('showroom/showroomproduct')->loadByShowroomAndProduct($showroom->getShowroomId(),$id)
    			->setShowroomId($showroom->getShowroomId())
    			->setProductId($id)
    			->setPoint(10000)
    			->setType(1)
    			->save();
    	}catch (Exception $e){
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('showroom')->__('Product was successfully added to showroom'));
		Mage::dispatchEvent('showroom_change_product', array('showroom' => $showroom->getId()));
    	$this->_redirect('showroom/edit/index/');
	}
}