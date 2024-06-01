<?php
/**
 *
 * Schema definition for 'xdelivery_products'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_products'] = [
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
            'name' => 'FK_Xdelivery_PRODUCTS_VID',
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
    'store_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_store',
            'column' => 'store_id',
            'name' => 'FK_XDELIVERY_PRODUCT_STORE_SID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'store_id',
            'index_type' => 'BTREE',
            'is_null' => true,
            'is_unique' => false,
        ],
    ],
    'parent_id' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "0",
    ],
    'product_name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'brand_id' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "0",
    ],
    'short_description' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'product_slug' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'sku' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'price' => [
        'type' => 'double unsigned',
        'default' => "0.0",
        'is_null' => false,
    ],
    'special_price' => [
        'type' => 'double unsigned',
        'default' => "0.0",
        'is_null' => true,
    ],
    'selling_price' => [
        'type' => 'double unsigned',
        'default' => "0.0",
        'is_null' => true,
    ],
    'special_price_start' => [
        'type' => 'date',
        'is_null' => true,
    ],
    'special_price_end' => [
        'type' => 'date',
        'is_null' => true,
    ],
    'is_active' => [
        'type' => 'tinyint(11)',
        'default' => "1",
        'is_null' => true,
    ],
    'manage_stock' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'qty' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "0",
    ],
    'low_stock_threshold' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "2",
    ],
    'in_stock' => [
        'type' => 'tinyint(11)',
        'default' => "1",
    ],
    'product_type' => [
        'type' => 'varchar(22)',
        'default' => 'simple',
    ],
    'tax_id' => [
        'type' => 'int(11)',
        'is_null' => true,
    ],
    'description' => [
        'type' => 'text',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'viewed' => [
        'type' => 'int(11) unsigned ',
        'default' => "0",
    ],
    'new_from' => [
        'type' => 'date',
        'is_null' => true,
    ],
    'new_to' => [
        'type' => 'date',
        'is_null' => true,
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
