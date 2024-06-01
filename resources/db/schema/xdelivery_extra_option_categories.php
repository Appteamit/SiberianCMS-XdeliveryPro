<?php
/**
 * Schema definition for 'xdelivery_extra_option_categories'
 */

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_extra_option_categories'] = [
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
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_CATEGOTY_OID',
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
    'category_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_categories',
            'column' => 'id',
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_CATEGOTY_CID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'category_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];