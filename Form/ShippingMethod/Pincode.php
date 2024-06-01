<?php

class Xdelivery_Form_ShippingMethod_Pincode extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editshippingmethod"))
            ->setAttrib("id", "form-add-shipping");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("id");

        $hl6 = p__("xdelivery", "Pincode Based");
        $helpText6 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl6 .'</div></div>';
        $this->addSimpleHtml("helper_text6", $helpText6);

        $method_type = $this->addSimpleHidden("method_type")->setValue('pincode');

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Status'));        
 
        /** label name */
        $label_name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setValue('Pincode Based');
 
   
         
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>