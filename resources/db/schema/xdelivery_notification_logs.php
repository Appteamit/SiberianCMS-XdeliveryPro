<?php
/**
 *
 * Schema definition for 'xdelivery_notification_logs'
 *
 * Last update: 2024-02-02
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_notification_logs'] = [
    'id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'order_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'user_id' => [
        'type' => 'int(11) unsigned',
        'default' => '0'
    ],
    'type' => [ //email, sms, push, whatsender
        'type' => 'varchar(25)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'status' => [ // sent, in_active, error
        'type' => 'varchar(25)',
        'is_null' => false,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'additional_info' => [ // Error details, or success message
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'created_at' => [
        'type' => 'datetime'
    ]
];

