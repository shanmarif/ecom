<?php 
class EcomModel {
	
	public $wpdb;

	function __construct()
	{
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function getAddonDetails($aAddons)
	{
		$aResult = [];
		if(!empty($aAddons)){
			$sAddons = implode(',',$aAddons);
			$sSql = "SELECT
				ea.id AS addon_id,
				ea.name AS addon_name,
				ea.classification AS addon_class,
				ea.description AS addon_desc,
				eg.name AS group_name,
				eg.slug AS group_slug
			FROM {$this->wpdb->prefix}simple_ecom_addons ea
			INNER JOIN {$this->wpdb->prefix}simple_ecom_groups eg ON eg.id = ea.group_id
			WHERE ea.id IN ({$sAddons})";
			$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		}
		return $aResult;
	}

	public function getPackageDetails($aPackages)
	{
		$aResult = [];
		if(!empty($aPackages)){
			$sPackages = implode(',',$aPackages);
			$sSql = "SELECT
				ep.id,
				ep.price,
				p.type as `package_type`,
				ep.product_id,
				ep.additional_class_price as class_price,
				p.image
			FROM {$this->wpdb->prefix}simple_ecom_product_pricing ep
			INNER JOIN {$this->wpdb->prefix}simple_ecom_products p on p.id = ep.product_id
			WHERE ep.id IN ({$sPackages})";
			$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		}
		return $aResult;
	}

	public function getProductPricingByType($sType)
	{
		$sSql = "SELECT
			ep.id,
			ep.name,
			ep.slug,
			epp.price
		FROM {$this->wpdb->prefix}simple_ecom_products ep
		INNER JOIN {$this->wpdb->prefix}simple_ecom_product_pricing epp on epp.product_id = ep.id
		WHERE ep.type = '{$sType}'";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function getPackageDetailsByProductId($aProducts)
	{
		$sProducts = implode(',',$aProducts);
		$sSql = "SELECT
			ep.id,
			ep.price,
			ep.product_id,
			ep.package_id,
			ep.additional_class_price as class_price,
			ep.package_details,
			p.name,
			p.slug,
			p.group_id,
			p.description,
			p.type,
			p.image
		FROM {$this->wpdb->prefix}simple_ecom_products p
		LEFT JOIN {$this->wpdb->prefix}simple_ecom_product_pricing ep on p.id = ep.product_id
		WHERE p.id IN ({$sProducts})";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');

		return $aResult;
	}
	
	public function getProductTypeByPricingId($iPriceID)
	{
		$sSql = "SELECT ep.type as prod_type
		FROM {$this->wpdb->prefix}simple_ecom_products ep
		INNER JOIN {$this->wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
		WHERE epp.id = {$iPriceID}";
		$aResult = $this->wpdb->get_row($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function getProductDetailsByPricingId($iPriceID)
	{
		$sSql = "SELECT 
			ep.id AS prod_id, 
			ep.name AS prod_name, 
			ep.slug AS prod_slug,
			ep.type AS prod_type,
			epc.name,
			epc.slug,
			epp.id as package_id,
			epp.price,
			epp.additional_class_price as class_price,
			epp.package_details,
			ea.id AS addon_id,
			ea.name AS addon_name,
			ea.description AS addon_desc,
			eg.name AS group_name,
			eg.slug AS group_slug
		FROM {$this->wpdb->prefix}simple_ecom_products ep
		INNER JOIN {$this->wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
		INNER JOIN {$this->wpdb->prefix}simple_ecom_packages epc ON epc.id = epp.package_id and epc.type = ep.type
		LEFT JOIN {$this->wpdb->prefix}simple_ecom_product_addon_relation epar ON epar.product_id = ep.id
		LEFT JOIN {$this->wpdb->prefix}simple_ecom_addons ea ON ea.id = epar.addon_id
		LEFT JOIN {$this->wpdb->prefix}simple_ecom_groups eg ON eg.id = ea.group_id
		WHERE epp.id = {$iPriceID}"; // ORDER BY ea.classification ASC";

		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function getAllGroups(){
		$sSql = "SELECT * FROM {$this->wpdb->prefix}simple_ecom_groups where parent_id >= 0";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function getGroup($iId){
		$aResult = [];
		if(!empty($iId)){
			$sSql = "SELECT * FROM {$this->wpdb->prefix}simple_ecom_groups where id = {$iId}";
			$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		}
		return $aResult;
	}

	public function getPackage($iId){
		$aResult = [];
		if(!empty($iId)){
			$sSql = "SELECT * FROM {$this->wpdb->prefix}simple_ecom_packages where id = {$iId}";
			$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		}
		return $aResult;
	}

	public function getAllPackages(){
		$sSql = "SELECT * FROM {$this->wpdb->prefix}simple_ecom_packages";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function getAllAddons(){
		$sSql = "SELECT a.*, g.name as group_name FROM {$this->wpdb->prefix}simple_ecom_addons a
		INNER JOIN {$this->wpdb->prefix}simple_ecom_groups g ON g.id = a.group_id";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		$aAddonDetail = [];
		foreach($aResult as $aAddon){
			$aAddonDetail[$aAddon['group_name']][] = $aAddon;
		}
		return $aAddonDetail;	
	}

	public function insertProduct($aProductDetails)
	{
		$aProductData = [
			'name' => $aProductDetails['prod_name'],
			'slug' => $aProductDetails['prod_slug'],
			'type' => $aProductDetails['prod_type'],
			'group_id' => $aProductDetails['prod_group'],
			'image' => $aProductDetails['image'],
			'secondary_image' => $aProductDetails['secondary_image'],
			'description' => $aProductDetails['prod_desc'],
		];
		$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_products", $aProductData);
		$iProductId = $this->wpdb->insert_id;

		foreach($aProductDetails['addPricing'] as $iPackageId => $bStatus){
			$aProductPricing = [
				'product_id' => $iProductId,
				'package_id' => $iPackageId,
				'price'	=> $aProductDetails['pricing'][$iPackageId],
				'additional_class_price'	=> $aProductDetails['additionalClass'][$iPackageId],
				'package_details' => $aProductDetails['packagedetails'][$iPackageId]
			];
			$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_product_pricing", $aProductPricing);
		}

		foreach($aProductDetails['addon'] as $iAddonId){
			$aProductAddonRelation = [
				'product_id' => $iProductId,
				'addon_id' => $iAddonId
			];
			$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_product_addon_relation", $aProductAddonRelation);
		}
	}

	public function insertGroup($aData){
		$aGroupData = [
			'name' => $aData['name'],
			'slug' => $aData['slug'],
			'description' => $aData['description'],
			'parent_id' => $aData['parent_id'],
			'group_level' => $aData['group_level'],
			'image' => $aData['image'],
		];
		$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_groups", $aGroupData);
	}

	public function updateGroup($aData){
		$aGroupData = [
			'name' => $aData['name'],
			'slug' => $aData['slug'],
			'description' => $aData['description'],
			'parent_id' => $aData['parent_id'],
			'group_level' => $aData['group_level'],
			'image' => $aData['image'],
		];
		$this->wpdb->update("{$this->wpdb->prefix}simple_ecom_groups", $aGroupData, ['id' => $aData['group_id']]);
	}

	public function updatePackage($aData){
		$aPackageData = [
			'name' => $aData['name'],
			'slug' => $aData['slug'],
			'type' => $aData['type'],
			'description' => $aData['description']
		];
		$this->wpdb->update("{$this->wpdb->prefix}simple_ecom_packages", $aPackageData, ['id' => $aData['package_id']]);
	}

	public function insertPackage($aData){
		$aPackageData = [
			'name' => $aData['name'],
			'slug' => $aData['slug'],
			'type' => $aData['type'],
			'description' => $aData['description']
		];
		$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_packages", $aPackageData);
	}

	public function getProductSelectedAddons($id)
	{
		$sSql = "SELECT addon_id FROM {$this->wpdb->prefix}simple_ecom_product_addon_relation WHERE product_id = {$id}";
		$aResult = $this->wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}

	public function updateProduct($aProductDetails)
	{
		$aProductData = [
			'name' => $aProductDetails['prod_name'],
			'slug' => $aProductDetails['prod_slug'],
			'type' => $aProductDetails['prod_type'],
			'group_id' => $aProductDetails['prod_group'],
			'description' => $aProductDetails['prod_desc']
		];
		if($aProductDetails['image'] !== "-")
			$aProductData['image'] = $aProductDetails['image'];
		if($aProductDetails['secondary_image'] !== "-")
			$aProductData['secondary_image'] = $aProductDetails['secondary_image'];

		$this->wpdb->update("{$this->wpdb->prefix}simple_ecom_products", $aProductData, ["id" => $aProductDetails['product_id']]);
		$iProductId = $aProductDetails['product_id'];

		$this->wpdb->delete( "{$this->wpdb->prefix}simple_ecom_product_pricing", [ 'product_id' => $iProductId ], [ '%d' ]);
		$this->wpdb->delete( "{$this->wpdb->prefix}simple_ecom_product_addon_relation", [ 'product_id' => $iProductId ], [ '%d' ]);

		foreach($aProductDetails['addPricing'] as $iPackageId => $bStatus){
			$aProductPricing = [
				'product_id' => $iProductId,
				'package_id' => $iPackageId,
				'price'	=> $aProductDetails['pricing'][$iPackageId],
				'additional_class_price'	=> $aProductDetails['additionalClass'][$iPackageId],
				'package_details' => $aProductDetails['packagedetails'][$iPackageId]
			];
			$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_product_pricing", $aProductPricing);
		}

		foreach($aProductDetails['addon'] as $iAddonId){
			$aProductAddonRelation = [
				'product_id' => $iProductId,
				'addon_id' => $iAddonId
			];
			$this->wpdb->insert("{$this->wpdb->prefix}simple_ecom_product_addon_relation", $aProductAddonRelation);
		}
	}

	public function getAllProductsByGroup($sType, $sGroup = false){
		global $wpdb;

		$sWhereGroup = "";
		if($sGroup){
			$sWhereGroup = "AND eg.slug = '{$sGroup}'";
		}

		$sSql = "SELECT 
			ep.`id` AS prod_id, 
			eg.*, 
			ep.`name` AS product, 
			ep.`type`, 
			ep.`description` AS prod_desc,
			p.post_name AS link,
			ep.`image`, 
			ep.`slug` as prod_slug,
			epp.`price`
		FROM {$wpdb->prefix}simple_ecom_groups eg
		INNER JOIN {$wpdb->prefix}simple_ecom_products ep ON ep.group_id = eg.id
		LEFT JOIN {$wpdb->prefix}simple_ecom_product_pricing epp ON epp.product_id = ep.id
		LEFT JOIN {$wpdb->prefix}posts p on p.id = ep.description and p.post_type = 'product_page'
		WHERE ep.type = '{$sType}' {$sWhereGroup} 
		GROUP BY prod_id
		ORDER BY ep.id, epp.package_id ASC";
		$aResult = $wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
	}
    
    public function getAllOrders($limit = 25, $offset = 0){
		global $wpdb;
        $sSql = "SELECT * FROM {$wpdb->prefix}simple_ecom_orders o
		INNER JOIN {$wpdb->prefix}simple_ecom_order_items oi ON oi.order_id = o.id LIMIT = {$limit} OFFSET = {$offset}";
        $aResult = $wpdb->get_results($sSql, 'ARRAY_A');
		return $aResult;
    }
}