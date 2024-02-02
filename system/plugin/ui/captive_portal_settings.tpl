{include file="sections/header.tpl"}
<section class="content-header">
    <h1>
      <div class="btn-group">
          <button type="button" class="btn btn-success">
              Captive Portal Settings
          </button>
          <button
              type="button"
              class="btn btn-success dropdown-toggle"
              data-toggle="dropdown"
          >
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu" role="menu">
              <li><a href="{$_url}plugin/captive_portal_settings">{Lang::T('General Settings')}</a></li>
              <li>
                  <a href="{$_url}plugin/captive_portal_slider"
                      >{Lang::T('Manage Sliders')}</a
                  >
              </li>
              <li><a href="#">{Lang::T('Manage Advertisements')}</a></li>
              <li><a href="#">{Lang::T('Manage Authorizations')}</a></li>
              <li><a href="#">{Lang::T('Reports')}</a></li>
              <li class="divider"></li>
              <li>
                  <a
                      href="{$_url}plugin/captive_portal_login"
                      target="”_blank”"
                      >Preview Member Landing Page</a
                  >
              </li>
              <li>
                  <a
                      href="{$_url}plugin/captive_portal_download_login"
                      target="”_blank”"
                      > Download Login Page </a
                  >
              </li>
          </ul>
      </div>
  </h1>
    <ol class="breadcrumb">
        <li>
            <a href="{$_url}plugin/captive_portal_overview"><i class="fa fa-dashboard"></i> Captive Portal</a>
        </li>
        <li class="active"> General Settings</li>
    </ol>
</section>

<section class="content">
    <div class="table-responsive">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#tab_1" data-toggle="tab">{Lang::T('General Settings')}</a>
                </li>
                <li>
                    <a href="#tab_2" data-toggle="tab">{Lang::T('Customization')}</a>
                </li>
                <li>
                    <a href="#tab_3" data-toggle="tab">{Lang::T('Slider Settings')}</a>
                </li>
                <li>
                    <a href="#tab_4" data-toggle="tab">{Lang::T('Advertisement Settings')}</a>
                </li>
                <li>
                    <a href="#tab_5" data-toggle="tab">{Lang::T('Trial Authorization Settings')}</a>
                </li>
                <li>
                    <a href="#tab_6" data-toggle="tab">{Lang::T('Pages Settings')}</a>
                </li>

            </ul>
            <div class="tab-content">
                <div style="overflow-x:auto;" class="tab-pane active" id="tab_1">
                    <div class="box-body no-padding" id="">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="">Hotspot Page Title</label>
                                    <input type="text" class="form-control" name="title" id="title" value="{$settings.hotspot_title}" required>
                                    <small class="form-text text-muted">Hotspot Title will be display on Login Page Head Tag</small>
                                </div>
                                <div class="form-group">
                                    <label for="">Hotspot Name</label>
                                    <input type="text" class="form-control" name="name" id="name" value="{$settings.hotspot_name}" required>
                                    <small class="form-text text-muted">Hotspot Name will be display on Login Page Nav Bar if Logo is not available</small>
                                </div>
                                <div class="form-group">
                                    <label for="favicon">Favicon</label>
                                    <input type="file" class="form-control" name="favicon" id="favicon" accept="image/x-icon, image/png, image/jpeg, image/gif" onchange="previewImage('favicon', 'favicon-preview')">
                                    <small class="form-text text-muted">Favicon will be display on Login Page browser tab, its placed in head section</small>
                                    <br>
                                    <img id="favicon-preview" src="{$settings.favicon}" alt="Favicon Preview" style="max-width: 32px; max-height: 32px;">
                                </div>
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" name="logo" id="logo" accept="image/png, image/jpeg, image/svg+xml" onchange="previewImage('logo', 'logo-preview')">
                                    <small class="form-text text-muted">Logo will be display on Login Page Nav Bar section</small>
                                    <br>
                                    <img id="logo-preview" src="{$settings.logo}" alt="Logo Preview" style="max-width: 200px; max-height: 200px;">
                                </div>
                                <div class="form-group">
                                    <label class="">{Lang::T('Allow Free Trial')}</label>
                                    <div class="form-group">
                                        <select name="trial" id="trial" class="form-control">
                                            <option value="no" {if {$settings.hotspot_trial}=='no' }selected="selected" {/if}>No
                                            </option>
                                            <option value="yes" {if {$settings.hotspot_trial}=='yes' }selected="selected" {/if}>Yes
                                            </option>
                                        </select>
                                        <small class="form-text text-muted"><ul>
                            <li>Choose No if you dont want to allow Free Trial </li>
                            <li>Make sure you enable free trial in Mikrotik Router</li>
                             <li>free trial button won't display
                          on captive portal preview, but will work if you connect from hotspot</li>
                        </ul></small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="">{Lang::T('Allow Member Login')}</label>
                                    <div class="form-group">
                                        <select name="member" id="member" class="form-control">
                                            <option value="no" {if {$settings.hotspot_member}=='no' }selected="selected" {/if}>No
                                            </option>
                                            <option value="yes" {if {$settings.hotspot_member}=='yes' }selected="selected" {/if}>Yes
                                            </option>
                                        </select>
                                        <small class="form-text text-muted">Choose No If you want to disable Member Login</small>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <a href="{$_url}plugin/captive_portal_overview" class="btn btn-default">Cancel</a>
                                <button type="submit" class="btn btn-info pull-right">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- /.tab-pane -->
                <div class="tab-pane" style="overflow-x:auto;" id="tab_2">
                    <div class="box-body no-padding" id="">
                        This feature will be available on Pro Version
                    </div>
                </div>

                <!-- /.tab-pane -->
                <div style="overflow-x:auto;" class="tab-pane" id="tab_3">
                    <div class="box-body no-padding" id="">
                        This feature will be available on Pro Version
                    </div>
                </div>
                <div style="overflow-x:auto;" class="tab-pane" id="tab_4">
                    <div class="box-body no-padding" id="">
                        This feature will be available on Pro Version
                    </div>
                </div>
                <div style="overflow-x:auto;" class="tab-pane" id="tab_5">
                    <div class="box-body no-padding" id="">
                        This feature will be available on Pro Version
                    </div>
                </div>
                <div style="overflow-x:auto;" class="tab-pane" id="tab_6">
                    <div class="box-body no-padding" id="">
                        This feature will be available on Pro Version
                    </div>
                </div>
            </div>
        </div>
        <div>
            <pre><b>USAGE:</b>
                
                    <br>Upload your sliders in Slider Setting
                    <br>Go General Settings and setup as per your requirements
                    <br>Then download your the login.html by clicking on download login page
                    <br>Then upload the downloaded login.html file to your mikrotik router
                    <br>Make sure you add your webiste URL in mikrotik hotspot wall garden
                    <br>If your website is https i will suggest you to add certificate to your router
                
            </pre>
        </div>
</section>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        var portalLink = "https://github.com/focuslinkstech";
        $('#version').html('Captive Portal Plugin by: <a href="' + portalLink + '">Focuslinks Tech</a>');
    });
</script>
{include file="sections/footer.tpl"}