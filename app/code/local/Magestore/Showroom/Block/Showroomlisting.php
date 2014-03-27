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
 * Showroom Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */

class Magestore_Showroom_Block_Showroomlisting extends Mage_Core_Block_Template
{
	public function _construct() {
		return parent::_construct();
	}
    public function getProfileByShowroom($showroom){
        return Mage::getModel('showroom/showroomprofile')->load($showroom->getProfileId());
    }
    public function getStylist(){
        return Mage::getModel('showroom/showroom')->getStylist();
    }
    public function getImageByProfile($profile){
    	if($profile->getImage())
            $image=Mage::getBaseUrl('media').$profile->getImage();
        else{
            $image=Mage::getBaseUrl('media').'showroom/avatar.jpg';
        }
        return $image;
    }
    public function getShowrooms(){
        $order= $this->getRequest()->getParam('order','created');
        $direction= $this->getRequest()->getParam('direction','DESC');
        $collection=Mage::getModel('showroom/showroom')->getShowrooms();
        if($this->getStylist()->getAllIds())
                   $collection->addFieldToFilter('showroom_id',array('nin'=>$this->getStylist()->getAllIds()));
        if($order=='popular')
            $collection->getSelect()->order('follow_number '.$direction);
        if($order=='created')
            $collection->getSelect()->order('created_time '.$direction);
        $limit =Mage::helper("showroom")->getShowroomConfig('limit_showroomlisting');
        $collection->setPageSize($limit)->setCurPage(1);
        return $collection;
    }
}