<?php 
/**
 *
 * Schema definition for 'xdelivery_order_items'
 *
 * Last update: 2020-09-13
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_order_transactions'] = [
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
            'name' => 'xdelivery_order_transactions_ibfk_1',
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
    'amount' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'transaction_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
    ],
    'gateway_transaction_id' => [
        'type' => 'varchar(50)',
        'is_null' => true,
    ],
    'gateway_info' => [
        'type' => 'varchar(50)',
        'is_null' => true,
    ],
    'payment_method_id' => [
        'type' => 'int(11) unsigned',
    ],
    'payment_method' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'status' => [
        'type' => 'enum(\'pending\',\'success\',\'failed\',\'processing\',\'refunded\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ]
];