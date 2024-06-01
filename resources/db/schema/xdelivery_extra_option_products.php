<?php
/**
 * Schema definition for 'xdelivery_extra_option_products'
 */

$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_extra_option_products'] = [
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
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_PRODUCT_OID',
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
    'product_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_products',
            'column' => 'id',
            'name' => 'FK_Xdelivery_EXTRA_OPTIONS_PRODUCT_PID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'product_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
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