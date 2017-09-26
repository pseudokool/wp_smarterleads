<?php

define( 'DOING_AJAX', true );
if ( ! defined( 'WP_ADMIN' ) ) {
	define( 'WP_ADMIN', true );
}

/** Load WordPress Bootstrap */
require_once( '../../../../wp-load.php' );

check_ajax_referer( 'bk-ajax-nonce', 'security' );

// retvals
$retval['msg'] = '';
$retval['code'] = '200';

switch($_REQUEST['action']){
	case 'assign':
		$my_post = array(
      'ID'           => $_REQUEST['p'],
      'post_author' => 	$_REQUEST['a'],
  );
  wp_update_post( $my_post );	
  if (is_wp_error($post_id)) {
		$retval['code'] = '500';
		$retval['msg'] = 'Something went wrong.';
	} else {
		$retval['code'] = '200';
		$retval['msg'] = 'Lead assigned.';
	}
	break;

	default:
}

echo json_encode($retval);

?>