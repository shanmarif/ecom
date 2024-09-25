<?php
    $aProductDetail = current($context['aData']);
    $aAttr = $context['aAttr'];
    $sSlogan = [
        'standard' => "Pay as you go",
        'premium' => "All in One",
        'professional' => "Full Assurance + Extras"
    ];
?>
<?php if(!empty($aProductDetail)){ ?>
    <?php if(!empty($aAttr['elementor']) && $aAttr['elementor'] == false){ ?>
    <section class="bnr crt">
        <div class="container">
            <div class="row">
                <div class="flex-box">
                    <div class="col-sm-12">
                        <div class="text-center">
                            <img src="<?php echo $aProductDetail['secondary_image']; ?>" width="70px" height="46px" alt="img" class="img-responsive">
                            <h1 class="bnr-hdg"><span>Trademark Registration in</span>
                                <?php echo $aProductDetail['prod_name']; ?></h1>
                            <p class="bnr-para">File the trademark in just 3 simple steps. You can file either design, word,
                                or both in one single application. We assure you to give you the best solution and advice
                                for your business protection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>
<section class="crtcntnr">
    <div class="container">
        <div class="row">
            <div class="flex-box">
                <?php
                    $iPackageCount = count($context['aData']);
                    if(isset($aAttr['hidepackage'])){
                        $aPackages = array_column($context['aData'], 'name');
                        if(in_array($aAttr['hidepackage'], $aPackages)){
                            $iPackageCount = $iPackageCount -1;        
                        }
                    }
                    $iMedian = $iPackageCount > 0 ? ceil($iPackageCount/2): 0;
                    $context['aData'][$iMedian-1]['isActive'] = true;
                    $sCol = ceil(12/$iPackageCount);  
                ?>
                <?php foreach($context['aData'] as $aProduct){ ?>
                <?php if(!isset($aAttr['hidepackage']) || strtolower($aProduct['name']) != strtolower($aAttr['hidepackage'])){ ?>
                    <div class="col-sm-<?php echo $sCol; ?>">
                        <div class="crtbox <?php echo isset($aProduct['isActive']) ? 'active' : ''; ?>">
                            <h2 class="hdg"><?php echo $aProduct['name']; ?></h2>
                            <h3 class="hdg">$<?php echo $aProduct['price']; ?></h3>
                            <span class="blwprc"><?php echo $sSlogan[strtolower($aProduct['name'])]; ?></span>
                            <form action="/form/" method="post">
                                <input type="hidden" name="product" value="<?php echo $aProduct['prod_slug']; ?>" />
                                <input type="hidden" name="pricing_id" value="<?php echo $aProduct['price_id']; ?>" />
                                <button class="btn-blk" type="submit">Choose Package</button>
                            </form>
                            <h4 class="hdg">Features Included </h4>
                            <?php echo isset($aProduct['package_details']) && !empty($aProduct['package_details']) ? trim($aProduct['package_details']) : $aProduct['description']; ?>
                        </div>
                    </div>
                <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php } ?>