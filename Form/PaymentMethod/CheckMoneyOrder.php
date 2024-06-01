<?php

class Xdelivery_Form_PaymentMethod_CheckMoneyOrder extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editpaymentmethod"))
            ->setAttrib("id", "form-add-payment");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("id");

        $hl6 = p__("xdelivery", "Check/Money Order");
        $helpText6 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl6 .'</div></div>';
        $this->addSimpleHtml("helper_text6", $helpText6);

        $method_type = $this->addSimpleHidden("method_type")->setValue('check_money_order');

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Status'));
        /** label name */
        $label_name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setValue('Check/Money Order');
        /** desciption */
        $desciption = $this->addSimpleTextarea('desciption', p__('xdelivery', 'Desciption'))->setRequired(true)->setValue('Please send a check to our store');
        /** instruction */
        $instruction = $this->addSimpleTextarea('instruction', p__('xdelivery', 'Instruction'))->setRequired(true);
   
         
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>