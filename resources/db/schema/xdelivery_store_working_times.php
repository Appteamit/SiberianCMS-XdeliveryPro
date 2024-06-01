<?php
/**
 *
 * Schema definition for 'xdelivery_store_working_times'
 *
 * Last update: 2020-10-14
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_store_working_times'] = [
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
            'name' => 'FK_XDELIVERY_STORE_WORKING_VID',
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
    'store_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_store',
            'column' => 'store_id',
            'name' => 'FK_XDELIVERY_STORE_WORKING_SID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'KEY_STORE_ID',
            'index_type' => 'BTREE',
            'is_null' => true,
            'is_unique' => false,
        ],
    ],
    'working_day' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'opening_time' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'closing_time' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'enable_delivery' => [
        'type' => 'int(11)',
        'default' => 1
    ],
    'enable_pickup' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => 1
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];