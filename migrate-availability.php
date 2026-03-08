<?php
/**
 * Migration script to create availability_slots table.
 * Run this once after updating to version 1.0.3
 * 
 * Access: wp-admin -> Rezerwacje -> Dostępność
 * The table will be created automatically on first access.
 */

if (!defined('ABSPATH')) {
    die('Direct access not allowed');
}

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Create availability_slots table
$table_slots = $wpdb->prefix . 'booking_availability_slots';
$sql_slots = "CREATE TABLE $table_slots (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    date date NOT NULL,
    start_time time NOT NULL,
    end_time time NOT NULL,
    is_active tinyint(1) NOT NULL DEFAULT 1,
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY date (date),
    KEY is_active (is_active)
) ENGINE=InnoDB $charset_collate;";

dbDelta($sql_slots);

echo "Migration completed! Table {$table_slots} created successfully.";
