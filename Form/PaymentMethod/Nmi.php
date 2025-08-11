<?php

class Xdelivery_Form_PaymentMethod_Nmi extends Siberian_Form_Abstract
{

    public function init()
    {
        parent::init();

        $this
            ->setAction(__path("/xdelivery/settings/editpaymentmethod"))
            ->setAttrib("id", "form-add-payment");

        self::addClass("create", $this);

        $value_id = $this->addSimpleHidden("value_id");
        $id = $this->addSimpleHidden("id");
        $method_type = $this->addSimpleHidden("method_type")->setValue('nmi');

        $hl8 = p__("xdelivery", "Nmi");
        $helpText8 = '<div class="col-md-12"><div  class="alert alert-info">' .  $hl8 . '</div></div>';
        $this->addSimpleHtml("helper_text8", $helpText8);

        /* $hl1 = p__("xdelivery", "API CREDENTIALS settings is available in Menu -> Payment Gateways -> Nmi");
        $helpText1 = '<div class="col-md-12"><div class="alert alert-warning">1. '.  $hl1 .' .</div></div>';
        $this->addSimpleHtml("helper_text_home", $helpText1);

       */
        /** label name */
        $name = $this->addSimpleText('label_name', p__('xdelivery', 'Label Name'))->setRequired(true)->setRequired(true)->setValue('Nmi');

        /** desciption */
        $desciption = $this->addSimpleTextarea('desciption',  p__('xdelivery', 'Desciption'))->setRequired(true)->setValue('Pay using credit card');

        // $publishable_key = $this->addSimpleText("publishable_key", p__('xdelivery', "Publishable key"))->setRequired(true);
        $secret_key = $this->addSimpleText("secret_key", p__('xdelivery', "Secret key"))->setRequired(true);
        // Processing fee in % 
        $processing_fee = $this->addSimpleText("processing_fee", p__('xdelivery', "Processing fee in %"))->setRequired(true)->setValue(0);
        // is_test_mode
        $is_test_mode = $this->addSimpleCheckbox("is_test_mode", p__('xdelivery', "Test mode"))->setValue(0);
        $this->addSimpleCheckbox('status', p__('xdelivery', 'Active'));
    }

    public function setElementValueById($id, $value, $required = false)
    {
        $element = $this->getElement($id)->setValue($value);
        if ($required) {
            $element->setRequired(true);
        }
    }
}
