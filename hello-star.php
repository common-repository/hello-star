<?php

/*
 * Plugin Name: Hello Star
 * Version: 1.0.0
 * Description: Yet another plugin inspired by Hello Dolly. This plugin shows information about the 88 constellations and their stars that are most visible given your location, date and time.
 * Author: Sarah Al
 * Author URI: http://icodeforweb.com
 * Plugin URI: https://github.com/Sarahphp1/hello-star
 * License: GPL v2+
 * License URI: hhttp://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

// Make sure Wordpress version is 3.5 or higher since color picker has been introduced since -v 3.5.
global $wp_version;
$exitMsg = 'This plugin utilizes Wordpress iris color picker. Wordpress version 3.5 or higher is required.<br />
            <a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a>';
if (version_compare($wp_version,"3.5","<"))
{
    wp_die( $exitMsg );
}


/* ------------------------------------------------*/
/* -------------- Constellation -------------------*/
/* ------------------------------------------------*/

/**
 * Find file and access its content.
 * @return string
 */
function hs_hellostar_get_info() {

	$monthname = date('M');
	$monthname = strtolower($monthname);
	$month_file = $monthname . '.txt';

	// Get the constellation information file based on the current month.
	$const_file_path = plugin_dir_path( __FILE__ ) . 'constellations/' . $month_file;
	// Check if file exists.
	if ( empty( $month_file ) || ! file_exists( $const_file_path ) ) {
		return false;
	}
	// Get file content of the current month.
	$constellation = file_get_contents( $const_file_path );

	// Explode text into multiple lines.
	$constellation = explode( "\n", $constellation );

	// Choose a random line.
	return wptexturize( $constellation[ mt_rand( 0, count( $constellation ) - 1 ) ] );
}

// Execute 'hs_hellostar_show_info' function when the admin_notices hook is called
add_action( 'admin_notices', 'hs_hellostar_show_info' );

//
/**
 * Echoes the chosen random line.
 * @return string
 */
function hs_hellostar_show_info() {

	$chosen = hs_hellostar_get_info();

	if ( ! empty( $chosen ) ) {
		echo "<div id='hello_star'>$chosen</div>";
	}
}

/* ------------------------------------------------*/
/* --------------------- Menu ---------------------*/
/* ------------------------------------------------*/

//Register the hs_hellostar_menu() function under the admin_menu hook.
add_action( 'admin_menu', 'hs_hellostar_menu' );

/**
 * Add the options page under the settings menu.
 */
function hs_hellostar_menu() {
	//Generate admin page.
	add_options_page( 'Hello Star Options', 'Hello Star', 'manage_options', 'hello_star', 'hs_hellostar_options' );
	//Activate custom settings.
	add_action( 'admin_init', 'hs_hellostar_custom_settings' );
}

/**
 * Register and add fields.
 */
function hs_hellostar_custom_settings(){
	register_setting( 'hello_star', 'text_color');
	register_setting( 'hello_star', 'text_color_bg');
}

/**
 * Function to display the content of the option page.
 */
function hs_hellostar_options() {
	// The function handling the output of the options page should also verify the user's capabilities.
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	$textColor = esc_attr(get_option( 'text_color' ));
	$textBgColor = esc_attr(get_option( 'text_color_bg' ));
    /* ------------------ Nasa apod ------------------*/
	echo '
        <div class="wrap">
		<h1>Hello Star Settings</h1>
		<h4>Astronomy Picture of the Day - NASA apod</h4>
		<p id="apod_title" style="color: #808080"></p>
        <img id="apod_img_id" width="250px"/>
        <iframe id="apod_vid_id" type="text/html" width="640" height="385" frameborder="0"></iframe>
        <p id="copyright"></p>
        <hr>
		<h4>Choose the color of text and color of text background</h4>
		<form action="options.php" method="post">	
		';

	settings_fields( 'hello_star' );
	do_settings_sections( 'hello_star' );

	echo '
		<table class="form-table">
				<tr valign="top">
					<th scope="row">Text</th>
					<td>
						<input type="text" value="' .$textColor. '" class="text-color" name="text_color" placeholder="Hex code" data-default-color="#fff" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Text Background</th>
					<td>
						<input type="text" value="' .$textBgColor. '" class="text-bg-color" name="text_color_bg" placeholder="Hex code" data-default-color="#000" />
					</td>
				</tr>
		</table>
		';
	submit_button();
	echo '</form></div>';
}


// Add function hs_hellostar_color_picker to the admin_enqueue_scripts hook.
add_action( 'admin_enqueue_scripts', 'hs_hellostar_color_picker' );

/**
 * This function generates the Wordpress color picker.
 * And loads the custom javascript file needed for the color picker.
 */
function hs_hellostar_color_picker() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'hs-color-script', plugins_url( 'hello-star.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}

// Add function hs_hellostar_enqueue_styles to the admin_enqueue_scripts hook.
add_action( 'admin_enqueue_scripts', 'hs_hellostar_enqueue_styles' );

/**
 * This function applies the color choice of the user to the chosen line.
 * And adds some css styling.
 */
function hs_hellostar_enqueue_styles() {
	$textColor = esc_attr(get_option( 'text_color' ));
	$textBgColor = esc_attr(get_option( 'text_color_bg' ));
	$x = is_rtl() ? 'left' : 'right'; //Check language positioning - true if rtl
	echo '<style type="text/css">
		#hello_star{
			color: ' .$textColor. ' !important;
			background-color: ' .$textBgColor. ' !important;
			float: ' .$x. ';
			padding: 2px;
			margin: 0;
			font-size: 11px;
			border-radius: 5px;
			text-align: center;
			vertical-align: middle;
		}
		</style>
		';
}
