<?php
/**
 * User: kevin
 * Date: 05/03/2019
 * Time: 22:01
 */

class GooglePlusAuthoredItem
{
    private $date;
    private $author;

    function get_date() {
        return $this->date;
    }

    function set_date($new_date) {
        $this->date = $new_date;
    }

    function get_author() {
        return $this->author;
    }

    function set_author($new_author) {
        $this->date = $new_author;
    }
}