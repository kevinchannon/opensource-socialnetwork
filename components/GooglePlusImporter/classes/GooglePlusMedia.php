<?php
/**
 * User: kevin
 * Date: 05/03/2019
 * Time: 21:32
 */

/// Stores an instance of some media item.
abstract class GooglePlusMedia
{
    private $path;
    private $comments = array();

    abstract function get_type();

    function get_path() {
        return $this->path;
    }

    function set_path($new_path) {
        $this->path = $new_path;
    }

    function get_comments() {
        return $this->comments;
    }

    /// Add a new comment to this media item.
    function add_comment($new_comment) {
        array_push($this->comments, $new_comment );
    }
}

/// Stores the details of a photo.
class GooglePlusPhoto extends GooglePlusMedia
{
    function get_type() {
        return "photo";
    }
}

/// Stores the details of a movie.
class GooglePlusMovie extends GooglePlusMedia
{
    function get_type() {
        return "movie";
    }
}

function create_media_from_json_value($json_value) : GooglePlusMedia {
    switch ( $json_value ) {
        case "image/*" :
            return new GooglePlusPhoto();
            break;
        case "movie/*" :
            return new GooglePlusMovie();
            break;
    }

    return null;
}