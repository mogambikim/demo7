<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recharge Account</title>

    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

    {include file="sections/header.tpl"}

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading">{Lang::T('Recharge Account')}</div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post" role="form" action="{$_url}prepaid/recharge-post" >
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Select Account')}</label>
                            <div class="col-md-6">
                                <select id="personSelect" class="form-control select2" name="id_customer" style="width: 100%" data-placeholder="Select a customer...">
                                    <option></option>
                                    {foreach $c as $cs}
                                        {if $id eq $cs['id']}
                                            <option value="{$cs['id']}" selected>{$cs['username']}</option>
                                        {else}
                                            <option value="{$cs['id']}">{$cs['username']}</option>
                                        {/if}
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Type')}</label>
                            <div class="col-md-6">
                                <label><input type="radio" id="Hot" name="type" value="Hotspot"> {Lang::T('Hotspot Plans')}</label>
                                <label><input type="radio" id="POE" name="type" value="PPPOE"> {Lang::T('PPPOE Plans')}</label>
                                <label><input type="radio" id="Static" name="type" value="Static"> {Lang::T('Static Ip Plans')}</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Routers')}</label>
                            <div class="col-md-6">
                                <select id="server" name="server" class="form-control select2">
                                    <option value=''>Select Routers</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">{Lang::T('Service Plan')}</label>
                            <div class="col-md-6">
                                <select id="plan" name="plan" class="form-control select2">
                                    <option value=''>Select Plans</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-success" type="submit">{Lang::T('Recharge')}</button>
                                Or <a href="{$_url}customers/list">{Lang::T('Cancel')}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tutorial Modal -->
    <div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="tutorialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tutorialModalLabel">Tutorial Video</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/M91aZf1wrEw?si=f3cxhNtD6wDbMBwz" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function(){
        $('#tutorialModal').modal('show');
    });
    </script>
	

    {include file="sections/footer.tpl"}

</body>
</html>
