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

function add_media_to_post($post, $media_json) {
    foreach( $media_json as $medium_json ) {
        $medium = create_medium_from_json_value($media_json["contentType"]);

        // TODO: Add the rest of the code to read in the medium.
    }
}

function add_comments_to_post($post, $comments_json) {
    foreach ( $comments_json as $comment_json ) {
        $comment = new GooglePlusComment();
        $comment->set_date($comment_json["creationTime"]);
        $comment->set_author($comments_json["author"]["displayName"]);
        $comment->set_content($comment_json["content"]);

        $post->add_comment($comment);
    }
}

function google_plus_importer_page(){

    $takeout_dir_path = "../GooglePlusData";

    $posts = array();

    $posts_dir_path = $takeout_dir_path."/Takeout/Google+ Stream/Posts";
    $post_file_infos = new DirectoryIterator(dirname($posts_dir_path));
    foreach ($post_file_infos as $file_info) {
        if ($file_info->isDot()) {
            continue;
        }

        $post_file_path = $file_info->getFilename();
        $file_contents = file_get_contents($post_file_path);
        $post_json = json_decode($file_contents, true);

        $post = new GooglePlusPost();

        foreach($post_json as $thing => $data) {
            // Find the post creation date, the content, pictures and comments (including on pictures)

            switch ( $thing ) {
                case "creationTime" :
                    $post->set_date($data);
                    break;
                case "author" :
                    // The author is a dictionary; we just want the display name.
                    $post->set_author($data["displayName"]);
                    break;
                case "comments" :
                    add_comments_to_post($post, $data);
                    break;
                case "album":
                    // TODO: Actually add the code to call the add_media_to_post function...
                    break;
            }
        }
    }
}

ossn_register_callback('ossn', 'init', 'google_plus_importer');