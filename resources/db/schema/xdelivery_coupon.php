<?php
/**
 *
 * Schema definition for 'xdelivery_coupon'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_coupon'] = [
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
            'name' => 'FK_Xdelivery_COUPON_CODE_VID',
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
    'status' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ], 
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ], 
    'description' => [
        'type' => 'text',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => true,
    ],
    'coupon_code' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'discount_type' => [
        'type' => 'enum(\'fixed\',\'percent\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'is_null' => false,
    ], 
    'discount_value' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'allow_free_shipping' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => 0
    ],
    'min_spend' => [
        'type' => 'varchar(100)',
        'is_null' => true,
    ],
    'max_spend' => [
        'type' => 'varchar(100)',
        'is_null' => true,
    ],
    'usage_limit_per_coupon' => [
        'type' => 'varchar(100)',
        'is_null' => true,
    ],
    'usage_limit_per_customer' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'start_date' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'end_date' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'start_time' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'end_time' => [
        'type' => 'varchar(100)',
        'is_null' => false,
    ],
    'is_delete' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];