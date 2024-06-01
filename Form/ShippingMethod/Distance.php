<?php

class Xdelivery_Form_ShippingMethod_Distance extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editshippingmethod"))
            ->setAttrib("id", "form-add-shipping");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("id");

        $hl6 = p__("xdelivery", "Distance Based");
        $helpText6 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl6 .'</div></div>';
        $this->addSimpleHtml("helper_text6", $helpText6);
        $this->addSimpleHtml("helper_text7", '<div class="col-md-4"></div><div class="col-md-8"><a class="pull-right" target="_blank" href="/xdelivery/settings/shippingdistance">'. p__("xdelivery", "Click here for Manage Cost").'</a></div>');

        $method_type = $this->addSimpleHidden("method_type")->setValue('distance');

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Status'));        
     
        /** label name */
        $label_name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setValue('Distance Based');
 
           
        $hl8 = p__("xdelivery", "Important: First make sure that you have configured the Google API keys correctly.");
        $helpText8 = '<div class="col-md-12"><div  class="alert alert-warning">'.  $hl8 .'</div></div>';
        $this->addSimpleHtml("helper_text8", $helpText8);

         
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>