<?php
class Magestore_Showroom_Block_Adminhtml_Showroom_Renderer_Config extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		return $element->getElementHtml().'<p class="note"><span><a id="link_view_'.$element->getId().'" href="javascript:void(0)" target="_bank">What is this?</a><img id="image_view_'.$element->getId().'" src="'.Mage::getBaseUrl('media').'/showroom/'.$element->getId().'.png" style="position: absolute; top: 1798px; left: 530px; display: none; "><script type="text/javascript"> var tip = new Tooltip("link_view_'.$element->getId().'", "image_view_'.$element->getId().'");</script><br></span></p>';
	}
}