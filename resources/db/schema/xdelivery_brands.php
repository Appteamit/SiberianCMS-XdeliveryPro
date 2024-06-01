<?php
/**
 * Schema for xdelivery_brands table
 */
$schemas = $schemas ?? [];
$schemas['xdelivery_brands'] = [
    'brand_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true
    ],
    'name' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'index' => [
            'key_name' => 'xdelivery_brand_value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'image' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'description' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'is_active' => [
        
        'type' => 'int(11) unsigned',
        'default' => '1',
    ],
    'is_delete' => [
        'type' => 'tinyint(2) unsigned',
        'default' => 0
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];
