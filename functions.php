<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

require_once( 'includes/EcomAdmin.php' );
require_once( 'includes/ProductsList.php' );
require_once( 'includes/GroupList.php' );
require_once( 'includes/PackagesList.php' );
require_once( 'includes/PaypalIPN.php');
require_once( 'includes/StripeIPN.php');
require_once( 'models/ecommodel.php' );
require_once( 'vendor/stripe/init.php' );

add_action('init', 'start_session', 0);


function start_session() {
	if(!session_id()) {
		session_start();
	}
}

function simple_ecom_enqueue_scripts(){
	wp_enqueue_script('ecom-js', plugin_dir_url( __FILE__ ).'/assets/js/ecom.js',[],CURRENT_APPLICATION_VERSION, true);
}

add_action( 'wp_enqueue_scripts', 'simple_ecom_enqueue_scripts' );

function get_product_slider( $atts = [], $content = null ){
	global $wpdb;
	$type = isset($atts["type"]) && $atts["type"] != "" ? implode('","', explode(",",$atts["type"])) : null;
	if(is_null($type)){
		$whereType = "ep.type != 'additional-services'";
	} else {
		$whereType = 'ep.type IN ("'.$type.'")';
	}
	$sSql = "SELECT 
		ep.`id` AS prod_id, 
		eg.*, 
		ep.`name` AS product, 
		ep.`type`, 
		ep.`description` AS prod_desc, 
		p.post_name as link,
		ep.`image`, 
		ep.`slug` as prod_slug,
		epp.`price`
	FROM {$wpdb->prefix}simple_ecom_groups eg
	INNER JOIN {$wpdb->prefix}simple_ecom_products ep ON ep.group_id = eg.id
	LEFT JOIN {$wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
	LEFT JOIN {$wpdb->prefix}posts p on p.id = ep.description and p.post_type = 'product_page'
	WHERE {$whereType}
	GROUP BY prod_id
	ORDER BY ep.id, epp.package_id ASC";
	$aResult = $wpdb->get_results($sSql, 'ARRAY_A');
	return render_template('product-slider.php', ['aData' => $aResult]);
}

add_shortcode("productslider", "get_product_slider");


function get_related_product(){
	global $wpdb;

	$sSql = "SELECT 
		ep.`id` AS prod_id, 
		eg.*, 
		ep.`name` AS product, 
		ep.`type`, 
		ep.`description` AS prod_desc, 
		p.post_name as link,
		ep.`image`, 
		ep.`slug` as prod_slug,
		epp.`price`
	FROM {$wpdb->prefix}simple_ecom_groups eg
	INNER JOIN {$wpdb->prefix}simple_ecom_products ep ON ep.group_id = eg.id
	LEFT JOIN {$wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
	LEFT JOIN {$wpdb->prefix}posts p on p.id = ep.description and p.post_type = 'product_page'
	WHERE ep.type != 'additional-services'
	GROUP BY prod_id
	ORDER BY RAND(), ep.id, epp.package_id ASC
	LIMIT 3";
	$aResult = $wpdb->get_results($sSql, 'ARRAY_A');
	return render_template('related-products.php', ['aData' => $aResult]);
}
add_shortcode("relatedproduct", "get_related_product");

function get_product_packages($atts = []){
	global $wpdb;
	$aAttributes = shortcode_atts([
		'product' => get_query_var('product',false),
		'type' => get_query_var('type',false),
		'elementor' => get_query_var('elementor', false),
		'hidepackage' => get_query_var('hidepackage', false),
	], $atts);

	$sProduct = $aAttributes['product'];
	$sType = $aAttributes['type'];

	if($sProduct){
		$sSql = "SELECT 
			ep.id AS prod_id, 
			ep.name as prod_name, 
			ep.slug as prod_slug,
			ep.description as post_details,
			ep.image,
			ep.secondary_image,
			epc.name,
			epc.slug,
			epc.description,
			epp.id as price_id,
			epp.price,
			epp.package_details
		FROM {$wpdb->prefix}simple_ecom_products ep
		INNER JOIN {$wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
		INNER JOIN {$wpdb->prefix}simple_ecom_packages epc ON epc.id = epp.package_id
		WHERE ep.slug = '{$sProduct}' AND ep.type = '{$sType}'";
		$aResult = $wpdb->get_results($sSql, 'ARRAY_A');
		return render_template('product-packages.php', ['aData' => $aResult, 'aAttr' => $aAttributes]);
	}
}

add_shortcode("productpackages", "get_product_packages");


function get_product_form(){
	global $wpdb;
	$iPriceID = isset($_POST['pricing_id']) ? $_POST['pricing_id'] : false;
	if($iPriceID){
		$oEcomModel = new EcomModel();
		$aData = $oEcomModel->getProductDetailsByPricingId($iPriceID);
		$aProdDetails = $oEcomModel->getProductTypeByPricingId($iPriceID);

		$sTemplate = 'product-form.php';
		if(isset($aProdDetails["prod_type"])){
			$sTemplate = get_product_form_template($aProdDetails["prod_type"]);
		}
		remove_filter('the_content','wpautop');
		return shortcode_unautop(render_template($sTemplate, ['aData' => $aData]));
	}
}

add_shortcode("productform", "get_product_form");

add_shortcode("getproductsbygroup", "get_all_products_by_group");

/*
function get_all_products_by_group(){
	
	$sGroup = get_query_var('group',false);
	$sType = get_query_var('type',"trademark");

	$oEcomModel = new EcomModel();
	$aResult = $oEcomModel->getAllProductsByGroup($sType, $sGroup);
	return render_template('product-list.php', ['aData' => $aResult]);
}
*/

function get_all_products_by_group($atts = []){
	
	$aAttributes = shortcode_atts([
		'group' => get_query_var('group',false),
		'type' => get_query_var('type',"trademark")
	], $atts);

	$sGroup = $aAttributes['group'];
	$sType = $aAttributes['type'];

	$oEcomModel = new EcomModel();
	$aResult = $oEcomModel->getAllProductsByGroup($sType, $sGroup);
	return render_template('product-list.php', ['aData' => $aResult]);
}


function get_session_value($sKey, $sDefault = ""){
	return $_SESSION[$sKey]??$sDefault;
}

function set_session_value($sKey, $data)
{
	$_SESSION[$sKey] = $data;
}

function get_cart(){
	global $wpdb;
	$aCartDetails = get_session_value('trademark_data');

	if($aCartDetails){
		$oEcomModel = new EcomModel();
		$aAddonDetails = [];
		$aAddons = array_column($aCartDetails['products'], 'addon');
		
		if(!empty(array_filter($aAddons))){
			try {
				$aAddons = count($aAddons) > 1 ? array_filter($aAddons) : $aAddons;
				$aAddons = array_merge(...$aAddons);

				$aAddonDetails = array_column($oEcomModel->getAddonDetails($aAddons), null, 'addon_id');
			} catch (\Exception $e) {
				$aAddonDetails = [];
			}
		}

		$aPackages = array_column($aCartDetails['products'], 'package_id');
		
		$aProductPricings = $oEcomModel->getPackageDetails($aPackages);

		$aPricingDetails = array_column($aProductPricings, 'price', 'id');
		$aClassPricingDetails = array_column($aProductPricings, 'class_price', 'id');
		$aProductImages = array_column($aProductPricings, 'image', 'id');

		
		$aAdditionalServices = $oEcomModel->getProductPricingByType('additional-services');

		return render_template('product-cart.php', [
			'aAddonDetails' => $aAddonDetails,
			'aCartDetails' => $aCartDetails,
			'aPricingDetails' => $aPricingDetails,
			'aProductPricings' => $aProductPricings,
			'aClassPricingDetails' => $aClassPricingDetails,
			'aProductImages' => $aProductImages,
			'aAdditionalServices' => $aAdditionalServices
		]);
	}
}
add_shortcode("cart", "get_cart");

function checkout_shortcode(){
	wp_enqueue_script('worldpay', 'https://cdn.worldpay.com/v1/worldpay.js');
	
	return render_template('checkout.php');
}
add_shortcode("checkout", "checkout_shortcode");

if(!function_exists('get_product_form_template')){
function get_product_form_template($sType = ""){
	$sTemplateFileName = $sType."-product-form.php";
	$file = get_template_directory()."/template/".$sTemplateFileName;
	if(!file_exists($file)){
		$file = plugin_dir_path( __FILE__ )."/template/".$sTemplateFileName;
		$sTemplateFileName = file_exists($file) ? $sTemplateFileName : "product-form.php";
	}
	return $sTemplateFileName;
}
}

if(!function_exists('render_template')){
function render_template($template, array $context = []) {
	
	remove_filter('the_content','wpautop');
	$file = get_template_directory()."/template/".$template;
	if(!file_exists($file)){
		$file = plugin_dir_path( __FILE__ )."/template/".$template;
	}
    $contents = '';

    try {
        if (file_exists($file)) {
            ob_start(); // start output buffer
            require_once $file;
            $contents = ob_get_contents(); // get contents of buffer
            ob_end_clean();
        }
    } catch ( Exception $e) {
        error_log("render_template error occurred when parsing template {$e->getMessage()}");
        error_log("render_template Trace: {$e->getTraceAsString()}");
    }

    return preg_replace("/[\r\n]+/", "", $contents);
}
}

add_action( 'init',  function() {
    $labels = array(
        'name'                => 'Product Pages',
        'singular_name'       => 'Product Page',
        'menu_name'           => 'Product Pages',
        'all_items'           => 'All Product Pages',
        'view_item'           => 'View',
        'add_new_item'        => 'Add New',
        'add_new'             => 'Add New',
        'edit_item'           => 'Edit Product Page',
        'not_found'           => 'Not Found',
        'not_found_in_trash'  => 'Not found in Trash',
    );
	// Set other options for Custom Post Type
    $args = array(
        'description'         => 'Product Page',
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields' ),
        'hierarchical'        => true,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest' => true
    ); 
    register_post_type( 'product_page', $args );
	add_rewrite_rule( '^group/(.*)/(.*)/?', 'index.php?pagename=group&type=$matches[1]&group=$matches[2]', 'bottom' );
	add_rewrite_rule( '^group/(.*)/?', 'index.php?pagename=group&type=$matches[1]', 'bottom' );
	add_rewrite_rule( '^trademark/(.*)/?', 'index.php?pagename=trademark&type=trademark&product=$matches[1]', 'bottom' );
	add_rewrite_rule( '^design/(.*)/?', 'index.php?pagename=design&type=design&product=$matches[1]', 'bottom' );
	
	add_rewrite_tag('%product%', '([^&]+)');
	add_rewrite_tag('%type%', '([^&]+)');
	add_rewrite_tag('%group%', '([^&]+)');
		// Set UI labels for Custom Post Type
	add_filter('query_vars', function( $query_vars ){
		$query_vars[] = 'product';
		$query_vars[] = 'type';
		$query_vars[] = 'group';
		return $query_vars;
	});
	flush_rewrite_rules();
}, 1);

// function custom_rewrite_tags() {
// 	add_rewrite_tag('%product%', '([^&]+)');
// 	add_rewrite_tag('%type%', '([^&]+)');
// 	add_rewrite_tag('%group%', '([^&]+)');
// }
// add_action("init", "custom_rewrite_tags", 10, 0);

// function custom_rewrite_rules() {
// 	add_rewrite_rule( '^group/(.*)/(.*)/?', 'index.php?pagename=group&type=$matches[1]&group=$matches[2]', 'bottom' );
// 	add_rewrite_rule( '^group/(.*)/?', 'index.php?pagename=group&type=$matches[1]', 'bottom' );
// 	add_rewrite_rule( '^trademark/(.*)/?', 'index.php?pagename=trademark&type=trademark&product=$matches[1]', 'bottom' );
// 	add_rewrite_rule( '^design/(.*)/?', 'index.php?pagename=design&type=design&product=$matches[1]', 'bottom' );
// }

// add_action("init", "custom_rewrite_rules", 10, 0);


function ipprotector_remove_cpt_slug( $post_link, $post ) {
    if ( 'product_page' === $post->post_type && 'publish' === $post->post_status ) {
        $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );
    }
    return $post_link;
}
add_filter( 'post_type_link', 'ipprotector_remove_cpt_slug', 10, 2 );

function ipprotector_add_cpt_post_names_to_main_query( $query ) {
	if(isset($query->query['post_type'])){
        return;
    }
	$aPostType = $query->get('post_type');
	if(!empty($aPostType)){
		array_push($aPostType, 'product_page');
	} else {
		$aPostType = ['product_page', 'page', 'post'];
	}
	
    if (!empty($query->query['name']) && !is_admin()) {
        $query->set('post_type', $aPostType);
    }
}
add_action( 'pre_get_posts', 'ipprotector_add_cpt_post_names_to_main_query', 11 );

add_action('wp_ajax_add_product_to_cart', 'add_product_to_cart');
add_action('wp_ajax_nopriv_add_product_to_cart', 'add_product_to_cart');

function add_product_to_cart() {
    // if ( empty($_POST) || !wp_verify_nonce($_POST['security-code-here'],'add_product_to_cart') ) {
    if ( empty($_POST)) {
        echo 'You targeted the right function, but sorry, your nonce did not verify.';
        die();
    } else {
    	$base64File = "-";
    	if(isset($_FILES['product_images']) && !empty($_FILES['product_images'])){
			$uploadedfile = $_FILES['product_images'];
			if(!empty($uploadedfile['tmp_name'])){
				$sUploadFile = file_get_contents($uploadedfile['tmp_name']);
				$type = pathinfo($uploadedfile['name'], PATHINFO_EXTENSION);
				$base64File = 'data:image/' . $type . ';base64,' . base64_encode($sUploadFile);
			}
		}
		$aTradeMarkData = get_session_value('trademark_data',[]);
		$aTradeMarkData['products'][] = [
			'trademark_type' => $_POST['Trademark_type'],
			'trademark_text' => $_POST['Trademark_text'],
			'trademark_logo' => $base64File,
			'trademark_inuse' => $_POST['Trademark_inuse'],
			'trademark_date' => !empty($_POST['Trademark_date']) ? $_POST['Trademark_date'] : '-',
			'addon' => $_POST['addon'],
			'unsure-goods-notes' => $_POST['unsure-goods-notes'],
			'unsure-service-notes' => $_POST['unsure-service-notes'],
			'email' => $_POST['email'],
			'name' => $_POST['name'],
			'phone' => $_POST['phone'],
			'package_id' => $_POST['package_id'],
			'product_id' => $_POST['product_id'],
			'package_name' => $_POST['package_name'],
			'product_name' => $_POST['product_name'],
			'notes' => $_POST['notes']
		];
		$aTradeMarkData['customer'] = [
			'email' => $_POST['email'],
			'name' => $_POST['name'],
			'phone' => $_POST['phone'],
			'type' => $_POST['product_type']
		];
		set_session_value('trademark_data', $aTradeMarkData);
        // do your function here 
        wp_redirect('/cart');
        exit();
    }
}

function save_base64_image($base64Image, $post_id = 0) {
    // Get the upload directory
    $upload_dir = wp_upload_dir();

    // Split the base64 string into the type and the data
    list($type, $data) = explode(';', $base64Image);
    list(, $data) = explode(',', $data);
    
    // Check the image type (e.g., png, jpeg)
    if (strpos($type, 'image/') !== false) {
        $image_type = str_replace('data:image/', '', $type); // Get the file extension
    } else {
        return new WP_Error('invalid_image_type', 'Invalid image type');
    }

    // Decode the base64 string
    $decoded_image = base64_decode($data);

    // Generate a unique file name
    $filename = uniqid() . '.' . $image_type;

    // Full path to the upload directory
    $filepath = $upload_dir['path'] . '/' . $filename;

    // Save the image file
    if (file_put_contents($filepath, $decoded_image)) {
        // Get the file type (MIME type)
        $filetype = wp_check_filetype($filename, null);

        // Prepare an array of post data for the attachment
        $attachment = array(
            'post_mime_type' => $filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );

        // Insert the attachment into the media library
        $attach_id = wp_insert_attachment($attachment, $filepath, $post_id);

        // Generate metadata for the attachment, and update the attachment meta
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attach_id, $filepath);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id; // Return the attachment ID
    } else {
        return new WP_Error('image_save_error', 'Failed to save the image');
    }
}

function remove_item_from_cart(){
	if(!empty($_POST['id'])){
		$iPackageId = str_replace('package-','',$_POST['id']);
		$aOld = $aCartDetails = get_session_value('trademark_data');
		if(isset($aCartDetails['products'], $aCartDetails['products'][$iPackageId])){
			unset($aCartDetails['products'][$iPackageId]);
		}
		$aCartDetails['products'] = array_values($aCartDetails['products']);
		set_session_value('trademark_data', $aCartDetails);
	}
	return [
		"success" => true,
		"messages" => "Item removed successfully"
	];
}

function add_to_cart_additional_service(){
	$iServiceId = $_POST['id'];
	$aCartDetails = get_session_value('trademark_data');
	$aCartDetails['additional_services'] = !empty($aCartDetails['additional_services']) ? $aCartDetails['additional_services'] : [];
	$aCartDetails['additional_services'][] = $iServiceId;
	$aCartDetails['additional_services'] = array_unique($aCartDetails['additional_services']);
	set_session_value('trademark_data', $aCartDetails);
	return [
		"success" => true,
		"messages" => "Item added successfully"
	];
}

function remove_from_cart_additional_service(){
	$iServiceId = $_POST['id'];
	$aCartDetails = get_session_value('trademark_data');
	if(isset($aCartDetails['additional_services'])){
		$iIndex = array_search($iServiceId, $aCartDetails['additional_services']);
		unset($aCartDetails['additional_services'][$iIndex]);
		set_session_value('trademark_data', $aCartDetails);
	}
	return [
		"success" => true,
		"messages" => "Item removed successfully"
	];
}

add_action('wp_ajax_checkout', 'checkout');
add_action('wp_ajax_nopriv_checkout', 'checkout');

function checkout(){
	try {
		if(!empty($_POST['paymentmethod'])){
			$aOrderDetail = generateOrder($_POST['paymentmethod']);
			
			switch ($_POST['paymentmethod']){
				case "paypal":
					$aParamDetails = generatePaypalOrder($aOrderDetail);
					// sendOrderGenerateNotificationToCustomer($aOrderDetail);
					break;
				case "stripe":
					$aParamDetails = generateStripeOrder($aOrderDetail);
					// sendOrderGenerateNotificationToCustomer($aOrderDetail);
					break;
				case "worldpay":
					$aParamDetails = generateWorldPayOrder($aOrderDetail);
					// sendOrderGenerateNotificationToCustomer($aOrderDetail);
					break;
				default:
					sendOrderGenerateNotificationToCustomer($aOrderDetail);
					wp_redirect('/thankyou?orderid='.$aOrderDetail['iOrderId']);exit;
					break;
			}
			echo render_template('form-post.php', $aParamDetails);exit;

		}
	} catch (\Exception $e){
		echo "<pre>";print_r($e);exit;
	}
}

function generateOrder($sPaymentMethod){
	try {
		global $wpdb;
		$aOrderDetails = getCartDetails();
		if(isset($aOrderDetails["aCustomerDetails"])){
			$sCustomerName = $aOrderDetails["aCustomerDetails"]["name"] ?? "-";
			$sCustomerEmail = $aOrderDetails["aCustomerDetails"]["email"] ?? "-";
			$sCustomerPhone = $aOrderDetails["aCustomerDetails"]["phone"] ?? "";
		} else {
			$sCustomerName = "-";
			$sCustomerEmail = "-";
			$sCustomerPhone = "";
		}

		$wpdb->insert("{$wpdb->prefix}simple_ecom_orders", [
			"payment_method" => $sPaymentMethod,
			"grand_total" => $aOrderDetails['iGrandTotal'],
			"name" => $sCustomerName,
			"email" => $sCustomerEmail,
			"phone_number" => $sCustomerPhone
		]);

		$aContacts[$sCustomerEmail] = $sCustomerName;

		$iOrderId = $wpdb->insert_id;
		foreach($aOrderDetails['aItems'] as $aItem){
			$aItem['order_id'] = $iOrderId;
			unset($aItem['package_name'], $aItem['product_name']);
			if(!empty($aItem['email']) && !empty($aItem['name'])) {
				$aContacts[$aItem['email']] = $aItem['name'];
			}
			unset($aItem['package_type']);
			if(!empty($aItem['logo_file'])){
				$attachmentId = save_base64_image($aItem["logo_file"]);
				if (!is_wp_error($attachmentId)) {
					$aItem["logo_file"] = $attachmentId;
				}
			}
			$wpdb->insert("{$wpdb->prefix}simple_ecom_order_items", $aItem);
		}
		$aOrderDetails['contacts'] = $aContacts;
		$aOrderDetails['iOrderId'] = $iOrderId;
		$aOrderDetails['sPaymentMethod'] = $sPaymentMethod;
		return $aOrderDetails;
	} catch(\Exception $e){
		error_log($e);
	}
}

function sendOrderGenerateNotificationToCustomer($aOrderDetails){
	if(isset($aOrderDetails['contacts']) && !empty($aOrderDetails['contacts'])){
		foreach($aOrderDetails['contacts'] as $sEmail => $sName){
			$to = $sEmail;
			$subject = 'New Order Generated';
			$message = getOrderGenerationEmailTemplate($aOrderDetails);
			$headers[] = 'From: Sales <sales@ule.ae>';
			wp_mail($to, $subject, $message, $headers);
		}
		$to = "orders@ule.ae"; // Changed email address
		$subject = 'New Order Generated';
		$message = getOrderGenerationEmailTemplate($aOrderDetails, true);
		$headers[] = 'From: Sales <sales@ule.ae>';
		$headers[] = 'CC: Orders <orders@ule.ae>';
		wp_mail($to, $subject, $message, $headers); // Use wp_mail to send the email
	}
}


function getOrderGenerationEmailTemplate($aOrderDetails, $bIsAdmin = false){
	$aEmailPlaceHolder = [];
	$aEmailPlaceHolder['%%ORDER-ID%%'] = $aOrderDetails['iOrderId'];
	$aEmailPlaceHolder['%%NAME%%'] = !empty((array_values($aOrderDetails['contacts']))[0]) ? (array_values($aOrderDetails['contacts']))[0] : "Customer";
	$aEmailPlaceHolder['%%ORDER-DATE%%'] = date('F j, Y');
	$aEmailPlaceHolder['%%ORDER-ID%%'] = $aOrderDetails['iOrderId'];
	$aEmailPlaceHolder['%%ORDER-QTY%%'] = count($aOrderDetails['aItems']);
	$aEmailPlaceHolder['%%ORDER-PAYMENTMETHOD%%'] = $aOrderDetails['sPaymentMethod'];
	$aEmailPlaceHolder['%%ORDER-AMT%%'] = "$".$aOrderDetails['iGrandTotal'];
	$sNotes = '';
	$sOrderDetails = '';
	foreach($aOrderDetails['aItems'] as $aItem){
		if(isset($aItem['additional_notes'])){
			$sNotes .= "<br/>".$aItem['additional_notes'];
		}
		$sOrderDetails .= getOrderDetailHtml($aItem);
	}
	$sNotes = trim($sNotes, '<br/>');
	$sOrderDetails = trim($sOrderDetails, '<br/>');
	$aEmailPlaceHolder['%%ORDER-NOTE%%'] = $sNotes;
	$aEmailPlaceHolder['%%ORDER-DETAILS%%'] = $sOrderDetails;
	$file = get_template_directory()."/wp-html-mail/template.html";
	if($bIsAdmin){
		$file = get_template_directory()."/wp-html-mail/order-receipt.html";
	}
	if(file_exists($file)){
		$sEmailTemplate = file_get_contents($file);
		$sEmailTemplate = str_replace(array_keys($aEmailPlaceHolder), array_values($aEmailPlaceHolder), $sEmailTemplate);
	}
	return $sEmailTemplate;
}

function getCartDetails(){
	
	global $wpdb;

	$aCartDetails = get_session_value('trademark_data');
	if($aCartDetails){

		$oEcomModel = new EcomModel();
		$aPackages = array_filter(array_column($aCartDetails['products'], 'package_id'));

		
		$aAddons = array_filter(array_column($aCartDetails['products'], 'addon'));
		if(!empty($aAddons)){
			$aAddons = array_merge(...$aAddons);
			$aAddonDetails = array_column($oEcomModel->getAddonDetails($aAddons), null, 'addon_id');
		}
		$aProductPricings = $oEcomModel->getPackageDetails($aPackages);
		$aPricingDetails = array_column($aProductPricings, 'price', 'id');
		$aPackageTypeDetails = array_column($aProductPricings, 'package_type', 'id');
		$aAdditionalClassPrice = array_column($aProductPricings, 'class_price', 'id');
		
		if(!empty($aCartDetails['additional_services'])){
			$aAdditionalServiceDetails = $oEcomModel->getPackageDetailsByProductId($aCartDetails['additional_services']);
			$aAdditionalServicePricing = array_column($aAdditionalServiceDetails, 'price', 'product_id');
			$aAdditionalServicePricingId = array_column($aAdditionalServiceDetails, 'id', 'product_id');
			$aAdditionalServiceName = array_column($aAdditionalServiceDetails, 'name', 'product_id');
		}

	    $iGrandTotal = 0;
	    $aItem = [];
	    foreach($aCartDetails['products'] as $aPackage){
			if(isset($aPackage['addon'])){
				$iAddonCount = (count($aPackage['addon'])-1) >= 0 ? count($aPackage['addon'])-1 : 0;
			} else {
				$iAddonCount = 0;
			}
            $iPackagePrice = $aPricingDetails[$aPackage['package_id']];
            $iAdditionalClassPrice = isset($aAdditionalClassPrice[$aPackage['package_id']]) ? $aAdditionalClassPrice[$aPackage['package_id']] : 0;
            $iTotalPrice = ($iAddonCount*$iAdditionalClassPrice)+$iPackagePrice;
            $aItemDetails = [
            	'pricing_id' => $aPackage['package_id'],
            	'type' => 'trademark',
            	'addon_ids' => implode(',', isset($aPackage['addon']) ? $aPackage['addon'] : []),
            	'name' => $aPackage['name'],
            	'email' => $aPackage['email'],
            	'phonenumber' => $aPackage['phone'],
            	'trademark_type' => $aPackage['trademark_type'],
            	'package_name' => $aPackage['package_name'],
            	'package_type' => $aPackageTypeDetails[$aPackage['package_id']] ?? "trademark",
            	'product_name' => $aPackage['product_name'],
            	'trademark_text' => $aPackage['trademark_text'],
            	'logo_file' => $aPackage['trademark_logo'],
            	'trademark_date' => $aPackage['trademark_date'],
            	'additional_notes' => isset($aPackage['notes']) ? $aPackage['notes'] : '',
            	'total' => $iTotalPrice
            ];
            $aItem[] = $aItemDetails;
            $iGrandTotal += $iTotalPrice;
	    }
	    if(isset($aCartDetails['additional_services']) && !empty($aCartDetails['additional_services'])){
		    foreach($aCartDetails['additional_services'] as $iAdditionalServiceId){
		    	$iServicePrice = $aAdditionalServicePricing[$iAdditionalServiceId];
		    	$aItemDetails = [
		    		'pricing_id' => $aAdditionalServicePricingId[$iAdditionalServiceId],
		    		'name' => $aAdditionalServiceName[$iAdditionalServiceId],
		    		'type' => 'additional_service',
		    		'total' => $iServicePrice,
		    	];
		    	$aItem[] = $aItemDetails;
		    	$iGrandTotal += $iServicePrice;
		    }
		}
	    return [
	    	'aItems' => $aItem,
	    	'iGrandTotal' => $iGrandTotal,
			'aCustomerDetails' => isset($aCartDetails['customer']) ? $aCartDetails['customer'] : []
	    ];
	}
}

function generatePaypalOrder($aOrderDetails){
	$aCustomerDetails = current($aOrderDetails['aItems']);
	$aResult['first_name']          = $aCustomerDetails['name'];
    $aResult['night_phone_a']       = 000;
    $aResult['night_phone_b']       = $aCustomerDetails['phonenumber'];
    $aResult['business']            = "shahzaibaminmalik@gmail.com";
    $aResult['item_name']           = 'IPProtector - Order ID # '.$aOrderDetails['iOrderId'];
    $aResult['amount']              = $aOrderDetails['iGrandTotal'];
    $aResult['no_note']             = 1;
    $aResult['no_shipping']         = '1';
    $aResult['address_override']    = '0';
    $aResult['charset']             = 'utf-8';
    $aResult['currency_code']       = 'USD';
    $aResult['bn']                  = 'UNITEDLEGALEXPERTSLIMITED';
    $aResult['custom']              = $aOrderDetails['iOrderId'];
    $aResult['method']              = 'post';
    $aResult['invoice']             = $aOrderDetails['iOrderId'];
    $aResult['return']              = get_site_url()."/thankyou?id=".$aOrderDetails['iOrderId']."&paymentsuccess=true&orderid=".$aOrderDetails['iOrderId'];
    $aResult['cancel_return']       = get_site_url()."/checkout";
    $aResult['notify_url']          = get_site_url()."/wp-json/ipn/paypal";
    $aResult['rm']                  = '2';
    $aResult['cmd']                 = "_xclick";
    $aResult['tax']                 = "0.00";
    $aResult['action']              = "https://www.paypal.com/cgi-bin/webscr";
    $aParams['intent-type'] = "POST";
    $aParams['data'] = $aResult;
    $aParams['url'] = 'https://www.paypal.com/cgi-bin/webscr';
    return $aParams;
}

function generateStripeOrder($aOrderDetails){
    try {
	// $stripe = new \Stripe\StripeClient(STRIPE_SANDBOX_KEY);
	$stripe = new \Stripe\StripeClient(STRIPE_PRODUCTION_KEY);
	$aLineItems = [];
	foreach($aOrderDetails['aItems'] as $aItem){
		$aItemDetails["name"] = isset($aItem["package_name"], $aItem["product_name"]) ? ucwords($aItem["package_type"])." - ".$aItem["package_name"]." - ".$aItem["product_name"] : "Additional Service: ".$aItem["name"];
		$aItemDetails["metadata"] = [
			"addon_ids" => isset($aItem["addon_ids"]) ? $aItem["addon_ids"] : ""
		];
		$aPriceDetails["unit_amount"] = $aItem["total"]*100;
		$aPriceDetails["currency"] = "USD";
		$aLineItem["price_data"] = $aPriceDetails;
		$aLineItem["price_data"]["product_data"] = $aItemDetails;
		$aLineItem["quantity"] = 1;
		$aLineItems[] = $aLineItem;
	}
	$checkout_session = $stripe->checkout->sessions->create([
		'success_url' => get_site_url()."/thankyou?id=".$aOrderDetails['iOrderId']."&paymentsuccess=true&orderid=".$aOrderDetails['iOrderId'],
		'line_items' => $aLineItems,
		'cancel_url' => get_site_url()."/checkout",
		'mode' => 'payment',
		'metadata' => [
			"order_id" => $aOrderDetails['iOrderId'],
			"customer_name" => $aCustomerDetails['name']
		]
	]);
    } catch( \Exception $e ) {
    	print_r("Error Occurred: ".$e->getMessage()." in File: ".$e->getFile()." at Line: ".$e->getLine());exit;
    }
	$aParams['intent-type'] = "redirect";
	$aParams['data'] = [];
	$aParams['url'] = $checkout_session->url;
    return $aParams;
}

function generateWorldPayOrder($aOrderDetails){
	$aResult['testMode'] = "0";
	$aResult['instId'] = 1408392;
	$aResult['cartId'] = $aOrderDetails['iOrderId'];
	$aResult['amount'] = number_format($aOrderDetails["iGrandTotal"], 2, '.', '');
	$aResult['currency'] = "USD";
	$aParams['intent-type'] = "POST";
	$aParams['data'] = $aResult;
	$aParams['url'] = "https://secure.worldpay.com/wcc/purchase";
	return $aParams;
}

function products_page(){
    echo "<h1>".esc_html_e( 'Welcome to my custom admin group page.', 'my-plugin-textdomain' )."</h1>";
}

function addons_page(){
    echo "<h1>".esc_html_e( 'Welcome to my custom admin group page.', 'my-plugin-textdomain' )."</h1>";
}

function control_panel_page(){
    echo "<h1>".esc_html_e( 'Welcome to my custom admin page.', 'my-plugin-textdomain' )."</h1>";
}

function paypal_ipn()
{
	global $wpdb;

	$ipn = new PaypalIPN();

	// Use the sandbox endpoint during testing.
	$ipn->useSandbox();
	$ipn->usePHPCerts();
	$verified = $ipn->verifyIPN();
	if ($verified) {
	    if(!empty($_POST['custom'])){
	    	$wpdb->update('{$wpdb->prefix}simple_ecom_orders',['status' => 1, 'paymend_date' => date('Y-m-d H:i:s'), 'txn_id' => $_POST['txn_id']],['id' => $_POST['custom']]);
		    $aOrderDetails = getOrderDetailsFromDB($_POST['custom']);
			sendOrderProcessedEmail($aOrderDetails);
			sendOrderProcessedEmail($aOrderDetails, true);
	    }
	}

	// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
	header("HTTP/1.1 200 OK");
}


function stripe_ipn()
{
	global $wpdb;

	$ipn = new StripeIPN();
	// Use the sandbox endpoint during testing.
	$ipn->useSandbox(false);
	$ipn->usePHPCerts();
	$data = $ipn->verifyIPN();
	if ($data) {
	    if(!empty($data->metadata->order_id)){
	    	$wpdb->update('{$wpdb->prefix}simple_ecom_orders',['status' => 1, 'paymend_date' => date('Y-m-d H:i:s'), 'txn_id' => $data->id],['id' => $data->metadata->order_id]);
		    $aOrderDetails = getOrderDetailsFromDB($data->metadata->order_id);
			sendOrderProcessedEmail($aOrderDetails);
			sendOrderProcessedEmail($aOrderDetails, true);
	    }
	}

	// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
	header("HTTP/1.1 200 OK");
}

function worldpay_ipn()
{
	global $wpdb;


	$iInstId = isset($_REQUEST['instId']) ? $_REQUEST['instId'] : false ;
	$iCartId = isset($_REQUEST['cartId']) ? $_REQUEST['cartId'] : false ;
	$sTransStatus = isset($_REQUEST['transStatus']) ? $_REQUEST['transStatus'] == "Y" : false ;
	$iTransId = isset($_REQUEST['transId']) ? $_REQUEST['transId'] : false ;
	$sValidatePassword = isset($_REQUEST['callbackPW']) ? $_REQUEST['callbackPW'] == WPAY_CB_PWD : false ;
	error_log($iInstId);
	error_log($iCartId);
	error_log($sTransStatus);
	error_log($iTransId);
	error_log($sValidatePassword);
	error_log($_REQUEST['callbackPW']);
	error_log(WPAY_CB_PWD);
	if ($iInstId && $iCartId && $sTransStatus && $iTransId && $sValidatePassword) {
    	$wpdb->update('{$wpdb->prefix}simple_ecom_orders',['status' => 1, 'paymend_date' => date('Y-m-d H:i:s', strtotime($_REQUEST['transTime'])), 'txn_id' => $iTransId],['id' => $iCartId]);
		$iOrderId = $iCartId;
		$aOrderDetails = getOrderDetailsFromDB($iOrderId);
		error_log(print_r($aOrderDetails, true));
		sendOrderProcessedEmail($aOrderDetails);
		sendOrderProcessedEmail($aOrderDetails, true);
	}

	// Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
	header("HTTP/1.1 200 OK");
}

function getOrderDetailsFromDB($iOrderId){
	global $wpdb;
	$sQuery = "SELECT o.payment_method, o.grand_total, oi.*, p.name AS product_name, pg.name AS package_name  FROM {$wpdb->prefix}simple_ecom_orders o 
	INNER JOIN {$wpdb->prefix}simple_ecom_order_items oi on oi.order_id = o.id 
	INNER JOIN {$wpdb->prefix}simple_ecom_product_pricing pr on pr.id = oi.pricing_id
	INNER JOIN {$wpdb->prefix}simple_ecom_products p on p.id = pr.product_id
	LEFT JOIN {$wpdb->prefix}simple_ecom_packages pg on pg.id = pr.package_id
	where o.id = {$iOrderId}";
	$aOrderDetails = $wpdb->get_results($sQuery, ARRAY_A);
	return $aOrderDetails;
}
function sendOrderProcessedEmail($aOrderDetails, $bIsAdmin = false){
	$aEmailPlaceHolder = [];
	
	$sOrderDetails = "";
	$aEmailPlaceHolder['%%ORDER-QTY%%'] = 0;
	$sNotes = "";
	$aEmails = [];
	foreach($aOrderDetails as $key => $aItems){
		if(in_array($aItems['type'], ['trademark', 'design'])){
			$aEmailPlaceHolder['%%ORDER-ID%%'] = $aItems['order_id'];
			$aEmailPlaceHolder['%%NAME%%'] = !empty($aItems['name']) ? $aItems['name'] : "Customer";
			$aEmailPlaceHolder['%%ORDER-DATE%%'] = date('F j, Y');
			$aEmailPlaceHolder['%%ORDER-ID%%'] = $aItems['order_id'];
			$aEmailPlaceHolder['%%ORDER-PAYMENTMETHOD%%'] = $aItems['payment_method'];
			$aEmailPlaceHolder['%%ORDER-AMT%%'] = "$".$aItems['grand_total'];
			$aEmails[] = $aItems['email'];
		}
		$aEmailPlaceHolder['%%ORDER-QTY%%']++;
		if(isset($aItems['additional_notes'])){
			$sNotes .= "<br/>".$aItems['additional_notes'];
		}
		$sOrderDetails .= getOrderDetailHtml($aItems);
}
$sNotes = trim($sNotes, '<br/>');
$sOrderDetails = trim($sOrderDetails, '<br/>');
$aEmailPlaceHolder['%%ORDER-NOTE%%'] = $sNotes;
$aEmailPlaceHolder['%%ORDER-DETAILS%%'] = $sOrderDetails;
$file = get_template_directory()."/wp-html-mail/template.html";
if($bIsAdmin){
	$aEmails = ["sales@ule.ae", "orders@ule.ae"];  // Changed email address
	$file = get_template_directory()."/wp-html-mail/order-receipt.html";
}
	if(file_exists($file)){
		$sEmailTemplate = file_get_contents($file);
		$sEmailTemplate = str_replace(array_keys($aEmailPlaceHolder), array_values($aEmailPlaceHolder), $sEmailTemplate);
	}
	foreach($aEmails as $sEmail){
		$to = $sEmail;
		$subject = 'New Order Generated';
		$message = $sEmailTemplate;
		$headers[] = 'From: Sales <sales@ule.ae>';
		wp_mail($to, $subject, $message, $headers);
	}
}

function getOrderDetailHtml($aItem){
	$aTypes = [
		'trademark' => "Trademark",
		'design' => "Design",
		'additional_service' => "Additional Service"
	];
	$sOrderDetails = $aTypes[$aItem['type']]."<br/><ul>";
	if($aItem['type'] != "additional_service"){
		$sOrderDetails .= "<li><strong>Trademark Type</strong>: ".$aItem['trademark_type'];
		$sOrderDetails .= "<li><strong>Trademark Text</strong>: ".$aItem['trademark_text'];
		$sOrderDetails .= "<li><strong>Name</strong>: ".$aItem['name'];
		$sOrderDetails .= "<li><strong>Email</strong>: ".$aItem['email'];
		$sOrderDetails .= "<li><strong>Phone</strong>: ".$aItem['phonenumber'];
		$sOrderDetails .= "<li><strong>Notes</strong>: ".$aItem['additional_notes'];
		$sOrderDetails .= "<li><strong>Package Name</strong>: ".$aItem['product_name']." - ".$aItem['package_name'];
	} else {
		$sOrderDetails .= "<li><strong>Additional Service Name</strong>: ".$aItem['name'];
	}
	$sOrderDetails .= "</ul>";
	return $sOrderDetails;
}

