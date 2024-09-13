<?php



/**

 *

 * Schema definition for 'xdelivery_printer_log'

 *

 * Last update: 2019-03-18

 *

 */

$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['xdelivery_printer_log'] = [

    'log_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],

    'app_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],

    'order_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],

    'printer_username' => [
        'type' => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],

    'query_string' => [
        'type' => 'tinytext',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],

    'call_type' => [
        'type' => 'tinytext', //1 = getoreder, 2 = callback
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'log_type' => [
        'type' => 'tinyint(2)', //1 = getoreder, 2 = callback
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],

    'status' => [
        'type' => 'varchar(20)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'created_at' => [

        'type' => 'datetime'

    ],

];

