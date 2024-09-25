<?php
    $aCartDetails = isset($context['aCartDetails']['products']) ? $context['aCartDetails']['products'] : [];
    $aAddonDetails = $context['aAddonDetails'];
    $aPricingDetails = $context['aPricingDetails'];
    $aProductPricings = $context['aProductPricings'];
    $aClassPricingDetails = $context['aClassPricingDetails'];
    $aAdditionalServices = $context['aAdditionalServices'];
    $aProductImages = $context['aProductImages'];
    $aGrandTotal = 0;
?>
<section class="bnr prdt">
    <div class="container">
        <div class="row">
            <div class="flex-box">
                <div class="col-sm-12">
                    <ol class="breadcrumb">
                        <li><a href="#">Products</a></li>
                        <li><a href="#">Shop</a></li>
                        <li class="active">Cart</li>
                    </ol>
                </div>
                <div class="col-sm-12">
                    <h1 class="bnr-hdg">Product Cart</h1>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="prdtcrt">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <?php foreach($aCartDetails as $iKey => $aPackage){ ?>
                <?php
                    if(isset($aPackage['product_name'])){
                    $aAddonCount = isset($aPackage['addon']) ? count($aPackage['addon']) : 0;
					foreach($aProductPricings as $package_detail){
						if($package_detail["id"] == $aPackage['package_id']){
							$sProductType = $package_detail["package_type"];
						}
					}
                    $iPackagePrice = isset($aPricingDetails[$aPackage['package_id']]) ? $aPricingDetails[$aPackage['package_id']] : 0;
                    $iClassPrice = isset($aClassPricingDetails[$aPackage['package_id']]) ? $aClassPricingDetails[$aPackage['package_id']] : 0;
                    $aTotalPrice = (($aAddonCount > 0 ? $aAddonCount-1 : 0)*$iClassPrice)+$iPackagePrice;
                    $aGrandTotal += $aTotalPrice;
                ?>
                <div class="prdtcntnr" id="package-<?php echo $aPackage['package_id']; ?>" data-price="<?php echo $aTotalPrice; ?>">
                    <div class="flex-box">
                        <div class="col-sm-7">
                            <h2 class="hdg">Package</h2>
                            <div class="dtls">
                                <div class="imgcntnr">
                                    <img src="<?php echo isset($aProductImages[$aPackage['package_id']]) ? $aProductImages[$aPackage['package_id']] : ''; ?>" alt="img" class="img-responsive">
                                </div>
                                <div class="txtcntnr">
                                    <h3 class="hdg"><?php echo $sProductType == "trademark" ? "Trademark Registration in " : ''; ?><?php echo $aPackage['product_name']; ?></h3>
                                    <div class="inptcntnr">
                                        <label>Package: </label>
                                        <p><?php echo $aPackage['package_name']; ?></p>
                                    </div>
									<?php if(!empty($aPackage['trademark_type'])) {?>
                                    <div class="inptcntnr">
                                        <label>Trademark type: </label>
                                        <p><?php echo ucwords($aPackage['trademark_type']); ?></p>
                                    </div>
									<?php } ?>
                                    <?php if(!empty($aPackage['trademark_text'])) {?>
                                    <div class="inptcntnr">
                                        <label>Trademark text: </label>
                                        <p><?php echo ucwords($aPackage['trademark_text']); ?></p>
                                    </div>
									<?php } ?>
                                    <?php if(!empty($aPackage['trademark_inuse'])) {?>
                                    <div class="inptcntnr">
                                        <label>Using trademark: </label>
                                        <p><?php echo ucwords($aPackage['trademark_inuse']); ?></p>
                                    </div>
									<?php } ?>
                                    <?php if(isset($aPackage['addon'])){ ?>
                                        <?php foreach($aPackage['addon'] as $aAddon){ ?>
                                        <div class="inptcntnr">
                                            <label><?php echo $aAddonDetails[$aAddon]['group_name']; ?>: Class <?php echo $aAddonDetails[$aAddon]['addon_class']; ?>: </label>
                                            <p><?php echo $aAddonDetails[$aAddon]['addon_name']; ?></p>
                                        </div>
                                        <?php } ?>
                                    <?php } ?>

                                    <div class="inptcntnr">
                                        <label>Name: </label>
                                        <p><?php echo ucwords($aPackage['name']); ?></p>
                                    </div>
                                    <div class="inptcntnr">
                                        <label> Email: </label>
                                        <p><?php echo $aPackage['email']; ?></p>
                                    </div>
                                    <div class="inptcntnr inptmrgn">
                                        <label>Phone: </label>
                                        <p><?php echo $aPackage['phone']; ?></p>
                                    </div>
                                    <div class="inptcntnr">
                                        <p><b>Notes: </b><?php echo $aPackage['notes']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <h2 class="hdg">Price</h2>
                            <div class="dtls">
                                <div class="txtcntnr">
                                    <h3 class="hdg">$<?php echo $aTotalPrice; ?></h3>
                                    <div class="inptcntnr">
                                        <label>Package: </label>
                                        <p>$<?php echo $iPackagePrice; ?></p>
                                    </div>
                                    <div class="inptcntnr">
                                        <label>Additional classes x <?php echo $aAddonCount-1 > 0 ? $aAddonCount-1 : 0; ?>: </label>
                                        <p>$<?php echo $aAddonCount-1 > 0 ? ($aAddonCount-1)*$iClassPrice : 0; ?></p>
                                    </div>
                                </div>
                                <div class="actns to-right">
                                    <a class="dlt" href="javascript:void(0);" onclick="delPackage('package-<?php echo $iKey; ?>', 'package-<?php echo $aPackage['package_id']; ?>')"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }} ?>
            </div>
            <?php foreach($aAdditionalServices as $aAdditionalService){ 
                $aCartAdditionalServices = isset($context['aCartDetails']['additional_services']) ? $context['aCartDetails']['additional_services'] : [];
                if(in_array($aAdditionalService['id'], $aCartAdditionalServices)){
                    $aGrandTotal += $aAdditionalService['price'];
                }
            } ?>
            <div class="col-sm-5 col-sm-push-7">
                <div class="ttl">
                    <h2 class="hdg">Price</h2>
                    <div class="dtls">
                        <div class="txtcntnr">
                            <h3 class="hdg" id="grandtotal" data-grandtotal="<?php echo $aGrandTotal; ?>">$<?php echo $aGrandTotal; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-7 col-sm-pull-5 mob-center">
                <a class="btn-blk" href="/checkout">Proceed to Checkout<span class="arrow"></span></a>
            </div>
            <div class="col-sm-12">
                <div class="othrsrvs">
                    <div class="col-sm-12">
                        <h2 class="hdg">Additional Services</h2>
                        <div class="row">
                            <?php foreach($aAdditionalServices as $aAdditionalService){ ?>
                                <?php 
                                $aCartAdditionalServices = isset($context['aCartDetails']['additional_services']) ? $context['aCartDetails']['additional_services'] : [];
                                $bAddedToCard = in_array($aAdditionalService['id'], $aCartAdditionalServices);
                                ?>
                                <div class="col-md-4 col-sm-6">
                                    <div class="srvs">
                                        <label class="check_input"><?php echo $aAdditionalService['name']; ?>
                                            <input type="checkbox" <?php echo $bAddedToCard ? "checked" : ""; ?> data-price="<?php echo $aAdditionalService['price']; ?>" name="additional_services" value="<?php echo $aAdditionalService['id']; ?>">
                                            <span class="checkmark"></span>
                                        </label>
                                        <div class="to-right">
                                            <span>+$<?php echo $aAdditionalService['price']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>