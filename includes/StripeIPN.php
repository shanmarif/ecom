<?php

// require_once( 'vendor/stripe/init.php' );

class StripeIPN
{
    /** @var bool Indicates if the sandbox endpoint is used. */
    private $use_sandbox = false;
    /** @var bool Indicates if the local certificates are used. */
    private $use_local_certs = true;

    /** Production Postback URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';

    /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
    public function useSandbox($bool = false)
    {
        $this->use_sandbox = $bool;
    }

    /**
     * Sets curl to use php curl's built in certs (may be required in some
     * environments).
     * @return void
     */
    public function usePHPCerts()
    {
        $this->use_local_certs = false;
    }

    /**
     * Determine endpoint to post the verification data to.
     *
     * @return string
     */
    public function getStripeKey()
    {
        if ($this->use_sandbox) {
            return STRIPE_SANDBOX_KEY;
        } else {
            return STRIPE_PRODUCTION_KEY;
        }
    }

    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()
    {
        $stripe = new \Stripe\StripeClient($this->getStripeKey());

        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            \Stripe\Stripe::setApiKey($this->getStripeKey());
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        }
        // Handle the event
        $data = false;
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $data = $event->data->object; // contains a \Stripe\PaymentIntent
                break;
            case 'payment_method.attached':
                break;
            // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
                break;
        }

        return $data;
    }
}