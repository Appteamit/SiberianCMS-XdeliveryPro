<?php

class Xdelivery_Form_Store_From extends Siberian_Form_Abstract
{
    
    public function init() {
        parent::init();
        
        $this
            ->setAction(__path("/xdelivery/store/editpost"))
            ->setAttrib("id", "form-add-store");
        
        self::addClass("create", $this); 

        $value_id = $this->addSimpleHidden("value_id");
        $store_id = $this->addSimpleHidden("store_id");
        $address_lat = $this->addSimpleHidden("address_lat");
        $address_lng = $this->addSimpleHidden("address_lng");
        $is_default = $this->addSimpleHidden("is_default");
        
        /** Store name */
        $name = $this->addSimpleText('store_name', p__('xdelivery', 'Store Name'))->setRequired(true);

         /** Store sub title */
        $name = $this->addSimpleText('store_sub_title', p__('xdelivery', 'Sub Title'))->setRequired(true);

        /** Store phone */
        $store_phone = $this->addSimpleText('store_phone', p__('xdelivery', 'Store Phone'))->setRequired(false);
        /** Store email */
        $store_email = $this->addSimpleText('store_email', p__('xdelivery', 'Store Email'))->setRequired(true);
        /** Store address */
        $store_address = $this->addSimpleText('store_address', p__('xdelivery', 'Store Address'))->setRequired(false);
        /** Store city */
        $store_city = $this->addSimpleText('store_city', p__('xdelivery', 'Store City'))->setRequired(false);
        /** Store city */
        $store_city = $this->addSimpleText('store_location', p__('xdelivery', 'Store Location'))->setRequired(false);
        /** Store zip */
         $hl = p__("xdelivery", 'Required for distance calculation');
        $helpText1 = '<div class="col-md-4"></div><div class="col-md-8"><div  class="alert alert-info">'.  $hl .'</div></div>';
        $this->addSimpleHtml("helpText11",  p__('xdelivery', $hl ));

        $store_zip = $this->addSimpleText('store_zip', p__('xdelivery', 'Store Zip'))->setRequired(false);
 
        $this->addSimpleCheckbox('is_active', p__('xdelivery', 'Active'));

        $this->addSimpleText('commision_percentage', p__('xdelivery', 'Commission Percentage'))->setRequired(true);

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

        $helpText2 = '<div class="col-md-4"></div><div class="col-md-8"><div  class="store-images-container"></div></div>';
        $this->addSimpleHtml("helpText2", $helpText2);
  }
    
    public function setElementValueById($id, $value, $required = false) {
        $element = $this->getElement($id)->setValue($value);
        if( $required ) {
            $element->setRequired(true);
        }
    }  
    
}
?>
 