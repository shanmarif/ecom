<?php
    $aProductDetails = current($context['aData']);
    $aProductSpecificDetails = [
        'product_id' => $aProductDetails['prod_id'],
        'package_id' => $aProductDetails['package_id'],
        'product_name' => $aProductDetails['prod_name'],
        'product_slug' => $aProductDetails['prod_slug'],
        'product_type' => $aProductDetails['prod_type'],
        'product_package' => $aProductDetails['name'],
        'product_price' => $aProductDetails['price'],
        'class_price' => $aProductDetails['class_price'],
    ];

    // Check karein ki form trademark form hai ya nahi
    $isTrademarkForm = ($aProductSpecificDetails['product_type'] == 'trademark');

    // Define the new product title
    $newProductTitle = "Patent Line Art Creation";

?>
<section class="bnr form" style="padding: 0;">
    <div class="container">
        <div class="row">
            <div class="flex-box">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="#">Products</a></li>
                        <!-- Updated breadcrumb to only include product type -->
                        <li class="active"><?php echo ucwords($aProductSpecificDetails['product_type']); ?></li>
                    </ol>
                </div>
                <div class="col-sm-12">
                    <!-- Updated product title to "Patent Line Art Creation" -->
                    <h1 class="bnr-hdg"><?php echo $newProductTitle; ?></h1>
                    <p class="bnr-para">Fill in the following form to apply for registration</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="formsec sec" style="padding: 0;">
    <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('add_product_to_cart','security-code-here'); ?>
        <input name="action" value="add_product_to_cart" type="hidden" />
        <input name="package_id" value="<?php echo $aProductDetails['package_id']; ?>" type="hidden" />
        <input name="product_name" value="<?php echo $newProductTitle; ?>" type="hidden" />
        <input name="package_name" value="<?php echo $aProductDetails['name']; ?>" type="hidden" />
        <input name="product_id" value="<?php echo $aProductDetails['prod_id']; ?>" type="hidden" />
        <div class="container">
            <div class="row">
                <div class="flex-box">
                    <div class="col-md-7">
                        <div class="row">
                            <?php if ($isTrademarkForm) { ?>
                            <div class="top-form">
                                <div class="col-sm-12">
                                    <div class="input-container">
                                        <h2 class="hdg">Select classes for your <?php echo ucwords($aProductSpecificDetails['product_type']); ?></h2>
                                        <p class="para">Select the classes in which to register your <?php echo ucwords($aProductSpecificDetails['product_type']); ?>, depending on
                                            which products or services the mark will be used for. Once you have selected the
                                            classes fill in the corresponding boxes with the description of products or
                                            services.</p>
                                        <span class="small"><i>*Note:</i> Sample text about the price if he adds classes.</span>
                                    </div>
                                    <?php foreach($aAddonGroups as $sAddonGroup){ ?>
                                    <?php $currentVal = 1; ?>
                                    <div class="input-container">
                                        <label class="uppercase"><?php echo $sAddonGroup; ?> Classes</label>
                                        <div class="class-box faqs">
                                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                                <?php foreach($context['aData'] as $aAddons){ ?>
                                                <?php if($aAddons['group_slug'] == $sAddonGroup) { ?>
                                                <?php $bIsFirst = $currentVal == 1;$currentVal++; ?>
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="heading<?php echo $aAddons['addon_id']; ?>">
                                                        <h4 class="panel-title">
                                                            <label class="check_input">
                                                                <a <?php echo !$bIsFirst ? 'class="collapsed"' : ""; ?> role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $aAddons['addon_id']; ?>" aria-expanded="<?php echo $bIsFirst ? 'true' : "false"; ?>" aria-controls="collapse<?php echo $aAddons['addon_id']; ?>"><?php echo $aAddons['addon_name']; ?></a>
                                                                <input type="checkbox" class="addon-select" name="addon[]" value="<?php echo $aAddons['addon_id']; ?>" />
																<span class="checkmark"></span>
                                                            </label>
                                                        </h4>
                                                    </div>
                                                    <div id="collapse<?php echo $aAddons['addon_id']; ?>" class="panel-collapse collapse<?php echo $bIsFirst ? " in": ""; ?>" role="tabpanel" aria-labelledby="heading<?php echo $aAddons['addon_id']; ?>">
                                                        <div class="panel-body"><?php echo $aAddons['addon_desc']; ?></div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="faqs">
                                            <div class="panel-group" id="unassure1" role="tablist" aria-multiselectable="true">
                                                <div class="panel panel-default">
                                                    <div class="panel-heading" role="tab" id="surity1">
                                                        <h4 class="panel-title">
                                                            <label class="check_input">
                                                                <a role="button" data-toggle="collapse" data-parent="#unassure1" class="collapsed" href="#collapseSurity1" aria-expanded="true" aria-controls="collapseSurity1">
                                                                    I am unsure
                                                                </a>
                                                                <input type="checkbox" name="unsure-<?php echo $sAddonGroup; ?>" class="unsure">
                                                                <span class="checkmark"></span>
                                                            </label>
                                                        </h4>
                                                    </div>
                                                    <div id="collapseSurity1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="surity1">
                                                        <div class="panel-body">
                                                            Chemical Products; Chemicals used in industry, science and
                                                            photography, as well as in agriculture, horticulture and
                                                            forestry; unprocessed artificial resins; unprocessed plastics;
                                                            manures; fire extinguishing compositions; tempering and
                                                            soldering preparations; chemical substances for preserving
                                                            foodstuffs; tanning substances; adhesives used in industry.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-container" id="unsure-<?php echo $sAddonGroup; ?>-notes" style="display:none;">
                                        <label>Notes</label>
                                        <textarea rows="8" name="unsure-<?php echo $sAddonGroup; ?>-notes" class="form-control" placeholder="Some notes for us..."></textarea>
                                    </div>
                                    <?php } ?>
                                    <div class="input-container">
                                        <span class="small">If you are not sure how to fill in the boxes, leave them blank
                                            and an IP Consultant will get back to you in case the information is required.
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="top-form">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-container">
                                                <label class="required" for="email">Email</label>
                                                <div class="wthicon">
                                                    <input type="text" name="email" placeholder="Enter your email address" class="form-control" id="email">
                                                    <span class="bg-email_icon"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-container">
                                                <label class="required" for="phone">Phone</label>
                                                <div class="wthicon">
                                                    <input type="text" name="phone" placeholder="Enter your phone number" class="form-control" id="phone">
                                                    <span class="bg-phone_icon"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="input-container">
                                                <label for="name">Name</label>
                                                <div class="wthicon">
                                                    <input type="text" name="name" placeholder="Enter your name" class="form-control" id="name">
                                                    <span class="bg-name_icon"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="input-container">
                                                <label>Notes</label>
                                                <textarea rows="8" name="notes" class="form-control" placeholder="Some notes for us..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="top-form">
                                <div class="col-sm-6">
                                    <div class="input-container">
                                        <label>Upload Product Images</label>
                                        <label class="custom-file-upload" style="margin-bottom:0px;">
                                            <input type="file" name="product_images[]" id="product_images" multiple onchange="previewImages(event)">
                                            <span class="tl-file-text">Choose File</span>
                                        </label>
                                        <span class="filename small" style="min-height: 15px;"></span>
                                        <span id="upload-status" class="small" style="color: green;"></span>
                                        <span id="upload-error" class="small" style="color: red;"></span>
                                        <!-- <span class="small">Image Preview.</span> -->
										<div id="image-preview" style="margin-top: 10px;"></div> <!-- Image preview div -->
                                    </div>
                                </div>
                            </div>
                            <div class="mob-center">
                                <div class="mob-left">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="input-container">
                                                    <label for="count">Quantity <span class="small">(Int. Nice Classes)</span></label>
                                                    <span class="number-wrapper">
                                                       <input type="number" disabled="disabled" class="form-control count" id="count" value="1" min="0">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-container">
                                                    <label>Total</label>
                                                    <!-- Updated to include dollar sign with price -->
                                                    <span class="amount">$<?php echo $aProductDetails['price']; ?></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn-blk small" name="submit" value="submit">Add to Cart <span class="arrow"></span></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<?php echo do_shortcode('[relatedproduct]'); ?>
<section class="fxditms">
    <div class="callbtn hidden-sm hidden-xs">
        <a href="#">
            <img src="/wp-content/uploads/2021/09/call_btn.png" alt="img" class="img-resposive">
        </a>
    </div>
    <?php if(!empty($aProductDetails)){ ?>
    <div class="ordrsmry  hidden-sm hidden-xs">
        <h3 class="hdg">Order Summary</h3>
        <table class="table">
            <tbody>
                <tr>
                    <td>Country:</td>
                    <!-- Updated to show "USA" -->
                    <td>USA</td>
                </tr>
                <!-- Removed the "Duration" row -->
                <!--<tr>
                    <td>Duration:</td>
                    <td><?php echo $aProductDetails['duration']; ?></td>
                </tr>-->
                <tr>
                    <td>Type:</td>
                    <!-- Updated to show "Patent" -->
                    <td>Patent</td>
                </tr>
                <tr>
                    <td>Price:</td>
                    <!-- Updated to include dollar sign with price -->
                    <td>$<?php echo $aProductDetails['price']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php } ?>
</section>
