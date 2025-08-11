<?php 

/**
 * Class Xdelivery_Model_Xdelivery
 * @package Xdelivery\Model
 */
class Xdelivery_Model_Xdelivery extends Core_Model_Default
{
    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_Xdelivery::class;

    // ... [keep other methods the same] ...

    /**
     * @return null
     */
    public static function getCurrentValueId()
    {
        $instance = new self();
        $app = $instance->getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "xdelivery") {
                    return $option->getId();
                }
            }
        }
        return null;
    }

    /**
     * @return null
     */
    public static function getCurrent()
    {
        $instance = new self();
        $app = $instance->getApplication();
        if ($app) {
            $options = $app->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() === "xdelivery") {
                    return $option;
                }
            }
        }
        return null;
    }
}