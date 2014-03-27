<?php
/**
 * Feel free to contact me via Facebook
 * http://www.facebook.com/rebimol
 *
 *
 * @author 		Vladimir Popov
 * @copyright  	Copyright (c) 2011 Vladimir Popov
 */

class VladimirPopov_WebForms_Model_Fields extends Mage_Core_Model_Abstract{
	
	protected $img_regex = '/{{img ([\w\/\.]+)}}/';

	public function _construct()
	{
		parent::_construct();
		$this->_init('webforms/fields');
	}
	
	public function getFieldTypes()
	{
		$types = new Varien_Object(array(
			"text" 				=> Mage::helper('webforms')->__('Text'),
			"email" 			=> Mage::helper('webforms')->__('Text / E-mail'),
			"number" 			=> Mage::helper('webforms')->__('Text / Number'),
			"url" 				=> Mage::helper('webforms')->__('Text / URL'),
			"textarea"			=> Mage::helper('webforms')->__('Textarea'),
			"select" 			=> Mage::helper('webforms')->__('Select'),
			"select/radio" 		=> Mage::helper('webforms')->__('Select / Radio'),
			"select/checkbox" 	=> Mage::helper('webforms')->__('Select / Checkbox'),
			"select/contact" 	=> Mage::helper('webforms')->__('Select / Contact'),
			"date" 				=> Mage::helper('webforms')->__('Date'),
			"datetime" 			=> Mage::helper('webforms')->__('Date / Time'),
			"stars" 			=> Mage::helper('webforms')->__('Star Rating'),
			"file" 				=> Mage::helper('webforms')->__('File'),
			"image" 			=> Mage::helper('webforms')->__('Image'),
			"html" 				=> Mage::helper('webforms')->__('Html Block'),
		));
		
		// add more field types
		Mage::dispatchEvent('webforms_fields_types', array('types' => $types));

		return $types->getData();
		
	}
	
	public function getSelectOptions(){
		$field_value = $this->getValue();
		$options = explode("\n",$field_value);
		$options = array_map('trim',$options);
		$select_options = array();
		foreach($options as $o){
			if($this->getType() == 'select/contact'){
				$contact = $this->getContactArray($o);
				$o = $contact['name'];
			} 
			$select_options[$this->getCheckedOptionValue($o)] = $this->getCheckedOptionValue($o);
		} 
		return $select_options;
	}
	
	public function getResultsOptions(){
		$query = $this->getResource()->getReadConnection()
			->select('value')
			->from($this->getResource()->getTable('webforms/results_values'),array('value'))
			->where('field_id = '.$this->getId())
			->order('value asc')
			->distinct();
		$results = $this->getResource()->getReadConnection()->fetchAll($query);
		$options = array();
		foreach($results as $result){
			$options[$result['value']] =  $result['value'];
		}
		return $options;
	}
	
	public function getSizeTypes(){
		$types = new Varien_Object(array(
			"standard" => Mage::helper('webforms')->__('Standard'),
			"wide" => Mage::helper('webforms')->__('Wide'),
		));
		
		// add more size types
		Mage::dispatchEvent('webforms_fields_size_types', array('types' => $types));

		return $types->getData();
		
	}
	
	public function getAllowedExtensions(){
		if($this->getType() == 'image')
			return array('jpg','jpeg','gif','png');
		if($this->getType() == 'file'){
			$allowed_extensions = explode("\n",trim($this->getValue()));
			$allowed_extensions = array_map('trim',$allowed_extensions);
			$allowed_extensions = array_map('strtolower',$allowed_extensions);
			$filtered_result = array();
			foreach($allowed_extensions as $ext){
				if(strlen($ext)>0){
					$filtered_result[]= $ext;
				}
			}
			return $filtered_result;
		}
		return;
	}
	
	public function getStarsCount(){
		//return default value
		$options = $this->getOptionsArray();
		$value = (int)$options[0]['value'];
		if($value>0) return $value;
		return 5;
	}
	
	public function getDateType(){
		$type = "medium";
		$allowed_types = array('short','medium','long','full');
		$value = trim($this->getValue());
		if(in_array($value,$allowed_types)){
			$type = $value;
		}
		return $type;
	}
	
	public function getDateFormat(){
		$format = Mage::app()->getLocale()->getDateFormat($this->getDateType());
		if($this->getType() == 'datetime'){
			$format = Mage::app()->getLocale()->getDateTimeFormat($this->getDateType());
		}
		return $format;
	}
	
	public function getDateStrFormat(){
		$str_format =  Varien_Date::convertZendToStrftime($this->getDateFormat(), true, true);
		return $str_format;
	}
	
	public function getDbDateFormat(){
		$format = "Y-m-d";
		if($this->getType() == 'datetime'){
			$format = "Y-m-d H:i:s";
		}
		return $format;
	}
	
