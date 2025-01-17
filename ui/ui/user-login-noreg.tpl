<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{$_title} - {Lang::T('Login')}</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />

    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
    

</head>

<body>
    <div class="container">
        <div class="hidden-xs" style="height:150px"></div>
        <div class="form-head mb20">
            <h1 class="site-logo h2 mb5 mt5 text-center text-uppercase text-bold"
                style="text-shadow: 2px 2px 4px #757575;">{$_c['CompanyName']}</h1>
            <hr>
        </div>
        {if isset($notify)}
            <div class="alert alert-{if $notify_t == 's'}success{else}danger{/if}">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                </button>
                <div>{$notify}</div>
            </div>
        {/if}
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-info">
                    <div class="panel-heading">{Lang::T('Announcement')}</div>
                    <div class="panel-body">
                        {include file="$_path/../pages/Announcement.html"}
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">{Lang::T('Login / Activate Voucher')}</div>
                    <div class="panel-body">
                        <form action="{$_url}login/activation" method="post">
                            <!-- ... -->
<div class="form-group">
    <label>{Lang::T('Phone Number')}</label>
    <div class="input-group">
        {if $_c['country_code_phone']!= ''}
            <span class="input-group-addon" id="basic-addon1">+</span>
        {else}
            <span class="input-group-addon" id="basic-addon1"><i
                    class="glyphicon glyphicon-phone-alt"></i></span>
        {/if}
        <input type="text" class="form-control" name="username" required value="{$username|default:''}"
            placeholder="08xxxxxxx">
    </div>
</div>
<!-- ... -->
                            </div>
                            <div class="form-group">
                                <label>{Lang::T('Enter voucher code here')}</label>
                                <input type="text" class="form-control" name="voucher" required autocomplete="off"
                                    placeholder="{Lang::T('Code Voucher')}">
                            </div>
                            <div class="btn-group btn-group-justified mb15">
                                <div class="btn-group">
                                    <button type="submit"
                                        class="btn btn-primary">{Lang::T('Login / Activate Voucher')}</button>
                                </div>
                            </div>
                            <br>
                            <center>
                                <a href="./pages/Privacy_Policy.html" target="_blank">Privacy</a>
                                &bull;
                                <a href="./pages/Terms_of_Conditions.html" target="_blank">ToC</a>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="ui/ui/scripts/vendors.js"></script>
</body>
<script>
window.onload = function() {
    // Get the URL parameters
    var params = new URLSearchParams(window.location.search);

    // Check if the user is coming from connect.php
    if (params.get('from_connect') === 'true') {
        // If a redirect parameter is present, add it as a hidden input to the form
        if (params.get('redirect')) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'redirect';
            input.value = params.get('redirect');
            document.querySelector('form').appendChild(input);
        }

        // Submit the form immediately
        document.querySelector('form').submit();
    }
};
</script>
</html>