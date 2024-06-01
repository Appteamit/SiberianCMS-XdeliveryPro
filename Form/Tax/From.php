<?php

class Xdelivery_Form_Tax_From extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/settings/edittax"))
            ->setAttrib("id", "form-add-tax")
            ->addNav("nav-add-taxes", "Submit");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $id = $this->addSimpleHidden("id");

         $this->addSimpleCheckbox('status', p__('xdelivery', 'Status'));
        
        /** name */
        $name = $this->addSimpleText('name', p__('xdelivery', 'Name'))->setRequired(true);

        /** tax_rate  */
        $amount = $this->addSimpleText('tax_rate', p__('xdelivery', 'Rate (%)'))->setRequired(true)->setValue(0);
         
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>
 