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

class Magestore_Showroom_Block_Menu extends Mage_Core_Block_Template
{

    protected $_showroomlinks = array();
    protected $_stylistlinks = array();
    protected $_friendslinks = array();
    static $stylistIds=array();

    protected $_activeLink = false;

    public function _construct() {
        $store_id=$this->getStoreId();
        $category_str = Mage::getModel('catalog/category')
                        ->load(Mage::app()->getStore($store_id)->getRootCategoryId())
                        ->getAllChildren();
        $category_arr=explode(",",$category_str);
        $this->_activeLink =  $this->helper('core/url')->getCurrentUrl();//_completePath($path);
        $config_cat=explode(",",Mage::helper('showroom')->getShowroomConfig('category'));
        $category=Mage::getResourceModel('catalog/category_collection')
            ->addFieldToFilter('entity_id',array('in'=>$config_cat))
            ->addFieldToFilter('entity_id',array('in'=>$category_arr))
            ->addAttributeToSelect('name');
        $id  = $this->getRequest()->getParam('id');
        if($id) $add='/id/'.$id;
        $this->addShowRoomLink('all','showroom/index/overview'.$add,'Overview');
        
        foreach ($category as $cat) {
            $this->addShowRoomLink($cat->getName(),'showroom/index/index'.$add.'?cat='.$cat->getId(),$cat->getName());
        }
        $profile=$this->getProfile();
        $profile_temp=$this->getProfileByShowroom($showroom=Mage::getModel('showroom/showroom')->load($id));
            
        if(!$id||($profile==$profile_temp)){
			$this->addShowRoomLink('edit','showroom/edit/index','Edit showroom');
            $this->addShowRoomLink('setting','showroom/setting/index','Setting');
        }
        $stylists=$this->getStylist();
        $i=0;
        foreach ($stylists as $stylist) {
            $i++;
           // if($i>4) break;
            self::$stylistIds[]=$stylist->getShowroomId();
            $this->addStylistLink($this->getProfileByShowroom($stylist)->getProfileId().'stylist','showroom/index/overview/id/'.$stylist->getId(),$this->getProfileByShowroom($stylist)->getName());
        }
        /*

        $this->addFriendsLink('thanh','showroom/index/index/friends/7','blanka');
        $this->addFriendsLink('thanh1','showroom/index/index/friends/8','magic');
        $this->addFriendsLink('thanh2','showroom/index/index/friends/9','sarah');
        */
        return parent::_construct();
    }
    public function addShowRoomLink($name, $path, $label, $urlParams=array())
    {
        $this->_showroomlinks[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));
        return $this;
    }
    public function addStylistLink($name, $path, $label, $urlParams=array())
    {
        $this->_stylistlinks[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));
        return $this;
    }
    public function addFriendsLink($name, $path, $label, $urlParams=array())
    {
        $this->_friendslinks[$name] = new Varien_Object(array(
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'url' => $this->getUrl($path, $urlParams),
        ));
        return $this;
    }



    public function getShowRoomLinks()
    {
        return $this->_showroomlinks;
    }
    public function getStylistLinks()
    {
        return $this->_stylistlinks;
    }
    public function getFriendsLinks()
    {
        return $this->_friendslinks;
    }

    public function isActive($link)
    {
        return false;
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
                // no break

            case 2:
                $path .= '/index';
        }
        return $path;
    }
    public function getProfile(){
        $customer= Mage::getSingleton('customer/session')->getCustomer();
        $customer_id=$customer->getId();
        $profile=Mage::helper('showroom')->getProfileByCustomerId($customer_id);
        return $profile;
    }
    public function getShowroom(){
        if($this->getProfile())
            $showroom=Mage::helper('showroom')->getShowroomByProfileId($this->getProfile()->getProfileId());
        return $showroom;
    }
    public function getFollows($id){
        $collection = Mage::getModel('showroom/followers')->getCollection()->addFieldToFilter('showroom_id',$id);
        return $collection->getSize();
    }
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return Mage::app()->getStore()->getId();
    }
    public function getProfileByShowroom($showroom){
        return Mage::getModel('showroom/showroomprofile')->load($showroom->getProfileId());
    }
    public function getImageArray(){
        $image=array();
        foreach (self::$stylistIds as $id) {
            $showroom=Mage::getModel('showroom/showroom')->load($id);
            if($this->getProfileByShowroom($showroom)->getImage())
                $image[]=Mage::getBaseUrl('media').$this->getProfileByShowroom($showroom)->getImage();
            else{
                $image[]=Mage::getBaseUrl('media').'showroom/avatar.jpg';
            }
        }
        return $image;
    }
    public function getStylist(){
        return Mage::getModel('showroom/showroom')->getStylist();
    }
}
