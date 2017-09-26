<?php
	/*
	Plugin Name: Smarter Leads
	Plugin URI:  http://pseudokool.github.io/wp_smarterleads/
	Description: A Wordpress plugin, that add a CRM-like functionality to leads generated.
	Version:     1.0a
	Author:      pseudokool
	Author URI:  https://github.com/pseudokool

	Smarter Leads is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.
	 
	Smarter Leads is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
	 
	You should have received a copy of the GNU General Public License
	along with Newstream. If not, see {License URI}.
	*/

	register_activation_hook( __FILE__, 'wpsl_activate()' );
	register_deactivation_hook(__FILE__, 'wpsl_deactivate()');

	// watch for post add/updates
	add_action('save_post', 'wpsl_save_lead', 10, 2);
	function wpsl_save_lead($post_id, $post){
		// echo 'POST_ID | '.$post_id;
		// print_r($post);
		//exit;

	}

	// render menu
	function wpsl_options_page()
	{
	    add_menu_page(
	        'WordPress SmarterLeads',
	        'Leads',
	        'manage_options',
	        plugin_dir_path(__FILE__) . 'admin/view.php',
	        null,
	        'dashicons-universal-access',
	        20
	    );
	}
	add_action('admin_menu', 'wpsl_options_page');

	function wpsl_activate(){

	}
	function wpsl_deactivate(){
		
	}

?>