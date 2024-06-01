<?php
/**
 * Schema definition for 'xdelivery_extra_options'
 */

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_extra_options'] = [
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
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_VID',
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
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'category_type' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'is_required' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'maximum_option' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'minimum_option' => [
        'type' => 'int(11)',
        'is_null' => true,
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