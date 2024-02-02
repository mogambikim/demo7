{include file="sections/header.tpl"}


<div class="row">
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Discussions</h3>
            </div>
            <div class="box-body">Get help from WhatsApp</div>
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="https://chat.whatsapp.com/I8a7YGalCLD5c4QLcpiSvz" target="_blank"
                        class="btn btn-primary btn-sm btn-block"><i class="ion ion-chatboxes"></i> Whatsapp
                        Group</a>
                    <a href="https://t.me/freeispradius" target="_blank" class="btn btn-primary btn-sm btn-block"><i
                            class="ion ion-chatboxes"></i> Telegram Group</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Feedback</h3>
            </div>
            <div class="box-body">
                Feedback and Bug Report
            </div>
            <div class="box-footer">
                <a href="https://chat.whatsapp.com/I8a7YGalCLD5c4QLcpiSvz" target="_blank"
                    class="btn btn-primary btn-sm btn-block"><i class="ion ion-chatboxes"></i> Give Feedback</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Donasi</h3>
            </div>
            <div class="box-body">For better development, donate to Freeispradius, donations will continue to in
                application development</div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td>BCA</td>
                            <td>5410-454-825</td>
                        </tr>
                        <tr>
                            <td>Mandiri</td>
                            <td>163-000-1855-793</td>
                        </tr>
                        <tr>
                            <td>Atas nama</td>
                            <td>freeispradius</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="https://freeispradius.com" target="_blank"
                        class="btn btn-primary btn-sm btn-block">Paypal</a>
                    <a href="https://freeispradius.com" target="_blank"
                        class="btn btn-primary btn-sm btn-block">Credit card</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">cheap Sms Server</h3>
            </div>
            <div class="box-body">
                You can use the sms server with your own projects 
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td>Can be used locally</td>
                            <td>5410-454-825</td>
                        </tr>
                        <tr>
                            <td>Apps/sender ID</td>
                            <td>Websites</td>
                        </tr>
                        <tr>
                            <td>No wait time</td>
                            <td>World Wide</td>
                        </tr>
                        <tr>
                            <td>Account Name</td>
                            <td>freeispradius</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a href="https://sms.freeispradius.com" target="_blank" class="btn btn-primary btn-sm btn-block">Server</a>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Chat with me</h3>
            </div>
            <div class="box-body">$2 Paid Support<br>donation confirmation?<br>Or ask any Donation Alternative</div>
            <div class="box-footer">
                <a href="https://t.me/freeispradius" target="_blank" class="btn btn-primary btn-sm btn-block">Telegram</a>
            </div>
        </div>
        <div class="box box-primary box-hovered mb20 activities">
            <div class="box-header">
                <h3 class="box-title">Free WhatsApp Gateway and Telegram Bot creater</h3>
            </div>
            <div class="box-body">
                There is a Telegram bot wizard in here
            </div>
            <div class="box-footer">
                <a href="https://whatsapp.freeispradius.com" target="_blank"
                    class="btn btn-primary btn-sm btn-block">whatsapp.freeispradius.com</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6" id="update">
        <div class="box box-primary box-hovered mb20 activities">
            <div class="box-header">
                <h3 class="box-title">FREEISPRADIUS</h3>
            </div>
            <div class="box-body">
                {$_L['Welcome_Text_Admin']}
            </div>
           
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="https://freeispradius.com/CHANGELOG.md" target="_blank" class="btn btn-default btn-sm btn-block">Current
                        Changelog</a>
                    <a href="https://freeispradius.com/CHANGELOG.md" target="_blank"
                        class="btn btn-default btn-sm btn-block">Repo Changelog</a>
                </div>
            </div>
           
        </div>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        $.getJSON("./version.json?" + Math.random(), function(data) {
            $('#currentVersion').html('Current Version: ' + data.version);
        });
        $.getJSON("freeispradius.com" + Math
            .random(),
            function(data) {
                $('#latestVersion').html('Latest Version: ' + data.version);
            });
    });
</script>
{include file="sections/footer.tpl"}