<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PagoSeguro extends PaymentModule
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name                   = 'pagoseguro';
        $this->tab                    = 'payments_gateways';
        $this->version                = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->author                 = 'Widres8';

        $this->controllers            = ['validation'];
        $this->is_eu_compatible       = 1;

        $this->currencies      = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap       = true;

        parent::__construct();

        $this->displayName            = $this->l('PagoSeguro');
        $this->description            = $this->l('PagoSeguroDescription');
        $this->confirmUninstall       = $this->l('UninstallMessage');
        if (!count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = $this->l('NotSetCurrency');
        }
    }

    /**
     * Config install module
     */
    public function install()
    {
        if (
            !parent::install() ||
            !$this->registerHook('paymentOptions') ||
            !$this->registerHook('paymentReturn') ||
            !$this->registerHook('displayHeader')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Config uninstall module
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('PAGOSEGURO_ACCOUNT_ID') ||
            !Configuration::deleteByName('PAGOSEGURO_API_KEY') ||
            !Configuration::deleteByName('PAGOSEGURO_TEST_MODE') ||
            !Configuration::deleteByName('PAGOSEGURO_URL_TEST') ||
            !Configuration::deleteByName('PAGOSEGURO_URL_PAYMENT')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Show module in payment select option how radio button
     * @param mixed $params
     */
    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return;
        }

        if (!$this->checkCurrency($params['cart'])) {
            return;
        }

        $this->context->smarty->assign(
            $this->getTemplateVars()
        );

        $payment_options = [
            $this->getExternalPaymentOption(),
        ];

        return $payment_options;
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active) {
            return;
        }

        // $state = $params['order']->getCurrentState();
        // if (in_array($state, array(Configuration::get('PS_OS_CHEQUE'), Configuration::get('PS_OS_OUTOFSTOCK'), Configuration::get('PS_OS_OUTOFSTOCK_UNPAID')))) {
        //     $this->smarty->assign(array(
        //         'total_to_pay' => Tools::displayPrice(
        //             $params['order']->getOrdersTotalPaid(),
        //             new Currency($params['order']->id_currency),
        //             false
        //         ),
        //         'shop_name' => $this->context->shop->name,
        //         'checkName' => $this->checkName,
        //         'checkAddress' => Tools::nl2br($this->address),
        //         'status' => 'ok',
        //         'id_order' => $params['order']->id
        //     ));
        //     if (isset($params['order']->reference) && !empty($params['order']->reference)) {
        //         $this->smarty->assign('reference', $params['order']->reference);
        //     }
        // } else {
        //     $this->smarty->assign('status', 'failed');
        // }
        // return $this->fetch('module:ps_checkpayment/views/templates/hook/payment_return.tpl');
    }

    /**
     * Load CSS and JS files
     * @param mixed $params
     */
    public function hookdisplayHeader($params)
    {
        if ('order' === $this->context->controller->php_self) {
            $this->context->controller->registerStylesheet('modules-pagoseguro', 'modules/'.$this->name.'/css/payu.css', ['media' => 'all', 'priority' => 200]);
            $this->context->controller->registerJavascript('modules-pagoseguro', 'modules/'.$this->name.'/js/payu.js', ['position' => 'bottom', 'priority' => 200]);
        }
    }

    /**
     * Get current Currency
     * @param mixed $cart
     */
    public function checkCurrency($cart)
    {
        $currency_order    = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Config and validation to show option payment
     */
    public function getExternalPaymentOption()
    {
        $externalOption = new PaymentOption();
        $externalOption->setModuleName($this->name)
                    ->setCallToActionText($this->l(''))
                    ->setAction($this->context->link->getModuleLink($this->name, 'payment', [], true))
                    ->setAdditionalInformation($this->context->smarty->fetch('module:pagoseguro/views/templates/hook/hook_payment_detail.tpl'))
                    ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'/img/payment_logo.png'));

        return $externalOption;
    }

    /**
     * Get content to configurer module
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $accountId     = Tools::getValue('PAGOSEGURO_ACCOUNT_ID');
            $apiKey        = Tools::getValue('PAGOSEGURO_API_KEY');
            $testMode      = 1 == Tools::getValue('PAGOSEGURO_TEST_MODE') ? true : false;
            $urlApiTest    = Tools::getValue('PAGOSEGURO_URL_TEST');
            $urlApi        = Tools::getValue('PAGOSEGURO_URL_PAYMENT');

            if ('' == $accountId || '' == $apiKey || '' == $urlApi || '' == $urlApiTest) {
                $errors[] = $this->l('NotSaveConfiguration');
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            } else {
                Configuration::updateValue('PAGOSEGURO_ACCOUNT_ID', $accountId);
                Configuration::updateValue('PAGOSEGURO_API_KEY', $apiKey);
                Configuration::updateValue('PAGOSEGURO_TEST_MODE', $testMode);
                Configuration::updateValue('PAGOSEGURO_URL_TEST', $urlApiTest);
                Configuration::updateValue('PAGOSEGURO_URL_PAYMENT', $urlApi);
                $output .= $this->displayConfirmation($this->l('SettingsUpdated'));
            }
        }
        $this->context->smarty->assign([
            'PAGOSEGURO_ACCOUNT_ID'     => Configuration::get('PAGOSEGURO_ACCOUNT_ID'),
            'PAGOSEGURO_API_KEY'        => Configuration::get('PAGOSEGURO_API_KEY'),
            'PAGOSEGURO_TEST_MODE'      => Configuration::get('PAGOSEGURO_TEST_MODE'),
            'PAGOSEGURO_URL_TEST'       => Configuration::get('PAGOSEGURO_URL_TEST'),
            'PAGOSEGURO_URL_PAYMENT'    => Configuration::get('PAGOSEGURO_URL_PAYMENT'),
        ]);

        return $output.$this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    /**
     * Return total value in payment option details
     */
    public function getTemplateVars()
    {
        $cart  = $this->context->cart;
        $total = $this->trans(
            '%amount% (tax incl.)',
            [
                '%amount%' => Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH)),
            ]
        );

        return [
            'totalAmount' => $total,
        ];
    }
}