<section id="pagoseguro_hook_payment_detail" class="container">
    <div style="min-height: 20px;
    padding: 19px;
    margin-bottom: 20px;
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
    box-shadow: inset 0 1px 1px rgba(0,0,0,.05);">
        <dl>
            <dt>{l s='Amount' mod='pagoseguro'}</dt>
            <dd>{$totalAmount}</dd>
        </dl>
        {l s='PaymentDetailsMessage' mod='pagoseguro'}
    </div>
</section>
