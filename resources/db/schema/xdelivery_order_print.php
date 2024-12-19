<?php



/**

 *

 * Schema definition for 'xdelivery_order_print'
 *
 *
 */

$schemas = (!isset($schemas)) ? [] : $schemas;

$schemas['xdelivery_order_print'] = [

    'order_print_id' => [

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

    'oredr_id' => [

        'type' => 'int(11) unsigned',

        'default' => '0'

    ],


    'order_print_status' => [ // 0 = sent, 1 = printed, 2 = failed, 3 = rejected

        'type' => 'int(3) unsigned',

        'default' => '0'

    ],


    'created_at' => [

        'type' => 'datetime'

    ],

    'updated_at' => [

        'type' => 'timestamp',

        'default' => 'CURRENT_TIMESTAMP',

    ],

];

