<?php

class Xdelivery_Model_Db_Table_PrinterLogs extends Core_Model_Db_Table
{
    protected $_name = "xdelivery_printer_log";
    protected $_primary = "log_id";


     /**
     * @param array $data
     * @return bool
     */
    public static function saveLog($data = []) {
        $log = new Xdelivery_Model_PrinterLogs();
        $log->addData($data)->save();
        return true;
    }


}
