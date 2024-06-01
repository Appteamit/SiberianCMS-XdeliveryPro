<?php

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_business_day'] = [
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
            'name' => 'FK_Xdelivery_BUSINESS_DAY_VID_AOV_VID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ]
    ],
    'store_id' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => '1'
    ],
    'monday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'tuesday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'wednesday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'thursday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'friday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'saturday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'sunday' => [
        'type' => 'tinyint(1)',
        'default' => '1'
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];