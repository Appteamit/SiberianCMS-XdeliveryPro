<?php
/**
 *
 * Schema definition for 'xdelivery_categories'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_categories'] = [
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
            'name' => 'FK_Xdelivery_CATEGORIES_VID',
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
    'parent_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => 0
    ], 
    'category_name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ], 
    'short_summery' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'position' => [
        'type' => 'int(11) unsigned',
        'default' => "1",
        'is_null' => true,
    ],
    'is_popular_search' => [
        'type' => 'int(11) unsigned',
        'default' => "0",
        'is_null' => true,
    ],
    'image' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'is_active' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];