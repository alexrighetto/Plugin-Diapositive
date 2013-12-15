<?php
/*
Plugin Name: Slidesow dai contenuti
Plugin URI: https://github.com/creativita-digitale/Plugin-Diapositive
Description: Slideshow dai contenuti del sito, dagli articoli alle pagine o altro.
Version: 1.0
Author: Alex Righetto
Author URI: https://github.com/alexrighetto
License: GPLv2

  Copyright 2013  RIGHETTO ALEX  (email : alexrighetto@gmail.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	die('Sorry, but you cannot access this page directly.');
}




function ar_slide_slideshow_install() {
    If ( version_compare( get_bloginfo( 'version' ), '3.1', '<' ) ) {
        deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
    }
}
register_activation_hook( __FILE__, 'ar_slide_slideshow_install' );



function github_plugin_updater_test_init() {

	include_once 'updater.php';

	define( 'WP_GITHUB_FORCE_UPDATE', true );

	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin
    // we're in wp-admin
    require_once( dirname(__FILE__).'/updater.php' );
	$config = array(
			'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
			'proper_folder_name' => 'Plugin-Diapositive', // this is the name of the folder your plugin lives in
			'api_url' => 'https://api.github.com/repos/alexrighetto/Plugin-Diapositive', // the github API url of your github repo
			'raw_url' => 'https://raw.github.com/alexrighetto/Plugin-Diapositive', // the github raw url of your github repo
			'github_url' => 'https://github.com/alexrighetto/Plugin-Diapositive', // the github url of your github repo
			'zip_url' => 'https://github.com/alexrighetto/Plugin-Diapositive/zipball/master', // the zip url of the github repo
			'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
			'requires' => '3.0', // which version of WordPress does your plugin require?
			'tested' => '3.8', // which version of WordPress is your plugin tested up to?
			'readme' => 'README.md', // which file to use as the readme for the version number
			'access_token' => '40e2d0d6b62404e70ed89537a18983483eed3e0a', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
		);
		new WP_GitHub_Updater($config);
	}

}

add_action( 'init', 'github_plugin_updater_test_init' );

function ar_slide_slideshow_init()
{
	load_plugin_textdomain('slide_slideshow', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'ar_slide_slideshow_init');


    

require_once( dirname(__FILE__).'/admin.php' );

 ?>
