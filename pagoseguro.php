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
        $this->name    = 'pagoseguro';
        $this->author  = 'Widres8';
        $this->version = '1.0.0';
        $this->tab     = 'payments_gateways';

        $this->currencies      = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName            = $this->l('PagoSeguro');
        $this->description            = $this->l('PagoSeguroDescription');
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => '1.7.99.99'];

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
            !Configuration::deleteByName('PAGOSEGURO_API_KEY')
        ) {
            return false;
        }

        return true;
    }

    /**
     * Get content to configurer module
     */
    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            if (
                !Configuration::updateValue('PAGOSEGURO_ACCOUNT_ID', (string) Tools::getValue('PAGOSEGURO_ACCOUNT_ID')) ||
                !Configuration::updateValue('PAGOSEGURO_API_KEY', (string) Tools::getValue('PAGOSEGURO_API_KEY'))
            ) {
                $errors[] = $this->l('NotSaveConfiguration');
            }
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            } else {
                $output .= $this->displayConfirmation($this->l('SettingsUpdated'));
            }
        }

        return  $output.$this->display(__FILE__, 'views/templates/admin/configure.tpl');
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

        $this->smarty->assign(
            $this->getTemplateVars()
        );

        $payment_options = [
            $this->getExternalPaymentOption(),
        ];

        return $payment_options;
    }

    /**
     * Load CSS and JS files
     * @param mixed $params
     */
    public function hookdisplayHeader($params)
    {
        if ('order' === $this->context->controller->php_self) {
            $this->context->controller->addCSS([$this->_path.'views/css/pagoseguro.css']);
            $this->context->controller->addJS([$this->_path.'views/js/pagoseguro.js']);
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
        $externalOption->setCallToActionText($this->l(''))
                       // ->setAction($this->context->link->getModuleLink($this->name, 'validation', [], true))
                       ->setAdditionalInformation($this->context->smarty->fetch('module:pagoseguro/views/templates/hook/hook_payment_detail.tpl'))
                       ->setLogo(Media::getMediaPath(_PS_MODULE_DIR_.$this->name.'views/img/payment_logo.png'));

        return $externalOption;
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