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

function google_plus_importer_page(){
    echo "Hello Google+";
}

ossn_register_callback('ossn', 'init', 'google_plus_importer');