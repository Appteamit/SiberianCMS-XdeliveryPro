<?php
/**
 * Schema definition for 'xdelivery_extra_option_values'
 */

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_extra_option_values'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'option_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_extra_options',
            'column' => 'id',
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_VALUES_ID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'option_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'price' => [
        'type' => 'double',
        'is_null' => false,
        'default' => 0        
    ],
    'is_active' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'position' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];