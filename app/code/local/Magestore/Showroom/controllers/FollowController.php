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
class Magestore_Showroom_FollowController extends Mage_Core_Controller_Front_Action
{
	/**
	 * index action
	 */
	public function indexAction(){
		$this->loadLayout();
		$this->getLayout()->getBlock('breadcrumbs')
            ->addCrumb('home',
                array('label' => $this->__('Home'),
                    'title' => $this->__('Go to Home Page'),
                    'link' => Mage::getBaseUrl()
				))
            ->addCrumb('breadcrumbs-listing',
                array('label' => $this->__('Showroom Listing'),
				));
		$this->renderLayout();
	}
	public function followAction(){
		$showroom_id = $this->getRequest()->getParam('showroom');
		$customer= Mage::getSingleton('customer/session')->getCustomer();
        $customer_id=$customer->getId();
		Mage::helper('showroom')->getFollowing($showroom_id,$customer_id)
				->setShowroomId($showroom_id)
				->setCustomerId($customer_id)
				->setStatus(1)
				->save();
		$model=Mage::getModel('showroom/showroom')->load($showroom_id);
		$model->setFollowNumber($model->getFollowNumber()+1)
				->save();
		$this->getResponse()->setBody($model->getFollowNumber());
	}
	public function unfollowAction(){
		$showroom_id = $this->getRequest()->getParam('showroom');
		$customer= Mage::getSingleton('customer/session')->getCustomer();
        $customer_id=$customer->getId();
		Mage::helper('showroom')->getFollowing($showroom_id,$customer_id)
				->delete();
		$model=Mage::getModel('showroom/showroom')->load($showroom_id);
		$model->setFollowNumber($model->getFollowNumber()-1)
				->save();
		$this->getResponse()->setBody($model->getFollowNumber());
	}
}