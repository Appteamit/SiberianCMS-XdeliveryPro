<?php
/**
 *
 * Schema definition for 'xdelivery_taxes'
 *
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_taxes'] = [
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
            'name' => 'FK_Xdelivery_TAXES_VID',
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
    'is_tax_class' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ], 
    'name' => [
        'type' => 'varchar(255)',
        'is_null' => false,
    ],
    'tax_rate' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'default' => "0"
    ],
    'status' => [
        'type' => 'enum(\'active\',\'inactive\', \'deleted\')',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];