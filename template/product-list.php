<section class="trdmrk sec">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
				<?php foreach($context['aData'] as $aProduct){ ?>
				<div class="col-sm-4">
				    <div class="tile text-left">
				        <img src="<?php echo $aProduct['image']; ?>" width="350px" height="190px" alt="img1" class="img-responsive">
				        <div class="cntnt">
				            <h4 class="hdg"><?php echo $aProduct['product']; ?></h4>
				            <h4 class="hdg to-right">$<?php echo $aProduct['price']??0; ?></h4>
				            <p class="para"></p>
				            <a href="/<?php echo !empty($aProduct['link']) ? $aProduct['link'] : $aProduct['type']."/".$aProduct['prod_slug']; ?>" class="btn-blk" style="display: block;">Register <?php echo ucwords($aProduct['type']); ?></a>
				        </div>
				    </div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</section>