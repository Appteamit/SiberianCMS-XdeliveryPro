<?php
/**
 *
 * Schema definition for 'xdelivery_attribute_category'
 *
 * Last update: 2020-05-04
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_attribute_category'] = [
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
            'name' => 'FK_XDELIVERY_ATTRIBUTES_CATEGORY_VID',
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
            'name' => 'FK_XDELIVERY_ATTRIBUTES_ATTRIBUTE_ID',
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
    'category_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_categories',
            'column' => 'id',
            'name' => 'FK_XDELIVERY_ATTRIBUTES_CATEGORY_ID',
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
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];