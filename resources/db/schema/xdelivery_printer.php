<?php



/**

 *

 * Schema definition for 'xdelivery_printer'
 *
 *
 */

$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['xdelivery_printer'] = [

    'printer_id' => [

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

    'store_id' => [

        'type' => 'int(11) unsigned',

        'default' => '0'

    ],

    'printer_title' => [

        'type' => 'varchar(255)',

        'is_null' => true,

        'charset' => 'utf8',

        'collation' => 'utf8_unicode_ci'

    ],

    'printer_type' => [ //1=1.0, 2=2.0, 3 = 3.0

        'type' => 'tinyint(4)',

        'default' => '2'

    ],

    'printer_username' => [

        'type' => 'varchar(20)',

        'is_null' => true,

        'charset' => 'utf8',

        'collation' => 'utf8_unicode_ci'

    ],

    'printer_password' => [

        'type' => 'varchar(20)',

        'is_null' => true,

        'charset' => 'utf8',

        'collation' => 'utf8_unicode_ci'

    ],

    'printer_ping_counter' => [

        'type' => 'int(3) unsigned',

        'default' => '0'

    ],

	'currency_code' => [
        'type' => 'varchar(20)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => 'EUR'
    ],
	'currency_symbol' => [
        'type' => 'varchar(20)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
		'default' => '€'
    ],
    
    'start_fetching_orders_from' => [

        'type' => 'datetime',

        'default' => 'CURRENT_TIMESTAMP',

    ],

    'created_at' => [

        'type' => 'datetime'

    ],

    'updated_at' => [

        'type' => 'timestamp',

        'default' => 'CURRENT_TIMESTAMP',

    ],

];

