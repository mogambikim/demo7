{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}paymentgateway/tinypesa" >
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">TinyPesa</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Api Key</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="apikey" name="apikey" placeholder="xxxxxxxxxxxxxxxxx" value="{$_c['tinypesa_api_key']}">
                            <small class="form-text text-muted"><a href="https://tinypesa.com/" target="_blank">https://tinypesa.com/</a></small>
                        </div>
                    </div>

                     <div class="form-group">
                        <label class="col-md-2 control-label">Webhook Url</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="webhook"  value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-primary" type="submit">{Lang::T('Save Changes')}</button>
                        </div>
                    </div>
                        <pre>/ip hotspot walled-garden
                   add dst-host=tinypesa.com
                   add dst-host=*.tinypesa.com</pre>
                </div>
            </div>

        </div>
    </div>
</form>

<script>
let input = document.getElementById('webhook');
var fullURL = window.location.href;

input.value = "https://"+fullURL.split('/')[2]+"/api/tinypesacallback";
</script>

{include file="sections/footer.tpl"}