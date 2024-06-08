{include file="sections/header.tpl"}

<section class="content-header">
    <h1>
        <div class="btn-group">
            <button type="button" class="btn btn-success btn-lg">
                <i class="fa fa-wifi"></i> Hotspot Settings
            </button>
            <button type="button" class="btn btn-success btn-lg dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="{$_url}plugin/hotspot_settings"><i class="fa fa-cog"></i> {Lang::T('General Settings')}</a></li>
                <li class="divider"></li>
                <li><a href="{$_url}plugin/captive_portal_login" target="_blank"><i class="fa fa-eye"></i> Preview Hotspot Login Page</a></li>
                <li><a href="{$app_url}/system/plugin/download.php?download=1" target="_blank"><i class="fa fa-download"></i> Download Login Page</a></li>
            </ul>
        </div>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{$app_url}/system/plugin/download.php?download=1" class="btn btn-info btn-lg"><i class="fa fa-download"></i> Click Here To Download Login Page</a></li>
        <li class="active">Hotspot Settings</li>
    </ol>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary box-solid">
                <div class="box-header with-border" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3 class="box-title"><i class="fa fa-cog"></i> {Lang::T('General Settings')}</h3>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#tutorialModal" style="margin-left: auto;">
                        {Lang::T('Need Help? Watch Guide Here')}
                    </button>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <form method="POST" action="" enctype="multipart/form-data" class="form-horizontal">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="hotspot_title" class="col-sm-2 control-label"><i class="fa fa-header"></i> Hotspot Page Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-lg" name="hotspot_title" id="hotspot_title" value="{$hotspot_title}" required>
                                <small class="form-text text-muted">In this field, you can enter the name of your ISP company. It will appear as the main title on the hotspot page.</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description" class="col-sm-2 control-label"><i class="fa fa-info-circle"></i> Brief Description Of Company/Tagline</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-lg" name="description" id="description" value="{$description}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="router_id" class="col-sm-2 control-label"><i class="fa fa-wifi"></i> Router:</label>
                            <div class="col-sm-10">
                                <select name="router_id" id="router_id" class="form-control input-lg">
                                    <option value="">Select a router</option>
                                    {foreach $routers as $router}
                                        <option value="{$router.id}" {if $router.id eq $selected_router_id}selected{/if}>{$router.name}</option>
                                    {/foreach}
                                </select>
                                <small class="form-text text-muted">This is the most important part of the form. Select the router from the dropdown list.</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequently_asked_questions_headline1" class="col-sm-4 control-label"><i class="fa fa-question-circle"></i> FAQ Headline 1</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-lg" name="frequently_asked_questions_headline1" id="frequently_asked_questions_headline1" value="{$frequently_asked_questions_headline1}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="frequently_asked_questions_answer1" class="col-sm-4 control-label"><i class="fa fa-comment"></i> FAQ Answer 1</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-lg" id="frequently_asked_questions_answer1" name="frequently_asked_questions_answer1" rows="4" required>{$frequently_asked_questions_answer1}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequently_asked_questions_headline2" class="col-sm-4 control-label"><i class="fa fa-question-circle"></i> FAQ Headline 2</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-lg" id="frequently_asked_questions_headline2" name="frequently_asked_questions_headline2" value="{$frequently_asked_questions_headline2}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="frequently_asked_questions_answer2" class="col-sm-4 control-label"><i class="fa fa-comment"></i> FAQ Answer 2</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-lg" id="frequently_asked_questions_answer2" name="frequently_asked_questions_answer2" rows="4" required>{$frequently_asked_questions_answer2}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="frequently_asked_questions_headline3" class="col-sm-4 control-label"><i class="fa fa-question-circle"></i> FAQ Headline 3</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control input-lg" name="frequently_asked_questions_headline3" id="frequently_asked_questions_headline3" value="{$frequently_asked_questions_headline3}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="frequently_asked_questions_answer3" class="col-sm-4 control-label"><i class="fa fa-comment"></i> FAQ Answer 3</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control input-lg" id="frequently_asked_questions_answer3" name="frequently_asked_questions_answer3" rows="4" required>{$frequently_asked_questions_answer3}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color_scheme" class="col-sm-4 control-label"><i class="fa fa-paint-brush"></i> Color Scheme:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control input-lg select2" name="color_scheme" id="color_scheme" data-placeholder="Select a color scheme" style="width: 100%;">
                                            <option value="green" {if $selected_color_scheme == 'green'}selected{/if}>Green</option>
                                            <option value="brown" {if $selected_color_scheme == 'brown'}selected{/if}>Brown</option>
                                            <option value="orange" {if $selected_color_scheme == 'orange'}selected{/if}>Orange</option>
                                            <option value="red" {if $selected_color_scheme == 'red'}selected{/if}>Red</option>
                                            <option value="blue" {if $selected_color_scheme == 'blue'}selected{/if}>Blue</option>
                                            <option value="black" {if $selected_color_scheme == 'black'}selected{/if}>Black</option>
                                            <option value="yellow" {if $selected_color_scheme == 'yellow'}selected{/if}>Yellow</option>
                                            <option value="pink" {if $selected_color_scheme == 'pink'}selected{/if}>Pink</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-lg pull-right"><i class="fa fa-save"></i> Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-info-circle"></i> Usage Instructions</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="callout callout-info">
                        <h4><i class="icon fa fa-primary"></i> Steps:</h4>
                        <ol>
                            <li>Make sure you change these custom settings and personalize them.</li>
                            <li>Download the <code>login.html</code> file by clicking on the "Download Login Page" button.</li>
                            <li>Upload the downloaded <code>login.html</code> file to your MikroTik router.</li>
                            <li>The login file should be named exactly login.html, if the name is different kindly rename.</li>
                            <li>Drag and drop the login file anywhere between error.html and status.html. This part is very important.</li>
                            <li>Add your website URL to the MikroTik hotspot walled garden.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/d1X8NrQodU4?si=mAUdMH7aIlbg9eva" allowfullscreen></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}
