{include file="sections/header.tpl"}

<form class="form-horizontal" method="post" role="form" action="{$_url}plugin/port_tester">
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Testing port external</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Port</label>
                        <div class="col-md-6">
                            <input type="text" id="port" name="port" value="{$port}" placeholder="8728" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            <button class="btn btn-success waves-effect waves-light" type="submit">Test It</button>
                        </div>
                    </div>
                </div>
            </div>
            {if $result != ''}
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">Result</div>
                <div class="panel-body">
                    {Lang::nl2br($result)}
                </div>
            </div>
            {/if}
        </div>
    </div>
</form>

{include file="sections/footer.tpl"}
