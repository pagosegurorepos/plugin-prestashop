<form  class="form-horizontal">
    <div class="panel">
        <div class="panel-heading" style="color: #041E3D; height: 60px;">
            <img style="max-width:50px; display: inline-block;" src="/prestashop/modules/pagoseguro/logo.png">
            <h3 style="display: contents;">{l s='Configuration' mod='pagoseguro'} PAGO SEGURO</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label class="control-label col-sm-3" for="PAGOSEGURO_ACCOUNT_ID">{l s='PAGOSEGURO_ACCOUNT_ID' mod='pagoseguro'}</label>
                 <div class="col-sm-9">
                    <input type="text" class="form-control" id="PAGOSEGURO_ACCOUNT_ID">
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-sm-3" for="PAGOSEGURO_API_KEY">{l s='PAGOSEGURO_API_KEY' mod='pagoseguro'}</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" id="PAGOSEGURO_API_KEY">
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" name="savepagoseguro" class="btn pull-right" style="background-color: #FF0000; color: white;">
                <i class="process-icon-save"></i>
                {l s='Save' mod='pagoseguro'}
            </button>
        </div>
    </div>
</form>