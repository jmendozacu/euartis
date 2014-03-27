<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 */

class VladimirPopov_WebForms_Helper_Data extends Mage_Core_Helper_Abstract{
	
	public function getRealIp()
	{
		 $ip = false;
		 if(!empty($_SERVER['HTTP_CLIENT_IP']))
		 {
			  $ip = $_SERVER['HTTP_CLIENT_IP'];
		 }
		 if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		 {
			  $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			  if($ip)
			  {
				   array_unshift($ips, $ip);
				   $ip = false;
			  }
			  for($i = 0; $i < count($ips); $i++)
			  {
				   if(!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i]))
				   {
						if(version_compare(phpversion(), "5.0.0", ">="))
						{
							 if(ip2long($ips[$i]) != false)
							 {
								  $ip = $ips[$i];
								  break;
							 }
						}
						else
						{
							 if(ip2long($ips[$i]) != - 1)
							 {
								  $ip = $ips[$i];
								  break;
							 }
						}
				   }
			  }
		 }
		 return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	} 
	
	public function captchaAvailable(){
		if(class_exists('Zend_Service_ReCaptcha')
			&& Mage::getStoreConfig('webforms/captcha/public_key')
			&& Mage::getStoreConfig('webforms/captcha/private_key')
		) return true;
		return false;
	}
	
	public function getCaptcha(){
		$pubKey = Mage::getStoreConfig('webforms/captcha/public_key');
		$privKey = Mage::getStoreConfig('webforms/captcha/private_key');
		if($pubKey && $privKey){
			$recaptcha = Mage::getModel('webforms/captcha');
			$recaptcha->setPublicKey($pubKey);
			$recaptcha->setPrivateKey($privKey);
			
			$theme = Mage::getStoreConfig('webforms/captcha/theme');
			if($theme) $recaptcha->setOption('theme',$theme);
			
			$language = Mage::getStoreConfig('webforms/captcha/language');
			if($language) $recaptcha->setOption('lang',$language);
		}
		return $recaptcha;
	}
		
}
?>