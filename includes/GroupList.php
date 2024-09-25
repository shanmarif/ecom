<?php
class Groups_List extends WP_List_Table {

	public $columns;

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Group', 'sp' ), //singular name of the listed records
			'plural' => __( 'Groups', 'sp' ), //plural name of the listed records
			'ajax' => true //should this table support ajax?
		] );
	}

	/**
	* Retrieve customer’s data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_groups( $per_page = 20, $page_number = 1 ) {
		global $wpdb;
		$sql = "SELECT 
				g.id, 
				g.name, 
				g2.name as parent_group,
				g.created_at,
				g.updated_at
			FROM {$wpdb->prefix}simple_ecom_groups g
			LEFT JOIN {$wpdb->prefix}simple_ecom_groups g2 on g2.id = g.parent_id WHERE g.parent_id >= 0";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	* Delete a customer record.
	*
	* @param int $id customer ID
	*/
	public static function delete_group( $id ) {
		global $wpdb;
		$wpdb->delete( "{$wpdb->prefix}simple_ecom_groups", [ 'id' => $id ], [ '%d' ]);
	}

	/**
	* Returns the count of records in the database.
	*
	* @return null|string
	*/
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT 
				COUNT(*)
			FROM {$wpdb->prefix}simple_ecom_groups WHERE parent_id >= 0";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		__( 'No groups avaliable.', 'sp' );
	}

	/**
	* Method for name column
	*
	* @param array $item an array of DB data
	*
	* @return string
	*/
	function column_name( $item ) {
		// create a nonce
		$delete_nonce = wp_create_nonce( 'sp_delete_group' );
		$edit_nonce = wp_create_nonce( 'sp_edit_group' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'edit' => sprintf( '<a href="/wp-admin/admin.php?page=%s&action=%s&group=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
			'delete' => sprintf( '<a href="/wp-admin/admin.php?page=%s&action=%s&group=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];

		return $title . $this->row_actions( $actions );
	}

	/**
	* Render a column when no column specific method exists.
	*
	* @param array $item
	* @param string $column_name
	*
	* @return mixed
	*/
	public function column_default( $item, $column_name ) {
		return isset($item[$column_name]) ? $item[$column_name] : "-";
	}

	/**
	* Render the bulk edit checkbox
	*
	* @param array $item
	*
	* @return string
	*/
	public function column_cb( $item ) {
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']);
	}

	/**
	* Associative array of columns
	*
	* @return array
	*/
	public function get_columns() {
		$columns = [
			'cb' => '<input type="checkbox" />',
			'name' => __( 'Group', 'sp' ),
			'parent_group' => __( 'Parent', 'sp' )
		];
		$columns['created_at'] = __( 'Created at', 'sp' );
		$columns['updated_at'] = __( 'Last Updated at', 'sp' );
		return $columns;
	}

	/**
	* Columns to make sortable.
	*
	* @return array
	*/
	public function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array( 'name', true ),
			'parent_group' => array( 'parent_group', false )
		);

		return $sortable_columns;
	}

	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions() {
		$actions = [
		'bulk-delete' => 'Delete'
		];

		return $actions;
	}

	/**
	* Handles data query and filter, sorting, and pagination.
	*/
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page = $this->get_items_per_page( 'groups_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_groups( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_group' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_group( absint( $_GET['group'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || 
			( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_groupt( $id );
			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}
}