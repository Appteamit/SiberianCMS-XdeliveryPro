<?php

class Xdelivery_Form_PaymentMethod_Paypal extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editpaymentmethod"))
            ->setAttrib("id", "form-add-payment");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $id = $this->addSimpleHidden("id");
        $method_type = $this->addSimpleHidden("method_type")->setValue('paypal');

        $hl7 = p__("xdelivery", "PayPal");
        $helpText7 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl7 .'</div></div>';
        $this->addSimpleHtml("helper_text7", $helpText7);
 
        $this->addSimpleCheckbox('status', p__('xdelivery', 'Active'));
        
        /** label name */
        $name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setRequired(true)->setValue('PayPal');

        /** desciption */
        $desciption = $this->addSimpleTextarea('desciption',  p__('xdelivery', 'Desciption'))->setRequired(true)->setValue('Accept payment from customers with PayPal account');

        $paypal_payment_mode = $this->addSimpleSelect('payment_mode',  p__('xdelivery', 'Payment Mode'),
            [
            'sandbox' => p__('xdelivery', 'Sandbox'),
            'live' => p__('xdelivery', 'Live'),
        ]
        );
        $paypal_payment_mode->addClass("select_payment_paypal");
        $paypal_payment_mode->setRequired(true);

        $username = $this->addSimpleText("username", p__('xdelivery', "Username"));
        $signature = $this->addSimpleText("signature", p__('xdelivery', "Signature"));
        $password = $this->addSimpleText("password", p__('xdelivery', "Password"));

        $sandboxusername = $this->addSimpleText("sandboxusername", p__('xdelivery', "Sandbox Username"));
        $sandboxsignature = $this->addSimpleText("sandboxsignature",p__('xdelivery', "Sandbox Signature"));
        $sandboxpassword = $this->addSimpleText("sandboxpassword", p__('xdelivery', "Sandbox Password"));
       
       
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>