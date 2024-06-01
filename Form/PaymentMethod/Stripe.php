<?php

class Xdelivery_Form_PaymentMethod_Stripe extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editpaymentmethod"))
            ->setAttrib("id", "form-add-payment");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $id = $this->addSimpleHidden("id");
        $method_type = $this->addSimpleHidden("method_type")->setValue('stripe');

        $hl8 = p__("xdelivery", "Stripe");
        $helpText8 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl8 .'</div></div>';
        $this->addSimpleHtml("helper_text8", $helpText8);
 
       /* $hl1 = p__("xdelivery", "API CREDENTIALS settings is available in Menu -> Payment Gateways -> Stripe");
        $helpText1 = '<div class="col-md-12"><div class="alert alert-warning">1. '.  $hl1 .' .</div></div>';
        $this->addSimpleHtml("helper_text_home", $helpText1);

       */
        /** label name */
        $name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setRequired(true)->setValue('Stripe');

        /** desciption */
        $desciption = $this->addSimpleTextarea('desciption',  p__('xdelivery', 'Desciption'))->setRequired(true)->setValue('Pay using credit card');

        $publishable_key = $this->addSimpleText("publishable_key", p__('xdelivery', "Publishable key"))->setRequired(true);
        $secret_key = $this->addSimpleText("secret_key", p__('xdelivery', "Secret key"))->setRequired(true);

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Active')); 

  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>
 