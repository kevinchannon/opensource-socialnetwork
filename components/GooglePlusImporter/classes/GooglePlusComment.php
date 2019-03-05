<?php
/**
 * User: kevin
 * Date: 05/03/2019
 * Time: 21:34
 */

class GooglePlusComment extends GooglePlusAuthoredItem
{
    private $content;

    function get_content() {
        return $this->content;
    }

    function set_content($new_content) {
        $this->content = $new_content;
    }
}