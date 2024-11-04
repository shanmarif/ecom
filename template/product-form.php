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
?>
<script type="text/javascript">
    var cart_state = {};
    cart_state.product = JSON.parse('<?php echo json_encode($aProductSpecificDetails); ?>');
    cart_state.total_price = parseInt(<?php echo $aProductDetails['price']; ?>);
    cart_state.selected_addons = [];
</script>

<section class="bnr form" style="padding:0">
    <div class="container">
        <div class="row">
            <div class="flex-box">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="#">Products</a></li>
                        <li>
                            <a href="#">
                                <?php echo ucwords($aProductSpecificDetails['product_type']); ?>
                            </a>
                        </li>
                        <li class="active">
                            <?php echo ucwords($aProductSpecificDetails['product_type']); ?> Registration in
                            <?php echo $aProductDetails['prod_name']; ?>
                        </li>
                    </ol>
                </div>
                <div class="col-sm-12">
                    <h1 class="bnr-hdg"><?php echo ucwords($aProductSpecificDetails['product_type']); ?> <br>Registration in <?php echo $aProductDetails['prod_name']; ?></h1>
                    <p class="bnr-para">Fill in the following form to apply for registration</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="formsec sec" style="padding:0">
    <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('add_product_to_cart','security-code-here'); ?>
        <input name="action" value="add_product_to_cart" type="hidden">
        <input name="package_id" value="<?php echo $aProductDetails['package_id']; ?>" type="hidden">
        <input name="product_name" value="<?php echo $aProductDetails['prod_name']; ?>" type="hidden">
        <input name="package_name" value="<?php echo $aProductDetails['name']; ?>" type="hidden">
        <input name="product_id" value="<?php echo $aProductDetails['prod_id']; ?>" type="hidden">
        <input name="product_type" value="<?php echo $aProductDetails['prod_type']; ?>" type="hidden">
        <input type="hidden" name="payment_method" value="jazzcash"> <!-- Add this line -->

        <div class="container">
            <div class="row">
                <div class="flex-box">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="top-form">
                                <div class="col-sm-12">
                                    <div class="input-container">
                                        <label class="required" for="type">
                                            <?php echo ucwords($aProductSpecificDetails['product_type']); ?> Type</label>
                                        <select class="form-control" id="type" name="<?php echo ucwords($aProductSpecificDetails['product_type']); ?>_type">
                                            <optgroup label="Wordmark">
                                                <option value="wordmark">A wordmark contains text only.</option>
                                            </optgroup>
                                            <optgroup label="Combined">
                                                <option value="combined">A combination of wordmark and design logo.</option>
                                            </optgroup>
                                            <optgroup label="Stylized">
                                                <option value="stylized">A stylish wordmark.</option>
                                            </optgroup>
                                            <optgroup label="Logo">
                                                <option value="logo">A stylish design represents your brand.</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="input-container">
                                                <label for="txt">
                                                    <?php echo ucwords($aProductSpecificDetails['product_type']); ?> Text</label>
                                                <input type="text" name="<?php echo ucwords($aProductSpecificDetails['product_type']); ?>_text" placeholder="IPL Traders" class="form-control" id="txt">
                                                <span class="small">Type in all the words, letters or numbers included in your <?php echo ucwords($aProductSpecificDetails['product_type']); ?>.</span>
                                            </div>
                                        </div>
                                        <!-- Image Upload Section -->
                                        <div class="col-sm-6" id="img-upload" style="display:none">
                                            <div class="input-container">
                                                <label>Please Upload Logo File</label>
                                                <label class="custom-file-upload" style="margin-bottom:0">
                                                    <input type="file" name="product_images[]" id="product_images" multiple>
                                                    <span class="tl-file-text">Choose File</span>
                                                </label>
                                                <span class="filename small" style="min-height:15px"></span>
                                                <span class="small">Upload a file with the image of your <?php echo ucwords($aProductSpecificDetails['product_type']); ?>.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

<div class="col-md-12">
<!-- Container for Image Previews -->
<div id="image-preview-container"></div>

<!-- Full-Size Image Modal -->
<div id="image-modal" class="modal" style="display: none;">
<span id="close-modal">&times;</span>
<img id="modal-image" src="" alt="Full-Size Preview">
</div>
</div>
<style>
    /* Style for Image Preview Thumbnails */
    #image-preview-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .image-preview {
        position: relative;
        width: 150px;
        height: 150px;
        margin-right: 10px;
        margin-bottom: 10px;
        overflow: hidden;
        border: 1px solid #ccc;
        border-radius: 5px;
        cursor: pointer;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Modal Styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 5px;
    }

    /* Close Button Style */
    #close-modal {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 30px;
        color: #fff;
        cursor: pointer;
    }
