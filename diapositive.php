<?php
/*
Plugin Name: Slidesow dai contenuti
Plugin URI: https://github.com/creativita-digitale/Plugin-Diapositive
Description: Slideshow dai contenuti del sito, dagli articoli alle pagine o altro.
Version: 1.0
Author: Alex Righetto
Author URI: http://example.com
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



if ( is_admin() ) {
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
			'tested' => '3.3', // which version of WordPress is your plugin tested up to?
			'readme' => 'README.md', // which file to use as the readme for the version number
			'access_token' => '40e2d0d6b62404e70ed89537a18983483eed3e0a', // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
		);
		new WP_GitHub_Updater($config);
}


function ar_slide_slideshow_init()
{
	load_plugin_textdomain('slide_slideshow', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'ar_slide_slideshow_init');

// Register Custom Post Type

function build_slide()
{
	$slide_labels = array(
		'name' => _x('Slides', 'Post Type General Name', 'slide_slideshow') ,
		'singular_name' => _x('Slide', 'Post Type Singular Name', 'slide_slideshow') ,
		'menu_name' => __('Slide', 'slide_slideshow') ,
		'parent_item_colon' => __('Parent Slide', 'slide_slideshow') ,
		'all_items' => __('All slides', 'slide_slideshow') ,
		'view_item' => __('View Slides', 'slide_slideshow') ,
		'add_new_item' => __('Add new Slide', 'slide_slideshow') ,
		'add_new' => __('Add New', 'slide_slideshow') ,
		'edit_item' => __('Edit Slide', 'slide_slideshow') ,
		'update_item' => __('Update Slide', 'slide_slideshow') ,
		'search_items' => __('Search Slide', 'slide_slideshow') ,
		'not_found' => __('No Slides found', 'slide_slideshow') ,
		'not_found_in_trash' => __('No Slides found in Trash', 'slide_slideshow') ,
	);
	$slide_rewrite = array(
		'slug' => 'slide',
		'with_front' => true,
		'pages' => true,
		'feeds' => true,
	);
	$slide_args = array(
		'labels' => $slide_labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => $slide_rewrite,
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => 5,
		'menu_icon' => '',
		'supports' => array(
			'title',
			'editor',
			'thumbnail'
		)
	);
	register_post_type('slide', $slide_args);
}

// Hook into the 'init' action

add_action('init', 'build_slide');

function slide_rewrite_flush()
{



	build_slide();


	flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'slide_rewrite_flush');






function add_menu_icons_styles()
{
	echo '<style>';
	echo '#adminmenu .menu-icon-slide div.wp-menu-image:before {';
	echo 'content: "\f232"';
	echo '}';
	echo '</style>';
}

add_action('admin_head', 'add_menu_icons_styles');

// add filter to ensure the text slide, or slide, is displayed when user updates a slide

function codex_slide_updated_messages($messages)
{
	global $post, $post_ID;
	$messages['slide'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf(__('slide updated. <a href="%s">View slide</a>', 'slide_slideshow') , esc_url(get_permalink($post_ID))) ,
		2 => __('Custom field updated.', 'slide_slideshow') ,
		3 => __('Custom field deleted.', 'slide_slideshow') ,
		4 => __('slide updated.', 'slide_slideshow') ,
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf(__('slide restored to revision from %s', 'slide_slideshow') , wp_post_revision_title((int)$_GET['revision'], false)) : false,
		6 => sprintf(__('slide published. <a href="%s">View slide</a>', 'slide_slideshow') , esc_url(get_permalink($post_ID))) ,
		7 => __('slide saved.', 'slide_slideshow') ,
		8 => sprintf(__('slide submitted. <a target="_blank" href="%s">Preview slide</a>', 'slide_slideshow') , esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
		9 => sprintf(__('slide scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview slide</a>', 'slide_slideshow') ,

		// translators: Publish box date format, see http://php.net/date

		date_i18n(__('M j, Y @ G:i') , strtotime($post->post_date)) , esc_url(get_permalink($post_ID))) ,
		10 => sprintf(__('slide draft updated. <a target="_blank" href="%s">Preview slide</a>', 'slide_slideshow') , esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))) ,
	);
	return $messages;
}

add_filter('post_updated_messages', 'codex_slide_updated_messages');

// display contextual help for Books

function codex_add_help_text($contextual_help, $screen_id, $screen)
{

	// $contextual_help .= var_dump( $screen ); // use this to help determine $screen->id

	if ('slide' == $screen->id) {
		$contextual_help = '<p>' . __('Things to remember when adding or editing a book:', 'your_text_domain') . '</p>' . '<ul>' . '<li>' . __('Specify the correct genre such as Mystery, or Historic.', 'your_text_domain') . '</li>' . '<li>' . __('Specify the correct writer of the book.  Remember that the Author module refers to you, the author of this book review.', 'your_text_domain') . '</li>' . '</ul>' . '<p>' . __('If you want to schedule the book review to be published in the future:', 'your_text_domain') . '</p>' . '<ul>' . '<li>' . __('Under the Publish module, click on the Edit link next to Publish.', 'your_text_domain') . '</li>' . '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.', 'your_text_domain') . '</li>' . '</ul>' . '<p><strong>' . __('For more information:', 'your_text_domain') . '</strong></p>' . '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>', 'your_text_domain') . '</p>' . '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>', 'your_text_domain') . '</p>';
	}
	elseif ('edit-book' == $screen->id) {
		$contextual_help = '<p>' . __('This is the help screen displaying the table of books blah blah blah.', 'your_text_domain') . '</p>';
	}

	return $contextual_help;
}

add_action('contextual_help', 'codex_add_help_text', 10, 3);
/* Fire our meta box setup function on the post editor screen. */
add_action('load-post.php', 'slide_post_meta_boxes_setup');
add_action('load-post-new.php', 'slide_post_meta_boxes_setup');
/* Meta box setup function. */

