<?php

/*
 * Plugin Name: Photo slider
 * Plugin URI: 
 * Description: Photo slider
 * Version: 0.1
 * Author: Anonymous
 * Author URI: 
 * License: 
 */
 
addAction('photoslider', 'photoslider_boot');
function photoslider_boot($id){
	include 'view.php';
}