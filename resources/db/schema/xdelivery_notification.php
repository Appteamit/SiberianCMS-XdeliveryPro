<?php
/**
 *
 * Schema definition for 'xdelivery_notification'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_notification'] = [
    'notification_id' => [
        'type' => 'int(11) unsigned',
        'auto_increment' => true,
        'primary' => true,
    ],
    'app_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application',
            'column' => 'app_id',
            'name' => 'FK_Xdelivery_NOTIFICATION_AID',
            'on_update' => 'CASCADE',
            'on_delete' => 'CASCADE',
        ],
        'index' => [
            'key_name' => 'app_id',
            'index_type' => 'BTREE',
            'is_null' => false,
            'is_unique' => false,
        ],
    ],
    'value_id' => [
        'type' => 'int(11) unsigned',
        'foreign_key' => [
            'table' => 'application_option_value',
            'column' => 'value_id',
            'name' => 'FK_Xdelivery_NOTIFICATION_VID',
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
    'notification_order_status' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    // customer
    'notification_is_customer' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    // custmer push
    'notification_customer_is_push' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_customer_push_title' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
     'notification_customer_push_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'notification_customer_push_custom_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'notification_customer_push_cover' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    // custmer email
    'notification_customer_is_email' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_customer_email_subject' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'notification_customer_email_body' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    // custmer sms
    'notification_customer_is_sms' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_customer_sms_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    // custmer Whatsender 
    'notification_customer_is_whatsender' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_customer_whatsender_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    // Admin
    'notification_is_admin' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    // Admin push
    'notification_admin_is_push' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_admin_push_title' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
     'notification_admin_push_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    'notification_admin_push_custom_url' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'notification_admin_push_cover' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    // Admin email
    'notification_admin_is_email' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_admin_email_subject' => [
        'type' => 'varchar(255)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ], 
    'notification_admin_email_body' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    // Admin sms
    'notification_admin_is_sms' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_admin_sms_text' => [
        'type' => 'text',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
    ],
    // Admin Whatsender 
    'notification_admin_is_whatsender' => [
        'type' => 'tinyint(11)',
        'default' => "0",
    ],
    'notification_admin_whatsender_text' => [
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
    ],
];