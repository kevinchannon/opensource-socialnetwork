<?php
/**
 * Open Source Social Network
 *
 * @package   (softlab24.com).ossn
 * @author    OSSN Core Team <info@softlab24.com>
 * @copyright (C) SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */

define('__OSSN_GOOGLE_PLUS_IMPORTER__', ossn_route()->com . 'GooglePlusImporter/');

function google_plus_importer(){
    //Create a new page to put the G+ uploader  dialogs on.
    ossn_register_page('google_plus_importer', 'google_plus_importer_page');
}

function get_photos(){}

function google_plus_importer_page(){

    $users = new QuickHashIntSet( 64, QuickHashIntSet::CHECK_FOR_DUPES );

    $string = file_get_contents("../test_dump.json");

    $json_chunk = json_decode($string, true);

    $takeout_dir_path = "../GooglePlusData";
    $posts_dir_path = $takeout_dir_path."/Takeout/Google+ Stream/Posts";
    $posts = new DirectoryIterator(dirname($posts_dir_path));
    foreach ($posts as $file_info) {
        if ($file_info->isDot()) {
            continue;
        }

        $post_file_path = $file_info->getFilename();
        $file_contents = file_get_contents($post_file_path);
        $post_json = json_decode($file_contents, true);

        foreach($post_json as $thing => $data) {
            // Find the post creation date, the content, pictures and comments (including on pictures)
        }
    }
}

ossn_register_callback('ossn', 'init', 'google_plus_importer');