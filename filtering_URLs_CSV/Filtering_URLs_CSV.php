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

// Hook into WordPress
add_action('wp_head', 'csv_filter_plugin_init');

// Monitor file changes
function csv_filter_plugin_monitor_file() {
    $newListFile = WP_CONTENT_DIR. '/filtering-URLs/Kidney_Cancer_new_list.csv';
    $lastModified = filemtime($newListFile);
    if ($lastModified > get_option('csv_filter_plugin_last_modified')) {
        update_option('csv_filter_plugin_last_modified', $lastModified);
        csv_filter_plugin_init();
    }
}
add_action('wp_head', 'csv_filter_plugin_monitor_file');