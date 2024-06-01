<?php
/**
 *
 * Schema definition for 'xdelivery_shopping_carts'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_shopping_carts'] = [
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
            'name' => 'FK_Xdelivery_SHOPPING_CART_VALUE_VID',
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
    'cart_key' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => '0'
    ],
    'store_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => '0'
    ],
    'device_uid' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'child_product_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => '0'
    ],
    'product_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_products',
            'column' => 'id',
            'name' => 'FK_Xdelivery_SHOPPING_CART_PRODUCT_PID',
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
    'qty' => [
        'type' => 'int(11) unsigned',
        'default' => "1",
        'is_null' => false,
    ],
    'amount' => [
        'type' => 'varchar(255)',
        'default' => "0",
        'is_null' => false,
    ],
    'discount_amount' => [
        'type' => 'varchar(255)',
        'default' => "0",
        'is_null' => true,
    ],
    'discount_code' => [
        'type' => 'varchar(255)',
        'default' => "",
        'is_null' => true,
    ],
    'tax_amount' => [
        'type' => 'varchar(255)',
        'default' => "0",
        'is_null' => false,
    ],
    'total_amount' => [
        'type' => 'varchar(255)',
        'default' => "0",
        'is_null' => false,
    ],
    'attributejson' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'optionsjson' => [
        'type' => 'text',
        'is_null' => true,
    ],    
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
