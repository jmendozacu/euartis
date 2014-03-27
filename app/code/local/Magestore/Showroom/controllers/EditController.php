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
 * Showroom Edit Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_EditController extends Mage_Core_Controller_Front_Action
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
                array('label' => $this->__('Showroom'),
                	'title' => $this->__('Go to my Showroom'),
                	'link' => Mage::getUrl('showroom/index/overview')
				))
            ->addCrumb('breadcrumbs-setting',
                array('label' => $this->__('Edit'),
				));
		$this->renderLayout();
	}
	public function saveAction(){
		$this->_initAction();
		$showroomId=Mage::helper('showroom')->getShowroomLogin()->getId();
		$data = $this->getRequest()->getPost();
		$arr=explode(",",$data['products']);
		$count=count($arr);$i=1;
		for($i;$i<$count;) {
			if($arr[$i+1]==-1){
				$model=Mage::helper('showroom')->getShowroomProduct($arr[$i],$showroomId)
					->setShowroomId($showroomId)->setProductId($arr[$i])->setType(0)->save();
			}
			if($arr[$i+1]==1){
				$model=Mage::helper('showroom')->getShowroomProduct($arr[$i],$showroomId)
					->setShowroomId($showroomId)->setProductId($arr[$i])->setType(1)->setPoint(10000)->save();
			}
			$i=$i+2;
		}
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('showroom')->__('Showroom was successfully saved'));
		$this->_redirect('showroom/edit/index');
	}
	public function viewAction(){
		$this->_initAction();
		$block = $this->getLayout()
				->createBlock('showroom/componentedit');
		$this->getResponse()->setBody(json_encode ($block->toHtml()));
		$checkloadmore=$block->isLoadMore();
		if(!$checkloadmore)
			$this->getResponse()->setBody('1');
		$block->setIsLoadMore(true);
	}
}