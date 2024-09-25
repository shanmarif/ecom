<section class="bnr chkot">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb">
                    <li><a href="#">Products</a></li>
                    <li><a href="#">Shop</a></li>
                    <li><a href="#">Cart</a></li>
                    <li class="active">Checkout</li>
                </ol>
            </div>
            <div class="col-sm-12">
                <h1 class="bnr-hdg">Checkout</h1>
                <p class="bnr-para">Returning customer? <a href=""> Click here to login</a></p>
            </div>
        </div>
    </div>
</section>

<section class="gtwysec sec">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="gtwyscntnr">
                    <form action="<?php echo admin_url( 'admin-ajax.php' ); ?>" method="POST" id="paymentForm">
                        <!-- PayPal Section -->
                        <div class="gtwys">
                            <div class="radio">
                                <label class="radio_input">PayPal
                                    <div class="gtwyimg">
                                        <img src="/wp-content/uploads/2021/09/img_paypal.png" alt="PayPal" class="img-responsive" style="width: 100px;">
                                    </div>
                                    <input type="radio" name="paymentmethod" value="paypal">
                                    <span class="checkmark"> </span>
                                </label>
                            </div>
                        </div>
                        <p class="para small">Pay via PayPal or You can also pay with your local credit card or debit card
                            if you don’t have a PayPal account.</p>

                        <!-- Stripe Section -->
                        <div class="gtwys">
                            <div class="radio">
                                <label class="radio_input">Stripe
                                    <div class="gtwyimg">
                                         <img src="http://theipprotector.com/wp-content/uploads/2024/01/stripe_logo_icon_145416.png" alt="Stripe" class="img-responsive" style="width: 100px; height: 80px;"> <!-- Adjust the height as needed -->
                                    </div>
                                    <input type="radio" name="paymentmethod" value="stripe">
                                    <span class="checkmark"> </span>
                                </label>
                            </div>
                        </div>
                        <p class="para small">Pay via Stripe: Accepts major credit cards.</p>

                        <!-- Add Publishable and Secret Keys -->
                        <input type="hidden" name="stripe_publishable_key" value="<?php echo STRIPE_PUBLIC_KEY; ?>">
                        <input type="hidden" name="stripe_secret_key" value="<?php echo STRIPE_SECRET_KEY; ?>">

                        <!-- Direct Bank Transfer Section -->
                        <div class="gtwys">
                            <div class="radio">
                                <label class="radio_input">Direct Bank Transfer
                                    <div class="gtwyimg">
                                        <img src="/wp-content/uploads/2021/09/img_direct_pay.png" alt="img" class="img-responsive">
                                    </div>
                                    <input type="radio" name="paymentmethod" value="dbt">
                                    <span class="checkmark"> </span>
                                </label>
                            </div>
                        </div>
                        <p class="para small">Please check your e-mail for bank details. Make your payment directly into our bank account. Please use your Order ID as
                            the payment reference. Your order will not be shipped until the funds have cleared in our
                            account.
                            <span>In case you wish to transfer in any other currency, Please <a href="/contact-us">CONTACT US</a> and one of our customer
                                support experts will help you in transferring funds in your local currency.</span></p>

                        <p class="para sep">Your personal data will be used to process your order, support your experience
                            throughout this website, and for other purposes described in our <a href="/privacy-policy">privacy policy.</a>
                        </p>
                        <?php wp_nonce_field('add_product_to_cart', 'security-code-here'); ?>
                        <input name="action" value="checkout" type="hidden" />
                        <button type="submit" value="submit" class="btn-blk" name="submit">Place Order<span class="arrow"></span></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
