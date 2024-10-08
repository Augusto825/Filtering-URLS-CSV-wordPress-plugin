<?php
/*
Plugin Name: CSV Filter Plugin
Description: Reads a CSV file, compares it to an existing list, filters out duplicates, and removes old links from the original file.
Version: 1.0
Author: Luis Fernando
*/

function csv_filter_plugin_init() {
    // Set file paths
    $oldListFile = WP_CONTENT_DIR. '/filtering-URLs/kidney-cancer.csv';
    $newListFile = WP_CONTENT_DIR. '/filtering-URLs/Kidney_Cancer_new_list.csv';

    // Read old list
    $oldList = array();
    if (($handle = fopen($oldListFile, 'r'))!== FALSE) {
        while (($data = fgetcsv($handle, 1000, ","))!== FALSE) {
            $oldList[] = $data[0];
        }
        fclose($handle);
    }

    // Read new list
    $newList = array();
    if (($handle = fopen($newListFile, 'r'))!== FALSE) {
        while (($data = fgetcsv($handle, 1000, ","))!== FALSE) {
            $newList[] = $data[0];
        }
        fclose($handle);
    }

    // Find duplicates and remove from old list
    $duplicates = array_intersect($oldList, $newList);
    $oldList = array_diff($oldList, $duplicates);

    // Write updated old list to file
    if (($handle = fopen($oldListFile, 'w'))!== FALSE) {
        foreach ($oldList as $url) {
            fwrite($handle, $url. "\n");
        }
        fclose($handle);
    }
}

// Schedule the task to run every hour
function csv_filter_plugin_schedule() {
    if (!wp_next_scheduled('csv_filter_plugin_cron')) {
        wp_schedule_event(time(), 'hourly', 'csv_filter_plugin_cron');
    }
}
add_action('init', 'csv_filter_plugin_schedule');

// Hook into WordPress cron
add_action('csv_filter_plugin_cron', 'csv_filter_plugin_init');