	public function formatDate($value){
		if(strlen($value)>0){
			$format = $this->getDateStrFormat();
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
			}				
			return strftime($format,strtotime($value));
		}
		return;
	}
	
	public function isCheckedOption($value){
		if(substr($value,0,1) == '^')
			return true;
		return false;
	}
	
	public function isNullOption($value){
		if(substr($value,0,2) == '^^')
			return true;
		return false;
	}
	
	public function getCheckedOptionValue($value){
		$value = preg_replace($this->img_regex,"",$value);
		if($this->isNullOption($value))
			return trim(substr($value,2));
		if($this->isCheckedOption($value))
			return trim(substr($value,1));
		return trim($value);
	}
	
	public function getOptionsArray(){
		$options = array();
		$values = explode("\n",$this->getValue());
		foreach($values as $val){
			$image_src = false;
			$matches = array();
			preg_match($this->img_regex,$val,$matches);
			if(!empty($matches[1])){
				$image_src = Mage::app()->getStore()->getUrl(dirname('media/'.$matches[1])).basename($matches[1]);
			}
			$options[] = array(
				'value' => $this->getCheckedOptionValue($val),
				'null' => $this->isNullOption($val),
				'checked' => $this->isCheckedOption($val),
				'image_src' => $image_src,
			);
		}
		return $options;
	}
	
	public function getContactArray($value){
		preg_match('/(\w.+) <([^<]+?)>/',$value,$matches);
		if(!empty($matches[1]) && !empty($matches[2]))
			return array("name" => trim($matches[1]) , "email" => trim($matches[2]));
		return false;
	}
	
	public function getContactValueById($id){
		$options = $this->getOptionsArray();
		if(!empty($options[$id]['value']))
			return $options[$id]['value'];
		return false;
	}
	
	public function toHtml()
	{
		$html="";
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$filter = new Varien_Filter_Template_Simple();
		if($customer->getDefaultBillingAddress()){
			foreach($customer->getDefaultBillingAddress()->getData() as $key=>$value)
				$filter->setData($key,$value);
		}
		$filter->setData('firstname',$customer->getFirstname());
		$filter->setData('lastname',$customer->getLastname());
		$filter->setData('email',$customer->getEmail());
		
		// apply custom filter
		Mage::dispatchEvent('webforms_fields_tohtml_filter',array('filter'=>$filter));
		
		$field_id="field[".$this->getId()."]";
		$field_name = $field_id;
		$field_value = $filter->filter($this->getValue());
		$field_type = $this->getType();
		$field_class="input-text";
		$field_style = "";
		$validate = "";
		$show_time = 'false';
		if($this->getRequired())
			$field_class.=" required-entry";
		if($field_type == "email")
			$field_class.= " validate-email";
		if($field_type == "number")
			$field_class.= " validate-number";
		if($field_type == "url")
			$field_class.= " validate-clean-url";
		if($this->getCssClass()){
			$field_class.=' '.$this->getCssClass();
		}
		if($this->getData('validate_length_min') || $this->getData('validate_length_max')){
			$field_class.=' validate-length';
		}
		if($this->getData('validate_length_min')){
			$field_class.=' minimum-length-'.$this->getData('validate_length_min');
		}
		if($this->getData('validate_length_max')){
			$field_class.=' maximum-length-'.$this->getData('validate_length_max');
		}
		if($this->getData('validate_regex')){
			$field_class.=' validate-field-'.$this->getId();
		}
		if($this->getCssStyle()){
			$field_style = $this->getCssStyle();
		}
		switch($field_type){
			case 'textarea': 
				$html = "<textarea name='$field_name' id='$field_id' class='$field_class' style='$field_style'>$field_value</textarea>";
				break;
			case 'select': 
				$options = $this->getOptionsArray();
				$html = "<select name='$field_name' id='$field_id' class='$field_class' style='$field_style'>";
				foreach($options as $option){
					$checked = false;
					if($option["checked"]){
						$checked = 'selected';
					}
					if(!empty($option["value"])){
						$value = htmlspecialchars($option["value"]);
						if($option['null']){
							$value = '';
						}
						$html.= "<option value=\"".$value."\" $checked>$option[value]</option>";
					}
				}
				$html.="</select>";
				break;
			case 'select/contact': 
				$options = $this->getOptionsArray();
				$html = "<select name='$field_name' id='$field_id' class='$field_class' style='$field_style'>";
				foreach($options as $i=>$option){
					$checked = false;
					if($option["checked"]){
						$checked = 'selected';
					}
					if(!empty($option["value"])){
						$contact = $this->getContactArray($option["value"]);
						if($contact)
							$html.= "<option value=\"$i\" $checked>$contact[name]</option>";
					}
				}
				$html.="</select>";
				break;
			case 'select/radio':
				$options = $this->getOptionsArray();
				$html= "<ul style='padding:10px'>";
				$field_class=  $this->getCssClass();
				foreach($options as $i=>$option){
					$checked = false;
					if($option["checked"]){
						$checked = 'checked';
					}
					if($this->getRequired() && $i==(count($options)-1)){
						$validate = "validate-one-required-by-name";
						if($this->getData('validate_regex')){
							$validate.= ' validate-field-'.$this->getId();
						}
					}
					if(!empty($option["value"])){
						$label = $option["value"];
						if($option["image_src"]){
							$label = "<img src='$option[image_src]'/>";
						}
						$html.= "<li class='control'><input style='float:left' type='radio' name='".$field_name."[]' id='$field_id.$i' value=\"".htmlspecialchars($option["value"])."\" class='radio $validate' $checked/><label for='$field_id.$i' class='$field_class' style='$field_style'>$label</label></li>";
					}
				}
				$html.="</ul>";
				break;
			case 'select/checkbox':
				$options = $this->getOptionsArray();
				$html= "<ul style='padding:10px'>";
				$field_class=  $this->getCssClass();
				foreach($options as $i=>$option){
					$checked = false;
					if($option["checked"]){
						$checked = 'checked';
					}
					if($this->getRequired() && $i==(count($options)-1)){
						$validate = "validate-one-required-by-name";
						if($this->getData('validate_regex')){
							$validate.= ' validate-field-'.$this->getId();
						}
					}
					
					if(!empty($option["value"])){
						$label = $option["value"];
						if($option["image_src"]){
							$label = "<img src='$option[image_src]'/>";
						}
						$html.= "<li class='control'><input style='float:left' type='checkbox' name='".$field_name."[]' id='$field_id.$i' value=\"".htmlspecialchars($option["value"])."\" class='checkbox $validate' $checked/><label for='$field_id.$i' class='$field_class' style='$field_style'>$label</label></li>";
					}
				}
				$html.="</ul>";
				break;
			case 'stars':
				$startAt = ceil($this->getStarsCount()/2);
				$options = $this->getOptionsArray();
				if(!empty($options[1]) && (int)$options[1]['value']){
					$startAt = (int)$options[1]['value'];
					if((int)$options[1]['value'] > $this->getStarsCount()){
						$startAt = $this->getStarsCount();
					}
				}
				$html.= "<script>new Starry('$field_id',{name:'$field_name',showNull:false,startAt:$startAt,maxLength:".$this->getStarsCount()."})</script>";
				break; 
			case 'image': case 'file':
				$field_id = 'file_'.$this->getId();
				$field_name = $field_id;
				$html.= "<input type='file' name='$field_name' id='$field_id' class='$field_class' style='$field_style'/>";
				break;
			case 'html':
				$html.= $field_value;
				break;
			case 'datetime':
				$show_time= 'true';
			case 'date': case 'datetime':
				$html.= "<div class='webforms-calendar'>";
				$html.= "<input type='text' name='$field_name' id='$field_id' class='$field_class' style='$field_style' readonly='readonly'/>";
				$html.= "<div id='$field_id-trig' class='webforms-calendar-button' onclick='\$(\"$field_id\").click()'></div>";
				$html.= "</div>";
				$html.= "<script type='text/javascript'>Calendar.setup({
					inputField: '$field_id',
					ifFormat: '{$this->getDateStrFormat()}',
					showsTime: $show_time,
					button: '$field_id',
					align: 'Bl',
					singleClick: true
				});</script>";
				break;
			default: 
				$html ="<input type='text' name='$field_name' id='$field_id' class='$field_class' style='$field_style' value=\"".htmlspecialchars($field_value)."\"/>";
				break;
		}
		
		if($this->getData('validate_regex')){
			$validate_message = str_replace("'","\'",$this->getData('validate_message'));
			$html.="<script>Validation.add('validate-field-{$this->getId()}','{$validate_message}',function(v){var r = new RegExp('{$this->getData('validate_regex')}');return Validation.get('IsEmpty').test(v) || r.test(v);})</script>";
		}
		
		// apply custom field type
		$html_object = new Varien_Object(array('html'=>$html));
		Mage::dispatchEvent('webforms_fields_tohtml_html',array('field'=>$this,'html_object'=>$html_object));
		
		return $html_object->getHtml();
	}
	
	public function duplicate(){
		// duplicate field
		$field = Mage::getModel('webforms/fields')
			->setData($this->getData())
			->setId(null)
			->setName($this->getName().' '.Mage::helper('webforms')->__('(new copy)'))
			->setIsActive(false)
			->setCreatedTime(Mage::getSingleton('core/date')->gmtDate())
			->setUpdateTime(Mage::getSingleton('core/date')->gmtDate())
			->save();
		
		return $field;
	}
	
}
?>