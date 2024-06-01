<?php
/**
 *
 * Schema definition for 'xdelivery_shipping_distance'
 *
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_shipping_distance'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'FK_Xdelivery_SHIPPING_DISTANCE_VID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'from_km' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ], 
    'upto_km' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ],
    'amount' => [
        'type' => 'double',
        'is_null' => false,
        'default' => "0"
    ],    
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];