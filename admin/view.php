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
	<h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>

	<?php
		if ( isset( $_REQUEST['s'] ) && strlen( $_REQUEST['s'] ) ) {
			/* translators: %s: search keywords */
			printf( ' <span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', $_REQUEST['s'] );
		}

		if ( isset($messages) && $messages ){ $message_show ='block';} else {$message_show = 'none';}
		echo '<div id="message" class="updated notice is-dismissible" style="display:'.$message_show.';"><p>' . ((isset($messages))?join( ' ', $messages ):'') . '</p></div>';
		unset( $messages );
	?>

	<?php /*$tbl_leads->views();*/ ?>
  <form id="posts-filter" method="post">
	  <input type="hidden" name="page" value="tbl_leads" />
	  <?php $tbl_leads->search_box('search', 'search_id'); ?>
	</form>
	<?php $tbl_leads->display(); ?>
</div>

<script type="text/javascript">

	jQuery(document).ready(function(){
		jQuery( "select[id^=wpsl_assig]" ).change(function() {
			pa=this.value.split('##')
		  jQuery.ajax({
			  type: 'POST',
			  dataType: "json",
			  url: '<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/wp_smarterleads/admin/wpsl_ajax.php',
			  data: "action=assign&p="+pa[0]+"&a="+pa[1]+"&security=<?php echo wp_create_nonce( "bk-ajax-nonce" ); ?>",
			  success: function(data){
			    //console.log(data.msg)
			    jQuery('#message p').html(data.msg);
			    jQuery('#message').fadeIn();

			  },
			  error: function(xhr, type, exception) { 
			    // if ajax fails display error alert
			    alert("ajax error response type "+type);
			  }
			});
		});
	});

</script>