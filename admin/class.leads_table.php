<?php

class Leads_Table extends WP_List_Table {

	var $filtered_data;	// paginated data
	  
 	function get_columns(){
	  $columns = array(
	    'cb'        	 => '<input type="checkbox" />',
	    'post_content' => 'Lead',
	    'post_date'    => 'Date',
	    'post_status'  => 'Status',
	    'post_author' =>  'Assigned To'
	  );
	  return $columns;
	}
	function get_views(){
   	$views = array();
 		$current = ( !empty($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');

   	$foo_url = add_query_arg('customvar','publish');
   	$class = ($current == 'publish' ? ' class="current"' :'');
   	$views['foo'] = "<a href='{$foo_url}' {$class} >New</a>";

   	$bar_url = add_query_arg('customvar','pending');
   	$class = ($current == 'pending' ? ' class="current"' :'');
   	$views['bar'] = "<a href='{$bar_url}' {$class} >Archived</a>";

   	$class = ($current == 'all' ? ' class="current"' :'');
   	$all_url = remove_query_arg('customvar');
   	$views['all'] = "<a href='{$all_url }' {$class} >All</a>";

	   
	   return $views;
	}

	function prepare_items() {
	  $customvar = ( isset($_REQUEST['customvar']) ? $_REQUEST['customvar'] : 'all');

	  $columns = $this->get_columns();
	  $hidden = array();
	  $sortable = $this->get_sortable_columns();
	  $this->_column_headers = array($columns, $hidden, $sortable, $customvar);

	  $per_page = 4;
	  $current_page = $this->get_pagenum();
	  $total_items = count($this->get_leads_count());

	  $this->filtered_data = $this->get_leads($current_page, $per_page);
	  
	  $this->set_pagination_args( array(
	    'total_items' => $total_items,                   
	    'per_page'    => $per_page                     
	  ) );
	  $this->items = $this->filtered_data;
	}

	/* Fetch leads, paginated
	 *
	 */
	function get_leads( $page_number = 1, $per_page = 5 ) {
		
		global $wpdb;

	  $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='flamingo_inbound'";
	  if ( ! empty( $_REQUEST['s'] ) ) {
	    $sql .= ' AND post_content LIKE \'%' . esc_sql( $_REQUEST['s']) ."%'";
	  }
	  if ( ! empty( $_REQUEST['orderby'] ) ) {
	    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
	    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  } else {
	  	$sql .= ' ORDER BY post_date DESC';
	  }

	  $sql .= " LIMIT $per_page";
	  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
	  //echo $sql;
	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );
	  
	  return $result;
	}

	/* Fetch lead count
	 *
	 */
	function get_leads_count() {
		
		global $wpdb;

	  $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type='flamingo_inbound'";
	  if ( ! empty( $_REQUEST['s'] ) ) {
	    $sql .= ' AND post_content LIKE \'%' . esc_sql( $_REQUEST['s']) ."%'";
	  }
	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );
	  
	  return $result;
	}

	function get_sortable_columns() {
	  $sortable_columns = array(
	    'post_content'  => array('post_content',false),
	    'post_date' => array('post_date',false),
	    'post_status'   => array('post_status',false)
	  );
	  return $sortable_columns;
	}

	// deprecated
	function usort_reorder( $a, $b ) {
	  // default to post date
	  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'post_date';
	  // default to desc
	  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
	  
	  $result = strcmp( $a[$orderby], $b[$orderby] );
	  return ( $order === 'desc' ) ? $result : -$result;
	} 

	/**
	 * Adds actionable buttons
	 * @param  array $item Single post of the type specified by options
	 * @return string       Field text and added action buttons
	 */
	function column_post_status($item) {
	  $actions = array(
	            'edit'      => sprintf('<a href="?page=%s&action=%s&lead=%s">Edit</a>',$_REQUEST['page'],'edit',$item['ID']),
	            'delete'    => sprintf('<a href="?page=%s&action=%s&lead=%s">Archive</a>',$_REQUEST['page'],'archive',$item['ID']),
	        );

	  return sprintf('%1$s %2$s', ($item['post_status']=='publish')?'<span class="badge badge-new">new</span>':'Archived', $this->row_actions($actions) );
	}

	/**
	 * Special case, to add checkboxes to the data table
	 * @param  [type] $item [description]
	 * @return [type]       [description]
	 */
	function column_cb($item) {
      return sprintf(
          '<input type="checkbox" name="lead[]" value="%s" />', $item['ID']
      );    
  }

  /**
   * Perform bulk tasks
   * @return [type] [description]
   */
	function get_bulk_actions() {
	  $actions = array(
	    'Archive'    => 'Archive',
	    'delete'    => 'Delete'
	  );
	  return $actions;
	}

	/**
	 * Common column output filters.
	 * @param  [type] $item        [description]
	 * @param  [type] $column_name [description]
	 * @return [type]              [description]
	 */
	function column_default( $item, $column_name ) {
	  switch( $column_name ) { 
	    case 'post_date':
	    	$d = strtotime($item['post_date']);
	  		return date('d M Y, h:ia', $d);
	  		break;
			case 'post_content':
	    	$c = explode("\n",$item['post_content']);
	    	return '<b>' . @$c[0] . '</b> <br/> ' . @$c[1];
	    	break;
	    case 'post_author':
	    	$args = array(
			    'orderby'       => 'name', 
			    'order'         => 'ASC', 
			    'exclude_admin' => false, 
			    'show_fullname' => false,
			    'hide_empty'    => true,
			    'echo'          => true,
			    'style'         => 'list',
			    'html'          => true,
		 		); 
		  	$users = get_users();
		  	$s = '<select id="wpsl_assig_'.$item['ID'].'">';
		  	foreach ( $users as $user ) {
		  		$selected = '';
		  		if($user->ID==$item['post_author']) $selected = 'selected';
					$s .= '<option '.$selected.' value="'.$item['ID'].'##'.$user->ID.'" class="wpsl_assigned">' . esc_html( ucfirst($user->user_nicename) ) . '</option>';
				}
				$s .= '</select>';
		  	return $s;
		  	break;
			case 'post_status':
	    	return $item[ $column_name ];
	    
	    default:
	      return print_r( $item, true ) ; // debug
	  }
	}

	/**
	 * Triggered when no data is retrieved.
	 * @return [type] [description]
	 */
	function no_items() {
	  _e( 'Nope, no leads here.' );
	}

}
?>