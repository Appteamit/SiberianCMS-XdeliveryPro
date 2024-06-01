<?php

class Xdelivery_Model_WorkingTimes extends Core_Model_Default {

    /**
     * @var null
     */
    public static $acl = null;

    /**
     * @var string
     */
    protected $_db_table = Xdelivery_Model_Db_Table_WorkingTimes::class;

    public static function getDefaultWorking() {
        $working = [];
        $working['monday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
        $working['tuesday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
        $working['wednesday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];;
        $working['thursday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
        $working['friday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
        $working['saturday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
        $working['sunday'] = ['opening_time' => '09:00', 'closing_time' => '18:00'];
      
        return $working;
    }

	public static function getWorkingDays($storeId = null, $valueId = null) {
        if ($storeId) {
            // Fetching!
	        $settings = (new self())->findAll(['store_id' => $storeId]); 
       
            if ($settings->count() == 0) {
                $defaultWorking = self::getDefaultWorking();               
                foreach ($defaultWorking as $key => $value) {
	                $modal = (new self())
			                ->setValueId($valueId)
			                ->setStoreId($storeId)
			                ->setWorkingDay($key)
			                ->setOpeningTime($value['opening_time'])
			                ->setClosingTime($value['closing_time'])
			                ->save();
                }
                $settings = (new self())->findAll(['store_id' => $storeId]);
	        }
        }
 
        return $settings;
    }



    
}