{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">
                <span>{Lang::T('Extend Plan')}</span>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" method="post" action="{$_url}prepaid/extend-post">
                    <input type="hidden" name="id" value="{$d.id}">
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Username')}</label>
                        <div class="col-md-6">
                            <input type="text" name="username" class="form-control" value="{$d.username}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">{Lang::T('Extension Days')}</label>
                        <div class="col-md-6">
                            <input type="number" name="extension_days" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-2 col-md-6">
                            <button type="submit" class="btn btn-primary">{Lang::T('Extend')}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
