<?php
/**
 *
 * Schema definition for 'xdelivery_product_attribute'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_product_attribute'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'attribute_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_attributes',
            'column' => 'id',
            'name' => 'FK_Xelivery_PRODUCT_ATTRIBUTE_AID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'attribute_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'attribute_value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_attribute_values',
            'column' => 'id',
            'name' => 'FK_Xdelivery_PRODUCT_ATTRIBUTE_VALUE_VID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'attribute_value_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'is_variant' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "0"
    ],
    'is_active' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "1"
    ],
    'product_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'xdelivery_products',
            'column' => 'id',
            'name' => 'FK_Xdelivery_PRODUCT_ATTRIBUTE_PID',
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
