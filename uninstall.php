<?php
	/**
	* Fired when the plugin is uninstalled.
	*
	* @package wp_msarterleads
	* @author Carlyle Oliver <carlyleoliver@gmail.com>
	* @license GPL-2.0+
	* @link http://example.com
	* @copyright 2014 Your Name or Company Name
	*/

	// if uninstall.php is not called by WordPress, die
	if (!defined('WP_UNINSTALL_PLUGIN')) {
	    die;
	}
	 
	$option_name = 'wpsl_option'; 
	delete_option($option_name);
	 
	
?>