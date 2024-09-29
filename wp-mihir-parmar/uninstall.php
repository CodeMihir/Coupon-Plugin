<?php 
global $wpdb;
$table_name = $wpdb->prefix . 'coupons_mihir_parmar';
$wpdb->query('DROP TABLE IF EXISTS ' . $table_name);
?>