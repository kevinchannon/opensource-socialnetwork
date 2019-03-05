<?php
/**
 * User: kevin
 * Date: 05/03/2019
 * Time: 21:27
 */

class GooglePlusPost extends GooglePlusAuthoredItem
{
    private $content;
    private $media_items = array();
    private $comments = array();

    function get_content() {
        return $this->content;
    }

    function set_content($new_content) {
        $this->content = $new_content;
    }

    function get_media_items() {
        return $this->media_items;
    }

    function add_media_item($new_item) {
        array_push($this->media_items, $new_item);
    }

    function get_comments() {
        return $this->comments;
    }

    function add_comment($new_comment) {
        array_push($this->comments, $new_comment);
    }
}