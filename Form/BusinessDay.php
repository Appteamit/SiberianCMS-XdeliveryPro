<?php

class Xdelivery_Form_BusinessDay extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/business-day"))
            ->setAttrib("id", "form-add-xdelivery");
        
        self::addClass("create", $this); 

        $this->addSimpleHidden("id");
        $value_id = $this->addSimpleHidden("value_id");
        $value_id->setRequired(true);

        $this->addSimpleCheckbox('monday', p__('xdelivery', 'Monday'));
        $this->addSimpleCheckbox('tuesday', p__('xdelivery', 'Tuesday'));
        $this->addSimpleCheckbox('wednesday', p__('xdelivery', 'Wednesday'));
        $this->addSimpleCheckbox('thursday', p__('xdelivery', 'Thursday'));
        $this->addSimpleCheckbox('friday', p__('xdelivery', 'Friday'));
        $this->addSimpleCheckbox('saturday', p__('xdelivery', 'Saturday'));
        $this->addSimpleCheckbox('sunday', p__('xdelivery', 'Sunday'));
 

   }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }

   
}