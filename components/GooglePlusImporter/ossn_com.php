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

function add_all_new_users_to_db() {
    // this mapping is because there are already some users in the site DB, so
    // we need to use their user names and not the ones from Google+.
    $name_mappings_file = fopen("../GooglePlusData/NameMappings.txt", "r") or die("Failed to open name mapping;");
    fseek($name_mappings_file, 0);

    $user_mappings = array();

    while(!feof($name_mappings_file)) {
        $line = fgets($name_mappings_file);
        $line_parts = explode(",", $line);
        $user_mappings[$line_parts[0]] = $line_parts[1];
    }

    fclose($name_mappings_file);

    foreach ($user_mappings as $full_name => $username) {

        $user_to_add = new OssnUser;
        $user_to_add->username = $username;
        if($user_to_add->isOssnUsername()){
            continue;   // This user is already in the DB.
        }

        // No user in the DB already; try and make up the rest of the details.
        $name_parts = explode(" ", $full_name);

        $user_to_add->first_name = $name_parts[0];

        $last_name = "UNKNOWN";
        if ( count($name_parts) > 1 ) {
            // The last name is everything except the first name.
            $last_name = implode(" ", array_slice($name_parts, 1));
        }

        $user_to_add->last_name = $last_name;

        $user_to_add->email = $full_name."@domain.com";
        $user_to_add->password = $full_name."\'s password";
        $user_to_add->sendactiviation = false;

        if ($user_to_add->addUser()) {
            $em['success'] = 1;
            $em['datasuccess'] = ossn_print('account:created:email');
            echo json_encode($em);
            exit;
        } else {
            $em['dataerr'] = ossn_print('account:create:error:admin');
            echo json_encode($em);
            exit;
        }
    }
}

///////////////////////////////////////////////////////////////////////////////

function add_post_to_db(GooglePlusPost $post) {
}

///////////////////////////////////////////////////////////////////////////////

function google_plus_importer_page(){

    // Add any necessary users to the DB.
    add_all_new_users_to_db();

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
