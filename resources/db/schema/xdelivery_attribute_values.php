<?php
/**
 *
 * Schema definition for 'xdelivery_attribute_values'
 *
 * Last update: 2020-05-04
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_attribute_values'] = [
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
            'name' => 'FK_Xdelivery_ATTRIBUTES_VALUES_VID',
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
    'attribute_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_attributes',
            'column' => 'id',
            'name' => 'FK_Xdelivery_ATTRIBUTES_ID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ], 
    'value_name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'position' => [
        'type' => 'int(11) unsigned',
        'default' => "1",
        'is_null' => true,
    ],
    'is_active' => [
        'type' => 'tinyint(11)',
        'default' => "1",
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
