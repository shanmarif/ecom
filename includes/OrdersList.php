<?php
class Orders_List extends WP_List_Table {

	public $columns;

	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Order', 'sp' ), //singular name of the listed records
			'plural' => __( 'Orders', 'sp' ), //plural name of the listed records
			'ajax' => true //should this table support ajax?
		] );
		$oEcomModel = new EcomModel();
		$this->columns = array_column($oEcomModel->getAllOrders(), 'name', 'name');
	}

	/**
	* Retrieve customer’s data from the database
	*
	* @param int $per_page
	* @param int $page_number
	*
	* @return mixed
	*/
	public static function get_orders( $per_page = 25, $page_number = 1 ) {
		global $wpdb;
		$sql = "SELECT 
				o.id,
				o.name,
				o.email,
				o.phone_number,
				o.payment_method,
				o.grand_total,
				o.status,
				o.payment_date
			FROM {$wpdb->prefix}simple_ecom_orders o";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' DESC';
		} else {
			$sql .= ' ORDER BY o.id DESC';
		}

		$sql .= " LIMIT $per_page";

		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
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
			FROM {$wpdb->prefix}simple_ecom_orders o";

		return $wpdb->get_var( $sql );
	}

	/** Text displayed when no customer data is available */
	public function no_items() {
		__( 'No Orders avaliable.', 'sp' );
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
		$view_nonce = wp_create_nonce( 'sp_view_order' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = [
			'view' => sprintf( '<a href="?page=%s&action=%s&order_id=%s">View Order Details</a>', esc_attr( $_REQUEST['page'] ), 'view', absint( $item['id'] ), $view_nonce )
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
			// 'id' => __( 'Order ID', 'sp' ),
			'name' => __( 'Name', 'sp' ),
			'email' => __( 'Email', 'sp' ),
			'phone_number' => __( 'Phone No.', 'sp' ),
			'payment_method' => __( 'Payment Method', 'sp' ),
			'grand_total' => __( 'Order Amount', 'sp' )
		];
		$columns = array_merge($columns, $this->columns);
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
			'email' => array( 'email', true ),
			'payment_method' => array( 'payment_method', false )
		);

		return $sortable_columns;
	}

	/**
	* Returns an associative array containing the bulk action
	*
	* @return array
	*/
	public function get_bulk_actions() {
		$actions = [];

		return $actions;
	}

	/**
	* Handles data query and filter, sorting, and pagination.
	*/
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page = $this->get_items_per_page( 'orders_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page' => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_orders( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_product' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
				self::delete_product( absint( $_GET['product'] ) );

				wp_redirect( esc_url( add_query_arg() ) );
				exit;
			}

		}

		if ( 'edit' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_edit_product' ) ) {
				die( 'Go get a life script kiddies' );
			} else {
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
				self::delete_product( $id );
			}

			wp_redirect( esc_url( add_query_arg() ) );
			exit;
		}
	}
}