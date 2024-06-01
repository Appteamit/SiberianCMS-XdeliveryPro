<?php

/**
 * Class xdelivery_Form_Slider_Delete
 */
class Xdelivery_Form_Slider_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/xdelivery/slider/delete"))
            ->setAttrib("id", "form-delete-xdelivery-slider")
            ->setConfirmText(p__('xdelivery', 'You are about to remove this slider ! Are you sure ?'));
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('xdelivery_sliders')
            ->where('xdelivery_sliders.id = :value')
        ;

        $id = $this->addSimpleHidden("id", p__('xdelivery', 'Slider'));
        $id->addValidator("Db_RecordExists", true, $select);
        $id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}