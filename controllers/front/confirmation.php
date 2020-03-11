<?php

class PagoSeguroConfirmationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (isset($_REQUEST['orderReference']) &&
            isset($_REQUEST['value']) &&
            isset($_REQUEST['product']) &&
            isset($_REQUEST['customerFullName']) &&
            isset($_REQUEST['customerEmail']) &&
            isset($_REQUEST['status']) &&
            isset($_REQUEST['sign'])
        ) {
            $total                       = $_REQUEST['value'];
            $customerFullName            = $_REQUEST['customerFullName'];
            $customerEmail               = $_REQUEST['customerEmail'];
            $orderReference              = $_REQUEST['orderReference'];
            $product                     = $_REQUEST['product'];
            $status 			                  = $_REQUEST['status'];
            $sign 			                    = $_REQUEST['sign'];
            $accountId                   = Configuration::get('PAGOSEGURO_ACCOUNT_ID');
            $apiKey                      = Configuration::get('PAGOSEGURO_API_KEY');

            $stringSignature            = $accountId.'|'.$orderReference.'|'.$total.'|'.$product.'|'.$customerFullName.'|'.$customerEmail.'|'.'/payment/process||||||||||'.$apiKey;
            $signature                  = hash('sha512', $stringSignature);
            $estadoTxt                  = 'Transacción en Proceso';

            if ($signature === $sign) {
                switch ($status) {
                    case 17:
                        $estadoTxt = 'Transacción Rechazada';
                        break;
                    case 22:
                        $estadoTxt = 'Transacción Autorizada';
                        break;
                    default:
                        $estadoTxt = 'Transacción en Proceso';
                }
            }

            $sql     = 'SELECT * FROM '._DB_PREFIX_.'orders  WHERE `reference` LIKE "'.$orderReference.'"';
            $orderId = Db::getInstance()->getValue($sql);

            if (false != $orderId) {
                $history           = new OrderHistory();
                $history->id_order = (int) $orderId;
                $history->changeIdOrderState((int) $estadoTxt, (int) ($orderId));
                // $history->add(true); // No send email
                $history->addWithemail(true); // Send email
            }
        }

        $this->setTemplate('module:pagoseguro/views/templates/front/confirmation.tpl');
    }
}