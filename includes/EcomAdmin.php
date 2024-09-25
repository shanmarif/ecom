<?php

class EcomAdmin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $products_obj, $orders_obj;

	// customer WP_List_Table object
	public $groups_obj, $packages_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		add_action('admin_enqueue_scripts', function () {
        	wp_enqueue_style('product-form-css',
            	'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css');
            wp_enqueue_script('product-form-bootstrap',
                'https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js', [], false );
        });
        add_action('wp_ajax_add_new_product', [$this, 'add_new_product']);
        add_action('wp_ajax_edit_product', [$this, 'edit_product']);
        add_action('wp_ajax_add_new_group', [$this, 'add_new_group']);
        add_action('wp_ajax_edit_group', [$this, 'edit_group']);
		add_action('wp_ajax_add_new_package', [$this, 'add_new_package']);
        add_action('wp_ajax_edit_package', [$this, 'edit_package']);
	}

	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {
		$hook = add_menu_page(
			'Basic Ecommerce Solution',
			'Ecommerce',
			'manage_options',
			'simple_ecom_controlpanel',
			[ $this, 'product_list_page' ],
			ecom_icon_svg()
		);
		add_submenu_page(
			'simple_ecom_controlpanel', 
			'Products', 
			'Products', 
			'manage_options', 
			'simple_ecom_controlpanel', 
			[ $this, 'product_list_page' ]
		);
		$package = add_submenu_page(
			'simple_ecom_controlpanel', 
			'Packages', 
			'Packages', 
			'manage_options', 
			'packages', 
			[ $this, 'package_list_page' ]
		);
		$group = add_submenu_page(
			'simple_ecom_controlpanel', 
			'Groups', 
			'Groups', 
			'manage_options', 
			'groups', 
			[ $this, 'group_list_page' ]
		);
		add_submenu_page(
			'simple_ecom_controlpanel',
			'Add New',
			'Add Product',
			'manage_options',
			'add_new',
			[ $this, 'insert_product' ]
		);
		add_submenu_page(
			'simple_ecom_controlpanel',
			'View Orders',
			'Orders',
			'manage_options',
			'list_orders',
			[ $this, 'order_list_page' ]
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );
		add_action( "load-$group", [ $this, 'screen_option_group' ] );
		add_action( "load-$package", [ $this, 'screen_option_package' ] );
		add_action( "load-$order", [ $this, 'screen_option_order' ] );
	}

	/**
	* Screen options
	*/
	public function screen_option() {
		$option = 'per_page';
		$args = [
			'label' => 'Products',
			'default' => 20,
			'option' => 'products_per_page'
		];

		add_screen_option( $option, $args );

		$this->products_obj = new Products_List();
	}

	/**
	* Screen options for Group
	*/
	public function screen_option_group() {
		$option = 'per_page';
		$args = [
			'label' => 'Groups',
			'default' => 20,
			'option' => 'groups_per_page'
		];

		add_screen_option( $option, $args );

		$this->groups_obj = new Groups_List();
	}

	/**
	* Screen options for Package
	*/
	public function screen_option_order() {
		$option = 'per_page';
		$args = [
			'label' => 'Orders',
			'default' => 25,
			'option' => 'orders_per_page'
		];

		add_screen_option( $option, $args );

		$this->orders_obj = new Orders_List();
	}

	/**
	* Screen options for Package
	*/
	public function screen_option_package() {
		$option = 'per_page';
		$args = [
			'label' => 'Packages',
			'default' => 20,
			'option' => 'packages_per_page'
		];

		add_screen_option( $option, $args );

		$this->packages_obj = new Packages_List();
	}

	/**
	* Product List Page
	*/
	public function product_list_page() {
		if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], ["new","edit"])){
			echo $this->insert_product();
		} else {
			$aData = [
				'products_obj' => $this->products_obj
			];
			echo render_template('product-admin.php', $aData);
		}
	}

	/**
	* Package List Page
	*/
	public function package_list_page() {
		if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], ["new","edit"])){
			echo $this->insert_package();
		} else {
			$aData = [
				'packages_obj' => $this->packages_obj
			];
			echo render_template('package-admin.php', $aData);
		}
	}
    
    /**
	* Order List Page
	*/
	public function order_list_page() {
		$aData = [
			'orders_obj' => $this->orders_obj
		];
        echo render_template('order-admin.php', $aData);
	}

	/**
	* Group List Page
	*/
	public function group_list_page() {
		if(isset($_REQUEST['action']) && in_array($_REQUEST['action'], ["new","edit"])){
			echo $this->insert_group();
		} else {
			$aData = [
				'products_obj' => $this->groups_obj,
			];
			echo render_template('product-admin.php', $aData);
		}
	}

	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function insert_product(){
	    $oEcomModel = new EcomModel();
	    $aData['groups'] = $oEcomModel->getAllGroups();
	    $aData['packages'] = $oEcomModel->getAllPackages();
	    $aData['addons'] = $oEcomModel->getAllAddons();
	    if(isset($_REQUEST['product']) && !empty($_REQUEST['product'])){
	    	$id = $_REQUEST['product'];
		    $aData['selectedAddon'] = array_column($oEcomModel->getProductSelectedAddons($id), 'addon_id');
		    $aData['pricingDetails'] = array_column($oEcomModel->getPackageDetailsByProductId([$id]), null, 'package_id');
		    $aData['action'] = 'edit_product';
		}
		echo render_template('new-product.php', $aData);
	}

	public function insert_group()
	{
		$oEcomModel = new EcomModel();
		$aData['groups'] = $oEcomModel->getAllGroups();
		if(isset($_REQUEST['group']) && !empty($_REQUEST['group'])){
			$id = $_REQUEST['group'];
	    	$aData['groupDetails'] = $oEcomModel->getGroup($id);
	    	$aData['action'] = 'edit_group';
		}
		echo render_template('new-group.php', $aData);
	}

	public function insert_package()
	{
		$oEcomModel = new EcomModel();
		$aData['package'] = $oEcomModel->getAllPackages();
		if(isset($_REQUEST['package']) && !empty($_REQUEST['package'])){
			$id = $_REQUEST['package'];
	    	$aData['packageDetails'] = $oEcomModel->getPackage($id);
	    	$aData['action'] = 'edit_package';
		}
		echo render_template('new-package.php', $aData);
	}

	public function add_new_product(){
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$sImageUrl = $this->handleUploads($_FILES['prod_img']);
			$sSecondaryImageUrl = $this->handleUploads($_FILES['secondary_image']);
			$aData = $_POST;
			$aData['image'] = $sImageUrl;
			$aData['secondary_image'] = $sSecondaryImageUrl;
			$oEcomModel = new EcomModel();
			$oEcomModel->insertProduct($aData);
			wp_redirect('/wp-admin/admin.php?page=simple_ecom_controlpanel');
			exit;
		}
	}

	public function add_new_group()
	{
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			$aData = $_POST;
			$oEcomModel = new EcomModel();
			$oEcomModel->insertGroup($aData);
			wp_redirect('/wp-admin/admin.php?page=groups');
			exit;
		}
	}

	public function edit_group()
	{
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			$aData = $_POST;
			$oEcomModel = new EcomModel();
			$oEcomModel->updateGroup($aData);
			wp_redirect('/wp-admin/admin.php?page=groups');
			exit;
		}
	}

	public function add_new_package()
	{
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			$aData = $_POST;
			$oEcomModel = new EcomModel();
			$oEcomModel->insertPackage($aData);
			wp_redirect('/wp-admin/admin.php?page=packages');
			exit;
		}
	}

	public function edit_package()
	{
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			$aData = $_POST;
			$oEcomModel = new EcomModel();
			$oEcomModel->updatePackage($aData);
			wp_redirect('/wp-admin/admin.php?page=packages');
			exit;
		}
	}

	public function handleUploads($uploadedfile)
	{
		$upload_overrides = array( 'test_form' => false );
		$sImageUrl = "-";
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		if ( isset($movefile['url']) ) {
		    $sImageUrl = $movefile['url'];
		}
		return $sImageUrl??"-";
	}

	public function edit_product(){
		if ( empty($_POST)) {
	        echo 'You targeted the right function, but sorry, your nonce did not verify.';
	        die();
	    } else {
			if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
			$sImageUrl = $this->handleUploads($_FILES['prod_img']);
			$sSecondaryImageUrl = $this->handleUploads($_FILES['secondary_image']);
			$aData = $_POST;
			$aData['image'] = $sImageUrl;
			$aData['secondary_image'] = $sSecondaryImageUrl;
			$oEcomModel = new EcomModel();
			$oEcomModel->updateProduct($aData);
			wp_redirect('/wp-admin/admin.php?page=simple_ecom_controlpanel');
			exit;
		}
	}

}