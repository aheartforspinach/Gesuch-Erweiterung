<?php

function task_wantedExtension($task){
    global $db, $mybb;
    $wantedArea = $mybb->settings['wantedExtension_area'];
    
    //deleted threads
    $deletedThreads = $db->query("SELECT tid
        FROM " . TABLE_PREFIX . "wanted
        WHERE tid NOT IN(
        SELECT tid 
        FROM " . TABLE_PREFIX . "threads)");

    while ($thread = $db->fetch_array($deletedThreads)) {
        $db->delete_query('wanted', 'tid = ' . $thread['tid']);
    }
    // Add an entry to the log
    add_task_log($task, 'Die DB-Tabelle "wanted" wurde bereinigt');
}