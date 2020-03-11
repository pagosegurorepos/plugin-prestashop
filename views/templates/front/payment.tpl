{extends file="page.tpl"}

{block name="content"}
    <div class="col-lg-12" style="display:none;">
        <form id="pagoseguropayment"    method="post"      action="{$URL}">
            <input type="hidden"        name="key"         value="{$KEY}" />
            <input type="hidden"        name="txnid"       value="{$ORDER_ID}" />
            <input type="hidden"        name="amount"      value="{$AMOUNT}" />
            <input type="hidden"        name="productinfo" value="{$PRODUCT}" />
            <input type="hidden"        name="firstname"   value="{$CUSTOMER}" />
            <input type="hidden"        name="email"       value="{$CUSTOMER_EMAIL}" />
            <input type="hidden"        name="hash"        value="{$SIGNATURE}" />
            <input type="hidden"        name="udf1"        value="/payment/process" />
            <input type="submit"        name="Submit"       type="hidden" value="Enviar" >
	    </form>

        <p style="text-align: center; font-size: 20px; color: #041E3D; font-family: Arial; font-weight: 600; margin: 20px;">
            {l s="PagoSeguro" mod='pagoseguro'}
        </p>
    </div>
    <script>
		document.getElementById("pagoseguropayment").submit();
	</script>
{/block}