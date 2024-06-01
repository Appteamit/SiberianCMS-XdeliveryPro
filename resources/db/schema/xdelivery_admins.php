<?php
/**
 *
 * Schema definition for 'xdelivery_admins'
 *
 * Last update: 2024-02-02
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_admins'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'store_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'customer_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'created_at' => [
        'type' => 'datetime'
    ],
    'updated_at' => [
        'type' => 'datetime'
    ]
];

