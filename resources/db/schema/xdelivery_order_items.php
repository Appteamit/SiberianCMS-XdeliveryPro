<?php 
/**
 *
 * Schema definition for 'xdelivery_order_items'
 *
 * Last update: 2020-09-13
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_order_items'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'order_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_orders',
            'column' => 'id',
            'name' => 'xdelivery_order_items_ibfk_1',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'order_id',
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
            'name' => 'FK_XDELIVERY_ORDER_PRODUCT_PID',
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
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'base_price' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'base_price_incl_tax' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'choice_price' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'price' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'price_incl_tax' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'qty' => [
        'type' => 'int(11)',
        'default' => '1.00',
    ],
    'total' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'total_incl_tax' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'choices' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'options' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'tax_id' => [
        'type' => 'int(11)',
        'default' => 0,
    ],
    'tax_rate' => [
        'type' => 'double',
        'default' => 0,
    ],
    'tax_amount' => [
        'type' => 'double',
        'default' => 0,
    ],
    'product_status' => [
        'type' => 'tinyint(11)',
        'default' => "1",
    ],
];