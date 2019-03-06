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

///////////////////////////////////////////////////////////////////////////////

define('__OSSN_GOOGLE_PLUS_IMPORTER__', ossn_route()->com . 'GooglePlusImporter/');

///////////////////////////////////////////////////////////////////////////////

function google_plus_importer(){
    //Create a new page to put the G+ uploader  dialogs on.
    ossn_register_page('google_plus_importer', 'google_plus_importer_page');
}

///////////////////////////////////////////////////////////////////////////////

function add_media_to_post(GooglePlusPost $post, array $media_json) {
    foreach( $media_json as $medium_json ) {
        $medium = create_medium_from_json_value($medium_json["contentType"]);

        $medium->set_path($media_json["localFilePath"]);

        if ( array_key_exists("comments", $medium_json) ) {
            $comments_json = $medium_json["comments"];
            foreach ( $comments_json as $comment_json ) {
                $comment = new GooglePlusComment();
                $comment->set_date($comment_json["creationTime"]);
                $comment->set_author($comments_json["author"]["displayName"]);
                $comment->set_content($comment_json["content"]);

                $medium->add_comment($comment);
            }
        }

        $post->add_media_item($medium);
    }
}

///////////////////////////////////////////////////////////////////////////////

function add_comments_to_post(GooglePlusPost $post, array $comments_json) {
    foreach ( $comments_json as $comment_json ) {
        $comment = new GooglePlusComment();
        $comment->set_date($comment_json["creationTime"]);
        $comment->set_author($comments_json["author"]["displayName"]);
        $comment->set_content($comment_json["content"]);

        $post->add_comment($comment);
    }
}

///////////////////////////////////////////////////////////////////////////////

function get_post_from_file(string $file_path) : GooglePlusPost {
    $file_contents = file_get_contents($file_path);
    $post_json = json_decode($file_contents, true);

    $post = new GooglePlusPost();

    foreach($post_json as $thing => $data) {
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
                // An album is a dictionary with only a single thing in it: the "media" item.
                add_media_to_post($post, $data["media"]);
                break;
        }
    }

    return $post;
}

///////////////////////////////////////////////////////////////////////////////

function add_post_to_db(GooglePlusPost $post) {

    // this mapping is because there are already some users in the site DB, so
    // we need to use their user names and not the ones from Google+.
    $user_mapping = array(
        "Kevin Channon" => "kevin",
        "Joanne Channon" => "joanne",
        "Anthony James" => "Anthony",
        "Jackie Channon" => "jackie",
        "Jessica Channon" => "jessica",
    );

    if ( array_key_exists($post->get_author(), $user_mapping) == false) {
        // This is a user that we haven't seen before. Add to mapping.
        $user_mapping[$post->get_author()] = $post->get_author();

        // Add the user to the DB.
        // TODO: Actually work out how to do things with the main DB...
    }

}

///////////////////////////////////////////////////////////////////////////////

function google_plus_importer_page(){

    $takeout_dir_path = "../GooglePlusData";

    //
    // Read all the posts from the files and push them into the DB.
    //
    $posts_dir_path = $takeout_dir_path."/Takeout/Google+ Stream/Posts";
    $post_file_infos = new DirectoryIterator(dirname($posts_dir_path));
    foreach ($post_file_infos as $file_info) {
        if ($file_info->isDot()) {
            continue;
        }

        $post = get_post_from_file($file_info->getFilename());

        add_post_to_db($post);
    }
}

///////////////////////////////////////////////////////////////////////////////

ossn_register_callback('ossn', 'init', 'google_plus_importer');

///////////////////////////////////////////////////////////////////////////////
