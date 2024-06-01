<?php
/**
 *
 * Schema definition for 'xdelivery_orders'
 *
 * Last update: 2020-09-12
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_orders'] = [
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
            'name' => 'FK_XDELIVERY_ORDERS_VID',
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
            'name' => 'FK_XDELIVERY_ORDER_STORE_SID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'KEY_STORE_ID',
            'index_type' => 'BTREE',
            'is_null' => true,
            'is_unique' => false,
        ],
    ],
    'customer_id' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'order_number' => [
        'type' => 'int(50)',
        'is_null' => false,
        'default' => 0
    ],
    'total_qty' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => 0
    ],
    'sub_amount' => [
        'type' => 'double',
    ],
    'sub_amount_with_vat' => [
        'type' => 'double',
    ],
    'total_tax' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'total_vat_tax' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'delivery_cost' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'discount_amount' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'tips_amount' => [
        'type' => 'double',
        'is_null' => true,
    ], 
    'total_amount' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'paid_amount' => [
        'type' => 'double',
        'is_null' => true,
    ],
    'notes' => [
        'type' => 'text',
        'is_null' => true,
    ],
    'delivery_date' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'delivery_time' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'discount_code' => [
        'type' => 'varchar(255)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'promocode_id' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'paid_at' => [
        'type' => 'datetime',
        'is_null' => true,
    ],
    'tip' => [
        'type' => 'double',
        'is_null' => true,
        'default' => 0
    ],
    'delivery_method_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => 0
    ],
    'delivery_method' => [
        'type' => 'varchar(50)',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'customer_firstname' => [
        'type' => 'varchar(100)',
        'charset' => 'utf8',
        'is_null' => true,
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_lastname' => [
        'type' => 'varchar(100)',
        'charset' => 'utf8',
        'is_null' => true,
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_email' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_street' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_postcode' => [
        'type' => 'varchar(10)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_city' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_phone' => [
        'type' => 'varchar(15)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'customer_info' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'is_return_request' => [
        'type' => 'int (11)',
        'is_null' => true,
        'default' => 0
    ],
    'status' => [
        'type' => 'enum(\'pending_payment\',\'failed\',\'processing\',\'completed\',\'on_hold\',\'canceled\',\'refunded\',\'shipped\',\'delivered\', \'accepted\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'admin_remark' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'tracking_type' => [
        'type' => 'int (11)',
        'is_null' => true,
        'default' => 0
    ],
    'is_managed' => [
        'type' => 'int (11)',
        'is_null' => true,
        'default' => 0
    ],
    'tracking_number_url' => [
        'type' => 'text',
        'is_null' => true,
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