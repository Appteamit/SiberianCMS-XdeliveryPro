<?php

class Xdelivery_Form_ShippingMethod_Flat extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editshippingmethod"))
            ->setAttrib("id", "form-add-shipping");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("id");

        $hl6 = p__("xdelivery", "Flat Rate");
        $helpText6 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl6 .'</div></div>';
        $this->addSimpleHtml("helper_text6", $helpText6);

        $method_type = $this->addSimpleHidden("method_type")->setValue('flat');

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Status'));
        
        /** label name */
        $label_name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setValue('Flat Rate');

        /** amount  */
        $amount = $this->addSimpleText('amount', p__('xdelivery', 'Cost'))->setRequired(true)->setValue(0);
        
         $hl8 = p__("xdelivery", "Important: This condition is applied, when records are not matched with the Distance Based and Free Shipping Method.");
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