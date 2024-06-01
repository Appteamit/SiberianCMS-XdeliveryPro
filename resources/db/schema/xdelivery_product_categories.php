<?php
/**
 *
 * Schema definition for 'xdelivery_product_categories'
 *
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_product_categories'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'category_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_categories',
            'column' => 'id',
            'name' => 'FK_Xdelivery_PRODUCT_CATEOGRY_CID',
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
    'product_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_products',
            'column' => 'id',
            'name' => 'FK_Xdelivery_PRODUCT_PID',
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
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