function slide_post_meta_boxes_setup()
{
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action('add_meta_boxes', 'slide_add_post_meta_boxes');
	/* Save post meta on the 'save_post' hook. */
	add_action('save_post', 'slide_save_post_class_meta', 10, 2);
}

/* Create one or more meta boxes to be displayed on the post editor screen. */

function slide_add_post_meta_boxes()
{
	add_meta_box('slide-post-class', // Unique ID
	esc_html__('Link to site content', 'slide_slideshow') , // Title
	'slide_post_class_meta_box', // Callback function
	'slide', // Admin page (or post type)
	'normal', // Context
	'default'

	// Priority

	);
}

/* Display the post meta box. */

function slide_post_class_meta_box($object, $box)
{ ?>

	<?php
	wp_nonce_field(basename(__FILE__) , 'slide_post_class_nonce'); ?>

	<p>
		<label for="slide-post-class"><?php
	_e("Select the content of the site that this slide should be connected.", 'slide_slideshow'); ?></label>
		</p>
		<p>
	
        
       
        
        <select name="slide-post-class" id="slide-post-class">
        	
       
       <?php // The Query
	$query_args = array(
		'post_type' => array(
			'post',
			'page',
			'vini',
			'riconoscimento',
			'tribe_events'
		) ,
		'posts_per_page' => - 1
	);
	$the_query = new WP_Query($query_args);

	// The Loop

	while ($the_query->have_posts()):
		$the_query->the_post();
		echo '<option ';
		if (esc_attr(get_post_meta($object->ID, 'slide_post_class', true)) == get_the_ID()) {
			echo ' selected ';
		}

		echo 'value="' . get_the_ID() . '">' . get_the_title() . '</option>';
	endwhile;
	/* Restore original Post Data
	* NB: Because we are using new WP_Query we aren't stomping on the
	* original $wp_query and it does not need to be reset.
	*/
	wp_reset_postdata();
?>
        
         </select>
	</p>
<?php
}

/* Save the meta box's post metadata. */

function slide_save_post_class_meta($post_id, $post)
{
	/* Verify the nonce before proceeding. */
	if (!isset($_POST['slide_post_class_nonce']) || !wp_verify_nonce($_POST['slide_post_class_nonce'], basename(__FILE__))) return $post_id;
	/* Get the post type object. */
	$post_type = get_post_type_object($post->post_type);
	/* Check if the current user has permission to edit the post. */
	if (!current_user_can($post_type->cap->edit_post, $post_id)) return $post_id;
	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = (isset($_POST['slide-post-class']) ? sanitize_html_class($_POST['slide-post-class']) : '');
	/* Get the meta key. */
	$meta_key = 'slide_post_class';
	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta($post_id, $meta_key, true);
	/* If a new meta value was added and there was no previous value, add it. */
	if ($new_meta_value && '' == $meta_value) add_post_meta($post_id, $meta_key, $new_meta_value, true);
	/* If the new meta value does not match the old value, update it. */
	elseif ($new_meta_value && $new_meta_value != $meta_value) update_post_meta($post_id, $meta_key, $new_meta_value);
	/* If there is no new meta value but an old value exists, delete it. */
	elseif ('' == $new_meta_value && $meta_value) delete_post_meta($post_id, $meta_key, $meta_value);
} ?>