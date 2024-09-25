<section class="trdmrk formpage sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="text-center">
                    <h2 class="hdg">Related Products</h2>
                </div>
                <?php foreach($context['aData'] as $aProduct){
                    // Check if the product type is design or trademark
                    if($aProduct['type'] == 'design' || $aProduct['type'] == 'trademark') {
                ?>
                    <div class="col-sm-4">
                        <div class="tile text-left">
                            <img src="<?php echo $aProduct['image']; ?>" alt="img1" class="img-responsive">
                            <div class="cntnt">
                                <h4 class="hdg"><?php echo ucwords($aProduct['type']); ?> Registration in <?php echo $aProduct['product']; ?></h4>
                                <a href="/<?php echo !empty($aProduct['link']) ? $aProduct['link'] : $aProduct['type']."/".$aProduct['prod_slug']; ?>" class="btn-blk">Register <?php echo ucwords($aProduct['type']); ?></a>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                } ?>
            </div>
        </div>
    </div>
</section>
