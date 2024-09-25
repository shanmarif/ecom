<?php
    $aProductTypeDetails = [];
    foreach($context['aData'] as $aProducts){
        $aProductTypeDetails[$aProducts['type']][] = $aProducts;
    }
?>
<section class="trdmrk sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="carousel-example-generic" class="carousel slide prod-slider" data-interval="false" data-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <?php 
                            $aProductTypes = array_unique(array_column($context['aData'], 'type'));
                            foreach($aProductTypes as $sProductType){
                        ?>
                            <div data-prod-type="<?php echo $sProductType; ?>" class="item <?php echo current($aProductTypes) == $sProductType ? "active" : "inactive"; ?>">
                                <h2 class="hdg"><?php echo ucwords($sProductType); ?> </h2>
                            </div>
                        <?php } ?>
                    </div>
                    <a class="left carousel-control prod-slider-controls" href="#carousel-example-generic" role="button" data-slide="prev">
                        <span class="fa fa-chevron-left" aria-hidden="true"></span>
                    </a>
                    <a class="right carousel-control prod-slider-controls" href="#carousel-example-generic" role="button" data-slide="next">
                        <span class="fa fa-chevron-right" aria-hidden="true"></span>
                    </a>
                </div>

                <?php foreach($aProductTypes as $sType){ ?>
                <!-- Tabs -->
                <div class="carousel-prod-details text-center <?php echo current($aProductTypes) == $sType ? "active" : "inactive"; ?>" id="prodtype-<?php echo strtolower($sType); ?>">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <?php 
                            $aProductGroups = array_unique(array_column($aProductTypeDetails[$sType], 'name', 'slug'));
                            foreach($aProductGroups as $sGroupSlug => $sProductGroup){
                        ?>
                            <li role="presentation" data-product-type="<?php echo $sType; ?>" data-group-name="<?php echo $sProductGroup; ?>" data-group-slug="<?php echo $sGroupSlug; ?>" class="product-group-nav<?php echo current($aProductGroups) == $sProductGroup ? " active" : "" ;?>"><a href="#<?php echo $sType."-".$sGroupSlug; ?>" aria-controls="<?php echo $sType."-".$sGroupSlug; ?>" role="tab" data-toggle="tab" ><?php echo $sProductGroup; ?></a></li>
                        <?php } ?>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
						<?php $firstGroup[$sType] = null; ?>
                        <?php foreach($aProductGroups as $sGroupSlug => $sProductGroup){ ?>
                            <?php $i = 0; ?>
							<?php $firstGroup[$sType] = is_null($firstGroup[$sType]) ? $sGroupSlug : $firstGroup[$sType]; ?>
                            <div role="tabpanel" class="tab-pane <?php echo current($aProductGroups) == $sProductGroup ? "active" : "" ;?>" id="<?php echo $sType."-".$sGroupSlug; ?>">
                            <?php foreach($aProductTypeDetails[$sType] as $aProduct) { ?>
                                <?php if($aProduct['slug'] == $sGroupSlug && $i < 3): ?>
                                <div class="col-sm-4">
                                    <div class="tile text-left">
                                        <img src="<?php echo $aProduct['image']; ?>" alt="img1" class="img-responsive">
                                        <div class="cntnt">
                                            <h4 class="hdg"><?php echo $aProduct['product']; ?></h4>
                                            <h4 class="hdg to-right">$<?php echo $aProduct['price']??0; ?></h4>
                                            <p class="para"></p>
                                            <a href="/<?php echo !empty($aProduct['link']) ? $aProduct['link'] : $aProduct['type']."/".$aProduct['prod_slug']; ?>" class="btn-blk" style="display: block; margin: 0 auto;">Register <?php echo ucwords($sType); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php $i++; endif; ?>
                            <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="clearfix"></div>
                   <a target="_blank" href="<?php echo get_site_url(); ?>/group-page/?type=<?php echo $sType; ?>&group=<?php echo $firstGroup[$sType]; ?>" class="text-cta prod-slider-seeall">See all <?php echo current($aProductGroups); ?> Countries</a>

                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>