<?php

class PagoSeguroPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /*
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        $cart = $this->context->cart;
        if (!$this->module->active ||
            null == $cart->id ||
            0 == $cart->id_customer ||
            0 == $cart->id_address_delivery ||
            0 == $cart->id_address_invoice) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect($this->context->link->getPageLink('order'));
        }
    }

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ('pagoseguro' == $module['name']) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('paymentMethodDisabled.', 'payment'));
        }

        // Cart
        $cart                       = $this->context->cart;
        $total                      = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $currency                   = $this->context->currency;
        $orderStatus                = 14;

        // Customer
        $customer                   = new Customer($cart->id_customer);
        $customerFullName           = $customer->firstname.' '.$customer->lastname;
        $customerEmail              = $customer->email;

        // Active email data

        $url = 1 == Configuration::get('PAGOSEGURO_TEST_MODE') ? Configuration::get('PAGOSEGURO_URL_TEST') : Configuration::get('PAGOSEGURO_URL_PAYMENT');
        if (false == $url) {
            $this->errors[] = $this->module->l('moduleWithoutConfig', 'payment');
            $this->redirectWithNotifications($this->context->link->getPageLink('order'));
        }

        $mailVars = [
        ];

        // Validate Order
        $this->module->validateOrder(
            $cart->id,
            $orderStatus,
            $total,
            $this->module->displayName,
            null,
            $mailVars,
            (int) $currency->id,
            false,
            $customer->secure_key);

        // Parameters Pago Seguro

        $accountId              = Configuration::get('PAGOSEGURO_ACCOUNT_ID');
        $apiKey                 = Configuration::get('PAGOSEGURO_API_KEY');

        // Order
        $order                       = new Order($this->module->currentOrder); // $this->module->currentOrder is generate after validateOrder
        $orderReference              = ($order->reference ? $order->reference : 0);
        $product                     = $order->getCartProducts()[0]['product_name'];

        $stringSignature            = $accountId.'|'.$orderReference.'|'.$total.'|'.$product.'|'.$customerFullName.'|'.$customerEmail.'|'.'/payment/process||||||||||'.$apiKey;
        $signature                  = hash('sha512', $stringSignature);
        $urlResponse                = $this->context->link->getPageLink('guest-tracking.php', true).'?id_order='.$order->id;
        $this->context->smarty->assign([
            'URL'              => $url,
            'KEY'              => $accountId,
            'ORDER_ID'         => $orderReference,
            'AMOUNT'           => $total,
            'PRODUCT'          => $product,
            'CUSTOMER'         => $customerFullName,
            'CUSTOMER_EMAIL'   => $customerEmail,
            'SIGNATURE'        => $signature,
            'URLRESPONSE'      => $urlResponse,
        ]);
        $this->setTemplate('module:pagoseguro/views/templates/front/payment.tpl');
    }

    /**
     * @see FrontController::setMedia()
     */
    public function setMedia()
    {
        parent::setMedia();
    }

    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
    }
}