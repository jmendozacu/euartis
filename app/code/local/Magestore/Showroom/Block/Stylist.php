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

class Magestore_Showroom_Block_Stylist extends Mage_Core_Block_Template
{
	public function _construct() {
		return parent::_construct();
	}
    public function getProfileByShowroom($showroom){
        return Mage::getModel('showroom/showroomprofile')->load($showroom->getProfileId());
    }
    public function getShowrooms(){
        $stylist=Mage::getModel('showroom/showroom')->getStylist();
        return $stylist;
    }
    public function getImageByProfile($profile){
    	if($profile->getImage())
            $image=Mage::getBaseUrl('media').$profile->getImage();
        else{
            $image=Mage::getBaseUrl('media').'showroom/avatar.jpg';
        }
        return $image;
    }
}