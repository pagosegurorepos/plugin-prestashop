<form id="configuration_form" class="defaultForm form-horizontal pagoseguro" action="index.php?controller=AdminModules&amp;configure=pagoseguro&amp;token=6208caa83a7464fc77be99b0cb27a738" method="post" enctype="multipart/form-data" novalidate="">
    <input type="hidden" name="submitpagoseguro" value="1">
    <div class="panel">
        <div class="panel-heading" style="color: #041E3D; height: 60px;">
            <img style="max-width:50px; display: inline-block;" src="/prestashop/modules/pagoseguro/logo.png">
            <h3 style="display: contents;">{l s='Configuration' mod='pagoseguro'} PAGO SEGURO</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label col-sm-3" for="PAGOSEGURO_ACCOUNT_ID">{l s='PAGOSEGURO_ACCOUNT_ID' mod='pagoseguro'}</label>
                 <div class="col-sm-9">
                    <input type="text" class="form-control" name="PAGOSEGURO_ACCOUNT_ID" id="PAGOSEGURO_ACCOUNT_ID" value="{$PAGOSEGURO_ACCOUNT_ID}" required>
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3" for="PAGOSEGURO_API_KEY">{l s='PAGOSEGURO_API_KEY' mod='pagoseguro'}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="PAGOSEGURO_API_KEY" id="PAGOSEGURO_API_KEY" value="{$PAGOSEGURO_API_KEY}">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1" id="configuration_form_submit_btn" name="submitpagoseguro" class="btn pull-right" style="background-color: #FF0000; color: white;">
                <i class="process-icon-save"></i>
                {l s='Save' mod='pagoseguro'}
            </button>								
        </div>
    </div>
</form>