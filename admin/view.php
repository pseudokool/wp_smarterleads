<?php
define( 'WPSL_PLUGIN', __FILE__ );

define( 'WPSL_PLUGIN_BASENAME',
	plugin_basename( WPSL_PLUGIN ) );

define( 'WPSL_PLUGIN_NAME',
	trim( dirname( WPSL_PLUGIN_BASENAME ), '/' ) );

define( 'WPSL_PLUGIN_DIR',
	untrailingslashit( dirname( WPSL_PLUGIN ) ) );

require_once WPSL_PLUGIN_DIR . '/includes/common.php';

// check user capabilities
if (!current_user_can('manage_options')) {
    return;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
if ( ! class_exists( 'Leads_Table' ) ) {
	require_once WPSL_PLUGIN_DIR . '/class.leads_table.php';
}

$tbl_leads = new Leads_Table();
$tbl_leads->prepare_items(); 


?>	
<div class="wrap">
	<h1><?= esc_html(get_admin_page_title()); ?></h1>
	<?php $tbl_leads->display(); ?>
</div>