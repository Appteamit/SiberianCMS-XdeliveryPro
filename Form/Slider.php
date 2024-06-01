<?php

class Xdelivery_Form_Slider extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/slider/add"))
            ->setAttrib("id", "form-add-slider")
            ->addNav("nav-add-xdelivery", "Submit");
        
        self::addClass("create", $this); 
    }


        /**
     * Special create/populate form
     *
     * @param array $values
     * @return Zend_Form
     */

    public function form($category) {

        $value_id = $this->addSimpleHidden("value_id");
        $slider_id = $this->addSimpleHidden("slider_id");

        /** Slider name */
        $slider_name = $this->addSimpleText('slider_name', p__('xdelivery', 'Name'))->setRequired(true);

        $this->addSimpleDatetimepicker(
            'valid_from', 
             p__('xdelivery', 'Valid From'), 
            false, 
            Siberian_Form_Abstract::DATETIMEPICKER
        )->setRequired(true);

        $this->addSimpleDatetimepicker(
            'valid_until', 
            p__('xdelivery', 'Valid Until'), 
            false, 
            Siberian_Form_Abstract::DATETIMEPICKER
        )->setRequired(true); 
      
     
        /** Store Image */
        $image = $this->addSimpleImage(
            'image', 
            p__('xdelivery','Image'), 
            p__('xdelivery','Import an image'), 
            [
                'width' => 512, 
                'height' => 320
            ]
        )->setRequired(true);

        $this->addSimpleSelect("category_id", p__('xdelivery','Display At'), $category);

        $this->addSimpleCheckbox('status', p__('xdelivery', 'Active'));  

        return $this;
    }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }
 
}
?>
 