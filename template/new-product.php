<div class="wrap container-fluid" style="margin-bottom:10px;">
    <h2><?php echo !empty($context['action']) && $context['action'] == "edit_product" ? "Edit Product" : "Add new Product"; ?></h2>
</div>
<?php
    $aProductDetails = isset($context['pricingDetails']) && !empty($context['pricingDetails']) ? current($context['pricingDetails']) : [];
    $aAction = isset($context['action']) && !empty($context['action']) ? $context['action'] : 'add_new_product';
?>
<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
    <input name="action" value="<?php echo $aAction; ?>" type="hidden" />
    <input name="product_id" value="<?php echo isset($_REQUEST['product']) ? $_REQUEST['product'] : ''; ?>" type="hidden" />
    <div class="container-fluid">
        <div class="row">
            <div class="col col-md-12">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="prod_name">Product Name</label>
                        <input class="form-control" required="true" type="text" name="prod_name" value="<?php echo isset($aProductDetails['name']) ? $aProductDetails['name'] : ""; ?>" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="prod_slug">Product SKU</label>
                        <input class="form-control" required="true" type="text" name="prod_slug" value="<?php echo isset($aProductDetails['slug']) ? $aProductDetails['slug'] : ""; ?>" /> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="prod_group">Product Group</label>
                        <select name="prod_group" class="form-control" required="true" >
                            <option value="">Please select a product group</option>
                            <?php foreach($context['groups'] as $group){ ?>
                                <option <?php echo isset($aProductDetails['group_id']) && $aProductDetails['group_id'] == $group['id'] ? "selected" : ""; ?> value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="prod_group">Product Type</label>
                        <select name="prod_type" class="form-control" required="true" >
                            <option value="trademark" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "trademark" ? "selected" : ""; ?>>Trademark</option>
                            <option value="design" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "design" ? "selected" : ""; ?>>Design</option>
							<option value="patent-line-art-creation" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "patent-line-art-creation" ? "selected" : ""; ?>>patent-line-art-creation</option>
							
							<option value="patent" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "patent" ? "selected" : ""; ?>>Patent</option>
										<option value="patentsearch" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "patentsearch" ? "selected" : ""; ?>>patentsearch</option>
														<option value="internationalutility" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "internationalutility" ? "selected" : ""; ?>>internationalutility</option>
							
							<option value="provisional-patent" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "provisional-patent" ? "selected" : ""; ?>>provisional-patent</option>
							
							<option value="utility-patents" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "utility-patents" ? "selected" : ""; ?>>utility-patents</option>
							
                            <option value="additional-service" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "additional-service" ? "selected" : ""; ?>>Additional-Service</option>
							
								<option value="trademarklogo" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "trademarklogo" ? "selected" : ""; ?>>trademarklogo</option>
							
							<option value="trademarksearch" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "trademarksearch" ? "selected" : ""; ?>>trademarksearch</option>
							
							<option value="trademarkwatch" <?php echo isset($aProductDetails['type']) && $aProductDetails['type'] == "trademarkwatch" ? "selected" : ""; ?>>trademarkwatch</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="prod_img">Primary Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="prod_img" id="prod_img" aria-describedby="prod_img">
                            <label class="custom-file-label" for="prod_img">Choose File</label>
                        </div>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="prod_img">Secondary Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="secondary_image" id="secondary_image" aria-describedby="secondary_image">
                            <label class="custom-file-label" for="prod_img">Choose File</label>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="prod_desc">Product Page</label>
                        <?php wp_dropdown_pages(['show_option_none' => 'Select a Product Page','name' => 'prod_desc', 'post_type' => 'product_page', 'class' => 'form-control', 'selected' => isset($aProductDetails['description']) ? $aProductDetails['description'] : 0]); ?>
                    </div>
                </div>
            </div>
            <div class="col col-md-12">
                <nav>
                  <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <?php foreach($context['packages'] as $package){ ?>
                        <?php if($package['type'] == $aProductDetails['type']): ?>
                        <a class="nav-item nav-link <?php echo current($context['packages'])['id'] == $package['id'] ? "active" : ""; ?>" id="<?php echo $package['slug']; ?>-tab" data-toggle="tab" href="#<?php echo $package['slug']; ?>" role="tab" aria-controls="<?php echo $package['slug']; ?>" aria-selected="<?php echo current($context['packages'])['id'] == $package['id'] ? "true" : ""; ?>"><?php echo $package['name']; ?></a>
                        <?php endif; ?>
                    <?php } ?>
                  </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <?php foreach($context['packages'] as $package){ ?>
                        <?php if($package['type'] == $aProductDetails['type']): ?>
                        <?php
                            $isEnabled = !empty($context['pricingDetails'][$package['id']]);
                            $iPrice = !empty($context['pricingDetails'][$package['id']]['price']) ? $context['pricingDetails'][$package['id']]['price'] : "";
                            $sPackageDetails = !empty($context['pricingDetails'][$package['id']]['package_details']) ? $context['pricingDetails'][$package['id']]['package_details'] : "";
                            $iAdditionalClassPrice = !empty($context['pricingDetails'][$package['id']]['class_price']) ? $context['pricingDetails'][$package['id']]['class_price'] : "";
                        ?>
                        <div class="tab-pane fade <?php echo current($context['packages'])['id'] == $package['id'] ? "show active" : ""; ?>" id="<?php echo $package['slug']; ?>" role="tabpanel" aria-labelledby="<?php echo $package['slug']; ?>-tab">
                            <div class="container-fluid row" style="margin-top: 10px;">
                                <div class="col col-md-4">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="addPricing[<?php echo $package['id']; ?>]">
                                                <input <?php echo $isEnabled ? "checked" : ""; ?> type="checkbox" class="package-enable" data-package="<?php echo $package['slug']; ?>" name="addPricing[<?php echo $package['id']; ?>]" />
                                                Enable Package
                                            </label>
                                        </div>
                                    </div>
                                    <fieldset <?php echo !$isEnabled ? "disabled" : ""; ?> class="<?php echo $package['slug']; ?>-fieldset">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="pricing[<?php echo $package['id']; ?>]">Package Price </label>
                                                <input value="<?php echo $iPrice; ?>" class="form-control" type="text" name="pricing[<?php echo $package['id']; ?>]" />
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="additionalClass[<?php echo $package['id']; ?>]">Each Additional Class Price </label>
                                                <input value="<?php echo $iAdditionalClassPrice; ?>" class="form-control" type="text" name="additionalClass[<?php echo $package['id']; ?>]" />
                                            </div>    
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col col-md-8">
                                    <fieldset <?php echo !$isEnabled ? "disabled" : ""; ?> class="<?php echo $package['slug']; ?>-fieldset-package-details">
                                        <div class="form-row">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Package Description</span>
                                                </div>
                                                <textarea class="form-control" name="packagedetails[<?php echo $package['id']; ?>]" rows="8" aria-label="Package Description"><?php echo $sPackageDetails; ?></textarea>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php } ?>
                </div>
            </div>
            <div class="col col-md-12">
                <?php foreach($context['addons'] as $sGroup => $aAddons){ ?>
                    <div class="col-md-4" style="float:left;">
                        <h5><label><input type="checkbox" class="select-all-addons" data-group=<?php echo $sGroup; ?>><?php echo $sGroup; ?></label></h5>
                        <div>
                            <?php foreach($aAddons as $aAddon){ ?>
                            <?php $isChecked = isset($context['selectedAddon']) && in_array($aAddon['id'], $context['selectedAddon']); ?>
                            <label for="addon[<?php echo $aAddon['id']; ?>]">
                                <input <?php echo $isChecked ? "checked" : ""; ?> class="addon-<?php echo $sGroup; ?>" type="checkbox" name="addon[<?php echo $aAddon['id']; ?>]" value="<?php echo $aAddon['id']; ?>"> <?php echo $aAddon['name']; ?>
                            </label>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col col-md-12">
                <input type="submit" class="btn btn-primary" name="submit" value="Save">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".package-enable").change(function(){
            var package = jQuery(this).data('package'); 
            console.log(jQuery(this).is(":checked"));
            if(jQuery(this).is(":checked")){
                jQuery("."+package+"-fieldset").removeAttr('disabled');
                jQuery("."+package+"-fieldset input").attr('required','true');
            } else {
                jQuery("."+package+"-fieldset").attr('disabled', 'disabled');
                jQuery("."+package+"-fieldset").removeAttr('required');
            }
        });
        jQuery(".select-all-addons").change(function(){
            var group = jQuery(this).data('group');
            if(jQuery(this).is(":checked")){
                jQuery('.addon-'+group).attr('checked', 'checked');
            } else {
                jQuery('.addon-'+group).removeAttr('checked');
            }
        });
    });
</script>