</style>

<script>
	$(document).ready(function() {
        $('#type').on('change', function() {
            var selectedValue = $(this).val();
            if (selectedValue == 'logo') {
                $('#img-upload').show();
            } else {
                $('#img-upload').hide();
            }
        });
        $('#product_images').on('change', function(event) {
            const previewContainer = $('#image-preview-container');
            previewContainer.empty();

            const files = event.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!file.type.startsWith('image/')) {
                    continue;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    const imgElement = $('<div class="image-preview"><img src="' + e.target.result + '" alt="Image Preview"></div>');
                    imgElement.on('click', function() {
                        showModal(e.target.result);
                    });
                    previewContainer.append(imgElement);
                };

                reader.readAsDataURL(file);
            }
        });

        function showModal(imageSrc) {
            const modal = $('#image-modal');
            const modalImage = $('#modal-image');
            modalImage.attr('src', imageSrc);
            modal.css('display', 'flex');
        }

        $('#close-modal').on('click', function() {
            $('#image-modal').hide();
        });

        $(window).on('click', function(event) {
            const modal = $('#image-modal');
            if ($(event.target).is(modal)) {
                modal.hide();
            }
        });
    });
</script>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-container">
                                                <label>Currently using
                                                    <?php echo ucwords($aProductSpecificDetails['product_type']); ?>?</label>
												<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Button Example</title>
    <style>
        .date-container {
            display: none; /* Hide date input by default */
        }
        .radio {
            margin: 10px 0;
        }
    </style>
</head>
<body>
												
                                                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Button Example</title>
    <style>
        .date-container {
            display: none; /* Hide date input by default */
        }
        .radio {
            margin: 10px 0;
        }
    </style>
</head>
<body>

<div class="radio">
    <label class="radio_input">Yes
        <input type="radio" name="product_inuse" value="yes" id="yesOption">
        <span class="checkmark"></span>
    </label>
</div>
<div class="radio">
    <label class="radio_input">No
        <input type="radio" checked="checked" name="product_inuse" value="no" id="noOption">
        <span class="checkmark"></span>
    </label>
</div>
<div class="dtpkr tdate date-container" id="dateContainer">
    <div class="wthicon">
        <input class="form-control" id="date" name="product_date" placeholder="MM/DD/YYYY" type="text">
        <span class="bg-calendar_icon"></span>
    </div>
</div>

<script>
    document.getElementById('yesOption').addEventListener('change', function() {
        document.getElementById('dateContainer').style.display = 'block';
    });

    document.getElementById('noOption').addEventListener('change', function() {
        document.getElementById('dateContainer').style.display = 'none';
    });
</script>

