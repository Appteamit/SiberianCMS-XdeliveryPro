<?php
/**
 *
 * Schema definition for 'xdelivery_settings'
 *
 */
$schemas = (!isset($schemas)) ? [] : $schemas;
$schemas['xdelivery_settings'] = [
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
            'name' => 'FK_Xdelivery_SETTINGS_VID',
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
    'enable_booking' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "1"
    ],
    'review_rating_enable' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "1"
    ], 
    'auto_approval_review' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "1"
    ],
    'send_invoice_email' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "1"
    ],
    'enable_tax' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "1"
    ],
    'min_qty_shopping_cart' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "1"
    ],
    'max_qty_shopping_cart' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "100"
    ],
    'max_qty_per_product' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "10"
    ],
    'min_order_value' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "1"
    ],
    'enable_print_customer_details' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "1"
    ],
    'max_order_value' => [
        'type' => 'int(255)',
        'is_null' => false,
        'default' => "10000000"
    ],
    'delivery_time' => [
        'type' => 'int(120)',
        'is_null' => false,
        'default' => "30"
    ],
    'pick_up_time' => [
        'type' => 'int(120)',
        'is_null' => false,
        'default' => "30"
    ],
    'enable_acceptance_rejection' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ],
    'product_design' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => "grid"
    ],
    'home_screen' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => "category"
    ],
    'category_design' => [
        'type' => 'varchar(50)',
        'is_null' => false,
        'default' => "grid"
    ],
    'delivery_cost' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => "0"
    ],
    'enable_to_deliver' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'enable_to_pickup' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'enable_time_to_deliver' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'break_down_times_deliver' => [
        'type' => 'int (50)',
        'is_null' => true,
        'default' => 30
    ],
    'enable_date_to_deliver' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'days_up_to_deliver' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 7
    ],
    'enable_time_to_pickup' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'break_down_times_pickup' => [
        'type' => 'int (50)',
        'is_null' => true,
        'default' => 30
    ],
    'enable_date_to_pickup' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 1
    ],
    'days_up_to_pickup' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 7
    ],
    'number_of_decimals' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 2
    ],
    'decimal_separator' => [
        'type' => 'varchar(11)',
        'is_null' => true,
        'default' => "."
    ],
    'thousand_separator' => [
        'type' => 'varchar(11)',
        'is_null' => true,
        'default' => ","
    ],
    'currency_position' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'default' => "left"
    ],
    'taxes_to_addons' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "1"
    ],
    'taxes_enable' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "1"
    ],
    'discount_tax_calculation' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => "1"
    ],
    'time_format' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => "g:i A"
    ],
    'date_format' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => "l jS \of F Y"
    ],
    'enable_retrun' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'return_within' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 14
    ],
    'enable_qrscan' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'enable_tips' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'company_address' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'sdi' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'pec' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'cod_fiscale' => [
        'type' => 'int(11)',
        'is_null' => true,
        'default' => 0
    ],
    'enable_tax_with_product' => [
        'type' => 'int(11)',
        'is_null' => false,
        'default' => "0"
    ], 
    'app_service_type' => [
        'type' => 'varchar(25)',
        'is_null' => false,
        'default' => "food"
    ], 
    'whatsender_key' => [
        'type' => 'varchar(100)',
        'is_null' => true
    ], 
    'whatsender_sender' => [
        'type' => 'varchar(100)',
        'is_null' => true
    ],
    'enable_addtocart' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => 1
    ], 
    'enable_save_later' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => 1
    ], 
    'hide_price' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => 0
    ], 
    'order_without_payment' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => 0
    ], 
    'order_status_push' => [
        'type' => 'varchar(50)',
        'is_null' => true,
        'default' => 0
    ],
    'is_enable_email' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'is_enable_sms' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'is_enable_address_two' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'is_enable_locality' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'is_enable_city' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ],
    'twillio_auth_token' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'twillio_sid' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'twillio_sim_id' => [
        'type' => 'varchar(100)',
        'is_null' => true,
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ],
    'is_enable_whatsender' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ], 
    'send_to_admin' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ], 
    'send_to_customer' => [
        'type' => 'tinyint(11)',
        'default' => "0",
        'is_null' => true,
    ], 
    'created_at' => [
        'type' => 'datetime',
    ],
    'updated_at' => [
        'type' => 'datetime',
    ],
];