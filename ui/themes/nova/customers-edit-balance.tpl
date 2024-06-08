{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel-primary">
            <div class="panel-heading">{Lang::T('Edit Balance')}</div>
            <div class="panel-body">
                <form method="post" action="{$_url}customers/edit-balance/{$customer['id']}">
                    <div class="form-group">
                        <label for="balance">{Lang::T('Balance')}</label>
                        <input type="text" class="form-control" id="balance" name="balance" value="{$customer['balance']}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">{Lang::T('Save Changes')}</button>
                    <a href="{$_url}customers/list" class="btn btn-default">{Lang::T('Cancel')}</a>
                </form>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}