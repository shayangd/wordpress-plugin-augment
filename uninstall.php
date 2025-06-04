<?php
/**
 * Uninstall script for AI Outline Generator
 * 
 * This file is executed when the plugin is deleted from WordPress admin.
 * It cleans up all plugin data from the database.
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('ai_outline_generator_api_key');
delete_option('ai_outline_generator_api_provider');

// Delete any transients
delete_transient('ai_outline_generator_cache');

// Drop custom table if it exists
global $wpdb;
$table_name = $wpdb->prefix . 'ai_outline_generator_logs';
$wpdb->query("DROP TABLE IF EXISTS {$table_name}");

// Clear any cached data
wp_cache_flush();
