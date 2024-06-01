<?php

class Xdelivery_Form_Settings extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/editpost"))
            ->setAttrib("id", "form-add-settings");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("id");

        $this->addSimpleSelect('app_service_type', p__('xdelivery', 'App Service Type'), [
            'food' => p__('xdelivery', 'Food App (Select Date and Time slot)'),
            'other' => p__('xdelivery', 'Other (Not select Date and Time slot)'),
        ]);

        $this->addSimpleSelect('home_screen', p__('xdelivery', 'Home Screen'), [
            'store' => p__('xdelivery', 'Store'),
           /* 'product' => p__('xdelivery', 'Product'),*/
            'category' => p__('xdelivery', 'Category'),
        ]);

        $this->addSimpleSelect('product_design', p__('xdelivery', 'Product Design'), [
            'list' => p__('xdelivery', 'List'),
            'grid' => p__('xdelivery', 'Grid'),
        ]);
        
        $this->addSimpleSelect('category_design', p__('xdelivery', 'Category Design'), [
            'list' => p__('xdelivery', 'List'),
            'grid' => p__('xdelivery', 'Grid'),
        ]);

        $this->addSimpleCheckbox('enable_booking', p__('xdelivery', 'Enable orders'));

        $this->addSimpleCheckbox('enable_acceptance_rejection', p__('xdelivery', 'Enable acceptance / rejection orders'));

        $this->addSimpleCheckbox('enable_retrun', p__('xdelivery', 'Enable Return'));
        $this->addSimpleSlider('return_within', p__('xdelivery', "Return Within"), array(
            'min' => 1,
            'max' => 365,
            'step' => 1,
            'unit' => p__('xdelivery', 'Days')
        ), true);

        $this->addSimpleCheckbox('enable_qrscan', p__('xdelivery', 'Enable Product Scan'));
      /*  $this->addSimpleCheckbox('review_rating_enable', p__('xdelivery', 'Review & Rating'));
        $this->addSimpleCheckbox('auto_approval_review', p__('xdelivery', 'Auto Approval Review'));
        $this->addSimpleCheckbox('send_invoice_email', p__('xdelivery', 'Send Invoice Email'));
        $this->addSimpleCheckbox('enable_tax', p__('xdelivery', 'Enable Tax'));*/
        $this->addSimpleCheckbox('enable_print_customer_details', p__('xdelivery', 'Print customer details on order slip'));
        $this->addSimpleCheckbox('order_status_push', p__('xdelivery', 'Enable push notification for order status'));
        $this->addSimpleCheckbox('is_enable_email', p__('xdelivery', 'Enable Email notification for order status'));
        
        $hl = p__("xdelivery", "Cart Settings");
        $helpText1 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl .'</div></div>';
        $this->addSimpleHtml("helpText1", $helpText1);

        $this->addSimpleCheckbox('enable_addtocart', p__('xdelivery', 'Enable Add To Cart'));
        $this->addSimpleCheckbox('enable_save_later', p__('xdelivery', 'Enable Save Later'));
        $this->addSimpleCheckbox('hide_price', p__('xdelivery', 'Hide price'));
        
        $this->addSimpleText('min_qty_shopping_cart',  p__('xdelivery', 'Mimimum Qty In Shopping Cart'))->setRequired(true);
        $this->addSimpleText('max_qty_shopping_cart', p__('xdelivery', 'Maximum Qty In Shopping Cart'))->setRequired(true);

        $this->addSimpleText('max_qty_per_product', p__('xdelivery', 'Maximum Qty Per Product'))->setRequired(true);

        $this->addSimpleText('min_order_value', p__('xdelivery', 'Mimimum Order Value'))->setRequired(true);
        $this->addSimpleText('max_order_value', p__('xdelivery', 'Maximum Order Value'))->setRequired(true);
        $this->addSimpleCheckbox('taxes_to_addons', p__('xdelivery', 'Apply taxes to addons'));
        $this->addSimpleCheckbox('taxes_enable', p__('xdelivery', 'Taxes enable'));
        $this->addSimpleSelect('discount_tax_calculation', p__('xdelivery', 'Discount calculation'), [
            '1' => p__('xdelivery', 'Discount before Tax'),
            '2' => p__('xdelivery', 'Discount after Tax'),
        ]);
        $this->addSimpleCheckbox('enable_tax_with_product', p__('xdelivery', 'Taxes With Product'));

        $h2 = p__("xdelivery", "Delivery Settings");
        $helpText2 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h2 .'</div></div>';
        $this->addSimpleHtml("helpText2", $helpText2);

        $this->addSimpleText('delivery_time', p__('xdelivery', 'Average delivery time (In Min)'))->setRequired(true);
        $this->addSimpleText('pick_up_time', p__('xdelivery', 'Average Pick Up time (In Min)'))->setRequired(true);
        $this->addSimpleCheckbox('enable_tips', p__('xdelivery', 'Gratuity enable'));
       
        $h3 = p__("xdelivery", "Currency options");
        $p3 = p__("xdelivery", "The following options affect how prices are displayed on the frontend");
        $helpText3 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h3 .'</div>'.  $p3 .'</div>';
        $this->addSimpleHtml("helpText3", $helpText3);

        $this->addSimpleSelect('currency_position', p__('xdelivery', 'Currency position'), [
            'left' => p__('xdelivery', 'Left'),
            'right' => p__('xdelivery', 'Right'),
            'left_with_space' => p__('xdelivery', 'Left with space'),
            'right_with_space' => p__('xdelivery', 'Right with space'),
        ]);
        $this->addSimpleText('decimal_separator', p__('xdelivery', 'Decimal separator'));
        $this->addSimpleText('thousand_separator', p__('xdelivery', 'Thousand separator'));
        $this->addSimpleText('number_of_decimals', p__('xdelivery', 'Number of decimals'));
     
        $h4 = p__("xdelivery", "Date & Time Format");
        $helpText4 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h4 .'</div></div>';
        $this->addSimpleHtml("helpText4", $helpText4);

        $this->addSimpleSelect('time_format', p__('xdelivery', 'Time Format'), [
            'g:i A' => p__('xdelivery', '12-hour format'),
            'H:i' => p__('xdelivery', '24-hour format'),
        ])->setRequired(true);;

        $this->addSimpleSelect('date_format', p__('xdelivery', 'Date Format'), [
            'Y-m-d' => date("Y-m-d"),
            'm-d-Y' => date("m-d-Y"),
            'd-m-Y' => date("d-m-Y"),
            'd M, Y' => date("d M, Y"),
            'M d, Y' => date("M d, Y"),
            'F j, Y' => date("F j, Y"),
            'l, F jS, Y' => date("l, F jS, Y")
        ])->setRequired(true);


        $h5 = p__("xdelivery", "External Field");
        $helpText5 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h5 .'</div></div>';
        $this->addSimpleHtml("helpText5", $helpText5);

        $this->addSimpleSelect('company_address', p__('xdelivery', 'Company Address'), [
            '0' => p__('xdelivery', 'Disable'),
            '1' => p__('xdelivery', 'Optional'),
            '2' => p__('xdelivery', 'Required'),
        ]);
        $this->addSimpleSelect('sdi', p__('xdelivery', 'SDI'), [
            '0' => p__('xdelivery', 'Disable'),
            '1' => p__('xdelivery', 'Optional'),
            '2' => p__('xdelivery', 'Required'),
        ]);
        $this->addSimpleSelect('pec', p__('xdelivery', 'PEC'), [
            '0' => p__('xdelivery', 'Disable'),
            '1' => p__('xdelivery', 'Optional'),
            '2' => p__('xdelivery', 'Required'),
        ]);
        $this->addSimpleSelect('cod_fiscale', p__('xdelivery', 'Cod Fiscale'), [
            '0' => p__('xdelivery', 'Disable'),
            '1' => p__('xdelivery', 'Optional'),
            '2' => p__('xdelivery', 'Required'),
        ]);



        $h6 = p__("xdelivery", "Whatsender Settings");
        $helpText6 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h6 .'</div></div>';
        $this->addSimpleHtml("helpText6", $helpText6);
        $this->addSimpleCheckbox('is_enable_whatsender', p__('xdelivery', 'Enable Whatsender Settings'));
        $this->addSimpleText('whatsender_key', p__('xdelivery', 'Token'))->setRequired(false);        

        $h7 = p__("xdelivery", "To retrieve your token, connect to https://api2.whatsender.it from the API");
        $helpText7 = '<div class="col-md-12"><div  class="alert">'.  $h7 .'</div></div>';
        $this->addSimpleHtml("helpText7", $helpText7);

        // sms
        $h8 = p__("xdelivery", "Twilio Settings");
        $helpText8 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h8 .'</div></div>';
        $this->addSimpleHtml("helpText8", $helpText8);
        $this->addSimpleCheckbox('is_enable_sms', p__('xdelivery', 'Enable Twilio Settings'));
        $this->addSimpleText('twillio_sid', p__('xdelivery', 'Account SID'))->setRequired(false);
        $this->addSimpleText('twillio_auth_token', p__('xdelivery', 'Auth Token'))->setRequired(false);
        $this->addSimpleText('twillio_sim_id', p__('xdelivery', 'Phone Sim Id'))->setRequired(false);

   }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>