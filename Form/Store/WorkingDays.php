<?php

class Xdelivery_Form_Store_WorkingDays extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/working/save"))
            ->setAttrib("id", "form-add-xdelivery");
        
        self::addClass("create", $this); 
        $this->addSimpleHidden("working_id");
        $this->addSimpleHidden("value_id");
        
        $this->addSimpleDatetimepicker(
            'opening_time', 
            p__('xdelivery', 'Opening time'), 
            false, 
            Siberian_Form_Abstract::TIMEPICKER
        )->setRequired(true);

        $this->addSimpleDatetimepicker(
            'closing_time', 
           p__('xdelivery', 'Closing time'), 
            false, 
            Siberian_Form_Abstract::TIMEPICKER
        )->setRequired(true);
        
        $this->addSimpleCheckbox('enable_delivery', p__('xdelivery', 'Enable delivery'));

        $this->addSimpleCheckbox('enable_pickup', p__('xdelivery', 'Enable pickup'));

    }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }

   
}