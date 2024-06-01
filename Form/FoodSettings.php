<?php

class Xdelivery_Form_FoodSettings extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/foodeditpost"))
            ->setAttrib("id", "form-add-settings");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $id = $this->addSimpleHidden("id");

    //    $this->addSimpleText('delivery_cost', p__('xdelivery', 'Delivery Cost'))->setRequired(true)->setValue(0);

        $this->addSimpleCheckbox('enable_to_deliver', p__('xdelivery', 'Enable Delivery'));

        $this->addSimpleCheckbox('enable_to_pickup', p__('xdelivery', 'Enable Pickup'));

        $h11 = p__("xdelivery", "Delivery Settings");
        $helpText11 = '<div class="col-md-12 text-center"><div  class="alert alert-warning">'.  $h11 .'</div></div>';
        $this->addSimpleHtml("helpText11", $helpText11);

        $hl = p__("xdelivery", "Enable Time To Deliver Option");
        $p1 = p__("xdelivery", "Enable this option for customers to be able to select a specific time to deliver the products based on Delivery/Pickup Hours");
        $helpText1 = '<div class="col-md-12"><div  class="alert alert-info">'.  $hl .'</div><p>'. $p1 .'</p></div>';
        $this->addSimpleHtml("helpText1", $helpText1);

        $this->addSimpleCheckbox('enable_time_to_deliver', p__('xdelivery', 'Enable Time To Deliver'));
        
        $this->addSimpleSelect('break_down_times_deliver', p__('xdelivery', 'Break Down Delivery Times every'), [
            '5' => p__('xdelivery', '5 Minutes'),
            '10' => p__('xdelivery', '10 Minutes'),
            '20' => p__('xdelivery', '20 Minutes'),
            '30' => p__('xdelivery', '30 Minutes'),
            '40' => p__('xdelivery', '40 Minutes'),
            '50' => p__('xdelivery', '50 Minutes'),
            '60' => p__('xdelivery', '60 Minutes'),
            '90' => p__('xdelivery', '1.5 Hours'),
            '120' => p__('xdelivery', '2 Hours'),
            '150' => p__('xdelivery', '2.5 Hours'),
            '180' => p__('xdelivery', '3 Hours'),
        ]);

        $h2 = p__("xdelivery", "Enable Date To Deliver Option");
        $p2 = p__("xdelivery", "Enable this option for customers to be able to select a specific time to deliver the products based on Delivery/Pickup Hours");
        $helpText2 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h2 .'</div><p>'. $p2 .'</p></div>';
        $this->addSimpleHtml("helpText2", $helpText2);

        $this->addSimpleCheckbox('enable_date_to_deliver', p__('xdelivery', 'Enable Date To Deliver'));

        $this->addSimpleText('days_up_to_deliver', p__('xdelivery', 'Up to (Days)'))->setRequired(true)->setValue(7);


        $h21 = p__("xdelivery", "Pickup Settings");
        $helpText21 = '<div class="col-md-12 text-center"><div  class="alert alert-warning">'.  $h21 .'</div></div>';
        $this->addSimpleHtml("helpText21", $helpText21);

        $h3 = p__("xdelivery", "Enable Time To Pickup Option");
        $p3 = p__("xdelivery", "Enable this option for customers to be able to select pickup option:");
        $helpText3 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h3 .'</div><p>'. $p3 .'</p></div>';
        $this->addSimpleHtml("helpText3", $helpText3);

        $this->addSimpleCheckbox('enable_time_to_pickup', p__('xdelivery', 'Enable Time To Pickup'));
        
        $this->addSimpleSelect('break_down_times_pickup', p__('xdelivery', 'Break Down Pickup Times every'), [
            '5' => p__('xdelivery', '5 Minutes'),
            '10' => p__('xdelivery', '10 Minutes'),
            '20' => p__('xdelivery', '20 Minutes'),
            '30' => p__('xdelivery', '30 Minutes'),
            '40' => p__('xdelivery', '40 Minutes'),
            '50' => p__('xdelivery', '50 Minutes'),
            '60' => p__('xdelivery', '60 Minutes'),
            '90' => p__('xdelivery', '1.5 Hours'),
            '120' => p__('xdelivery', '2 Hours'),
            '150' => p__('xdelivery', '2.5 Hours'),
            '180' => p__('xdelivery', '3 Hours'),
        ]);

        $h4 = p__("xdelivery", "Enable Date To Pickup Option");
        $p4 = p__("xdelivery", "Enable this option for customers to be able to select a specific date to pickup the products based on Days store is opened");
        $helpText4 = '<div class="col-md-12"><div  class="alert alert-info">'.  $h4 .'</div><p>'. $p4 .'</p></div>';
        $this->addSimpleHtml("helpText4", $helpText4);

        $this->addSimpleCheckbox('enable_date_to_pickup', p__('xdelivery', 'Enable Date To Pickup'));

        $this->addSimpleText('days_up_to_pickup', p__('xdelivery', 'Up to (Days)'))->setRequired(true)->setValue(7);
 
   }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>
 