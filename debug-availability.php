<?php
/**
 * Debug script for availability rules
 */

// Load WordPress
require_once('../../../wp-load.php');

global $wpdb;

echo "<h2>Debug Availability Rules</h2>";

// Check consultation types
$types_table = $wpdb->prefix . 'booking_consultation_types';
$types = $wpdb->get_results("SELECT * FROM $types_table");

echo "<h3>Consultation Types:</h3>";
echo "<pre>";
print_r($types);
echo "</pre>";

// Check availability rules
$rules_table = $wpdb->prefix . 'booking_availability_rules';
$rules = $wpdb->get_results("SELECT * FROM $rules_table");

echo "<h3>Availability Rules:</h3>";
echo "<pre>";
print_r($rules);
echo "</pre>";

// Test slot generation
if (!empty($types) && !empty($rules)) {
    $type_id = $types[0]->id;
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+7 days'));
    
    echo "<h3>Testing slot generation:</h3>";
    echo "Type ID: $type_id<br>";
    echo "Start Date: $start_date<br>";
    echo "End Date: $end_date<br>";
    
    $result = Availability_Manager::get_available_slots($type_id, $start_date, $end_date);
    
    echo "<h4>Result:</h4>";
    echo "<pre>";
    if ($result->is_success()) {
        $slots = $result->get_data();
        echo "Found " . count($slots) . " slots:\n";
        foreach ($slots as $slot) {
            echo $slot->get_start_formatted() . " - " . $slot->get_end_formatted() . "\n";
        }
    } else {
        echo "Error: " . $result->get_error();
    }
    echo "</pre>";
}

// Check logs
$log_file = __DIR__ . '/logs/booking-system.log';
if (file_exists($log_file)) {
    echo "<h3>Recent Logs:</h3>";
    echo "<pre>";
    echo file_get_contents($log_file);
    echo "</pre>";
}
?>