</body>
</html>
                                                <?php if($aProductDetails['prod_name'] == "USA"){ ?>
                                                    <span class="small">If you are not using mark currently, please note you will be charged Government fee $100 at the time of filling statement of use.</span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $aAddonGroups = array_unique(array_column($context['aData'], 'group_slug')); ?>
                            <?php if(!empty(array_filter($aAddonGroups))){ ?>
                                <div class="top-form">
                                    <div class="col-sm-12">
                                        <div class="input-container">
                                            <h2 class="hdg">Select classes for your <?php echo ucwords($aProductSpecificDetails['product_type']); ?></h2>
                                            <p class="para">Select the classes in which to register your
                                                <?php echo ucwords($aProductSpecificDetails['product_type']); ?>, depending on which products or services the mark will be used for. Once you have selected the classes fill in the corresponding boxes with the description of products or services.</p>
                                            <span class="small"><i>*Note:</i>Sample text about the price if he adds classes.</span>
                                        </div>
                                        <?php foreach($aAddonGroups as $sAddonGroup){ ?>
                                            <?php $currentVal = 1; ?>
                                            <div class="input-container">
                                                <label class="uppercase">
                                                    <?php echo $sAddonGroup; ?> Classes</label>
                                                <div class="class-box faqs">
                                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                                        <?php foreach($context['aData'] as $aAddons){ ?>
                                                            <?php if($aAddons['group_slug'] == $sAddonGroup) { ?>
                                                                <?php $bIsFirst = $currentVal == 1; $currentVal++; ?>
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading" role="tab" id="heading<?php echo $aAddons['addon_id']; ?>">
                                                                        <h4 class="panel-title">
                                                                            <label class="check_input">
                                                                                <a <?php echo !$bIsFirst ? 'class="collapsed"' : ""; ?> role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $aAddons['addon_id']; ?>" aria-expanded="<?php echo $bIsFirst ? 'true' : "false"; ?>" aria-controls="collapse<?php echo $aAddons['addon_id']; ?>"><?php echo strip_tags($aAddons['addon_name']); ?></a>
                                                                                <input type="checkbox" class="addon-select" name="addon[]" value="<?php echo $aAddons['addon_id']; ?>">
                                                                                <span class="checkmark"></span>
                                                                            </label>
                                                                        </h4>
                                                                    </div>
                                                                    <div id="collapse<?php echo $aAddons['addon_id']; ?>" class="panel-collapse collapse<?php echo $bIsFirst ? " in ": " "; ?>" role="tabpanel" aria-labelledby="heading<?php echo $aAddons['addon_id']; ?>">
                                                                        <div class="panel-body">
                                                                            <?php echo strip_tags($aAddons['addon_desc']); ?>
                                                                        </div>
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
                                                                        <a role="button" data-toggle="collapse" data-parent="#unassure1" class="collapsed" href="#collapseSurity1" aria-expanded="true" aria-controls="collapseSurity1">I am unsure</a>
                                                                        <input type="checkbox" name="unsure-<?php echo $sAddonGroup; ?>" class="unsure">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                </h4>
                                                            </div>
                                                            <div id="collapseSurity1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="surity1">
                                                                <div class="panel-body">Chemical Products; Chemicals used in industry, science and photography, as well as in agriculture, horticulture and forestry; unprocessed artificial resins; unprocessed plastics; manures; fire extinguishing compositions; tempering and soldering preparations; chemical substances for preserving foodstuffs; tanning substances; adhesives used in industry.</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="input-container" id="unsure-<?php echo $sAddonGroup; ?>-notes" style="display:none">
                                                <label>Notes</label>
                                                <textarea rows="8" name="unsure-<?php echo $sAddonGroup; ?>-notes" class="form-control" placeholder="Some notes for us..."></textarea>
                                            </div>
                                        <?php } ?>
                                        <div class="input-container"><span class="small">If you are not sure how to fill in the boxes, leave them blank and an IP Consultant will get back to you in case the information is required.</span></div>
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
                                                    <input type="text" name="email" placeholder="Enter your email address" class="form-control" id="email" required>
                                                    <span class="bg-email_icon"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="input-container">
                                                <label class="required" for="phone">Phone</label>
                                                <div class="wthicon">
                                                    <input type="text" name="phone" placeholder="Enter your phone number" class="form-control" id="phone" required>
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
                            <div class="mob-center">
            <div class="mob-left">
                <div class="col-sm-12">
                    <div class="row">
                        <!-- Quantity Input -->
                        <div class="col-sm-6">
                            <div class="input-container">
                                <label for="count">Quantity<span class="small">(Int. Nice Classes)</span></label>
                                <span class="number-wrapper">
                                    <input type="number" disabled="disabled" class="form-control count" id="count" value="1" min="0">
                                </span>
                            </div>
                        </div>
                        
                        
                        <!-- Order Summary (Placed between Quantity and Add to Cart Button) -->
                        <div class="col-sm-12">
                            <section class="fxditms">
                                <div class="ordrsmry">
                                    <h3 class="hdg">Order Summary</h3>
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>Country:</td>
                                                <td><?php echo $aProductDetails['prod_name']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Package:</td>
                                                <td><?php echo $aProductDetails['name']; ?>
                                                    <br>$<?php echo $aProductDetails['price']; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Additional Class:</td>
                                                <td id="addon-summary">0 x $<?php echo $aProductDetails['class_price']; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total:</td>
                                                <td><b id="cart-total">$<?php echo $aProductDetails['price']; ?></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
						<!-- Total -->
                        <div class="col-sm-6">
                            <div class="input-container">
                                <label>Total</label>
                                <span class="amount">$<?php echo $aProductDetails['price']; ?></span>
                            </div>
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <div class="col-sm-12">
                            <button type="submit" class="btn-blk small" name="submit" value="submit">Add to Cart<span class="arrow"></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<!-- Related Products Section -->
<?php echo do_shortcode('[relatedproduct]'); ?>