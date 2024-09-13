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

    /**
     * @param $valueId
     * @return array|bool
     */
    public function getInappStates($valueId)
    {
        
        $inAppStates = [
            [
                "state" => "xdelivery-home",
                "offline" => false,
                
                'params' => [
                    'value_id' => $valueId,
                ],          
            ],
        ];

        return $inAppStates;
    }

    /**
     * @return null
     */
    public static function getCurrentValueId()
    {
        $app = self::getApplication();
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
        $app = self::getApplication();
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