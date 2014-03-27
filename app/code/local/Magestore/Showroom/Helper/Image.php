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
 * Showroom Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Showroom
 * @author  	Magestore Developer
 */
class Magestore_Showroom_Helper_Image extends Mage_Core_Helper_Abstract
{	
	protected $_obj;
    protected $_pathname;
    protected $_placeholder;
    protected function _reset()
    {
        $this->_obj = null;
        $this->_pathname = null;
        $this->_placeholder = null;
        return $this;
    }
    public function init($obj,$pathname)
    {
        $this->_reset();
        $this->setObj($obj);
        $this->setPathname($pathname);
        return $this;
    }
    public function resize($size)
    {
    	$pathimageinput=Mage::getBaseDir('media') .DS.$this->getPathname().DS.$this->getObj()->getImage();
    	//var_dump($pathimageinput);
        $imagename=$this->getObj()->getImage();
    	if($imagename==''){
    		$imagename='avatar.jpg';
    		$pathimageinput=Mage::getBaseDir('media') . DS .'showroom'.DS.'avatar.jpg';
    	}
    	$pathimageoutput =$this->getPathname().DS.$size.DS.$imagename;
        $urlimage=str_replace(DS,'/',$pathimageoutput);
        $urlimage=Mage::getBaseUrl('media').$urlimage;
        $pathBaseDir=Mage::getBaseDir('media').DS.$pathimageoutput;
        $this->setPlaceholder($urlimage);
        if(!is_file($pathBaseDir)) {
			$_backgroundColor  = array(255, 255, 255);
			try {
					$imageObj = new Varien_Image($pathimageinput);
            		$imageObj->backgroundColor($_backgroundColor);
            		$imageObj->constrainOnly(TRUE);
            		$imageObj->keepAspectRatio(TRUE);
    				$imageObj->keepFrame(TRUE);
    				$h=$imageObj->getOriginalHeight();
            		$w=$imageObj->getOriginalWidth();
            		if ($h<=$w){
            			$height=$size;
            			$widht=$w/$h*$height;
            			$imageObj->resize($widht,$height);
            		}else{
            			$widht=$size;
            			$height=$h/$w*$widht;
            			$imageObj->resize($widht,$height);
            		}
            		$imageObj->save($pathBaseDir);
				} catch (Exception $e) {
				}
		}
		return $this->getPlaceholder();
    }
    protected function setObj($obj)
    {
        $this->_obj = $obj;
        return $this;
    }

    protected function getObj()
    {
        return $this->_obj;
    }
    protected function setPlaceholder($placeholder)
    {
        $this->_placeholder = $placeholder;
        return $this;
    }

    protected function getPlaceholder()
    {
        return $this->_placeholder;
    }
    protected function setPathname($pathname)
    {
        $this->_pathname = $pathname;
        return $this;
    }

    protected function getPathname()
    {
        return $this->_pathname;
    }
}