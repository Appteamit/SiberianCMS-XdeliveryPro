<?php
/**
 *
 * Schema definition for 'xdelivery_customer_address'
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_customer_address'] = [
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
            'name' => 'FK_Xdelivery_ADDRESS_VALUE_VID',
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
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'is_null' => true,
        'default' => '0'
    ],
    'customer_name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'phone_number' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'pincode' => [
        'type' => 'varchar(120)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'address' => [
        'type' => 'text',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'locality' => [
        'type' => 'varchar(255)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'city' => [
        'type' => 'varchar(120)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'state' => [
        'type' => 'varchar(120)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'address_type' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default'=> 'home'
    ],
    'is_default' => [
        'type' => 'int (11)',
        'is_null' => true,
        'default' => 0,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'company_address' => [
        'type' => 'varchar(120)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'sdi' => [
        'type' => 'varchar(120)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'pec' => [
        'type' => 'varchar(120)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'cod_fiscale' => [
        'type' => 'varchar(120)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'status' => [
        'type' => 'enum(\'active\',\'inactive\',\'deleted\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'default' => 'active'
    ],   
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];
