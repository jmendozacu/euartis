<?php

class Magestore_Showroom_Helper_Ajax extends Mage_Core_Helper_Abstract
{
    const NATIVE_TEMPLATE_KEY = 'native_catalog_product_list_template';
	const IS_AJAX_KEY = '_is_ajax';
    
    public function isRightRoute()
    {
        return in_array(Mage::app()->getRequest()->getRouteName(), array(
                            'catalog',
                            'catalogsearch',
                            'tag',
                            # etc
                        ));
    }
	
	/**
     * 
     * @return AW_Ajaxcatalog_Helper_Tools_Simpledom
     */
    public function getSimpleDOM()
    {
        return Mage::helper('showroom/ajax_simpledom');
    }
    
    /**
     * Store native template in registry
     * @param string $template
     * @return AW_Ajaxcatalog_Helper_Data 
     */
    public function setNativeTemplate($template)
    {
        Mage::register(self::NATIVE_TEMPLATE_KEY, $template);
        return $this;
    }
    
    /**
     * Retrives native template from registry
     * @return string
     */
    public function getNativeTemplate()
    {
       return Mage::registry(self::NATIVE_TEMPLATE_KEY); 
    }
    

    
	/**
     * Compare param $version with magento version
     * 
     * @param string $version
     * @return boolean
     */
	public function checkVersion($version)
	{
		return version_compare(Mage::getVersion(), $version, '>=');
	}	    
}