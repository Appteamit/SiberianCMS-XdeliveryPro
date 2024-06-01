<?php

/**
 * Class xdelivery_Form_Tax_Delete
 */
class Xdelivery_Form_Tax_Delete extends Siberian_Form_Abstract {

    public function init() {
        parent::init();

        $this
            ->setAction(__path("/xdelivery/settings/deletetaxes"))
            ->setAttrib("id", "form-delete-xdelivery-tax")
            ->setConfirmText("You are about to remove this Tax ! Are you sure ?");
        ;

        /** Bind as a delete form */
        self::addClass("delete", $this);

        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
            ->from('xdelivery')
            ->where('xdelivery_taxes.id = :value')
        ;

        $provider_id = $this->addSimpleHidden("id", p__('xdelivery', 'Taxes'));
        $provider_id->addValidator("Db_RecordExists", true, $select);
        $provider_id->setMinimalDecorator();

        $mini_submit = $this->addMiniSubmit();
    }
}