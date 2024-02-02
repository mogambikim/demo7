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
        <li><a href="{$_url}plugin/captive_portal_overview"><i class="fa fa-dashboard"></i> Captive Portal</a></li>
        <li class="active">Sliders</li>
    </ol>
</section>
<br>
<div class="tab-pane" style="overflow-x:auto;">
    <div class="box-body no-padding" id="hotspot-panel">
        <div class="col-sm-12">
            <div class="panel panel-hovered mb20 panel-primary">
                <div class="panel-heading">{Lang::T('Captive Portal Slider')}</div>
                <div class="panel-body">
                    <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary btn-block waves-effect" type="submit" name="createSlider" data-toggle="modal" data-target="#sliderAdd" value="">Add New Slider</button>
                        </div>&nbsp;
                    </div>
                    <br>
                    <div class="table-responsive"> {if empty($slides)}
                        <p align="center"><b>{Lang::T('Slider not found.')}</b></p> {else}
                        <table class="table table-bordered table-striped table-condensed">
                            <thead>
                                <tr>
                                    <th>{Lang::T('Image')}</th>
                                    <th>{Lang::T('Title')}</th>
                                    <th>{Lang::T('Description')}</th>
                                    <th>{Lang::T('Link')}</th>
                                    <th>{Lang::T('Button')}</th>
                                    <th>{Lang::T('Action')}</th>
                                </tr>
                            </thead>
                            <tbody> {foreach $slides as $index => $slide}
                                <tr>
                                    <td><img src="{$slide.thumbnail}" alt="Slide Thumbnail"></td>
                                    <td>{$slide.title}</td>
                                    <td>{$slide.description}</td>
                                    <td><a href="{$slide.link}">{$slide.link}</a></td>
                                    <td>{$slide.button}</td>
                                    <td align="center">
                                        <button style="margin: 0px;" class="btn btn-success btn-xs" data-toggle="modal" data-target="#sliderEdit{$index}" value="">{Lang::T('Edit')}</button>
                                        <div class="modal fade" id="sliderEdit{$index}" tabindex="-1" role="dialog" aria-labelledby="sliderEditLabel{$index}">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="sliderEditLabel{$index}">{Lang::T('Edit Slider')}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <!-- Edit Form -->
                                                        <form action="{$_url}plugin/captive_portal_slider_edit&slideIndex={$index}" method="post" enctype="multipart/form-data">
                                                            <input type="hidden" name="slideIndex" value="{$index}">
                                                            <div class="form-group">
                                                                <label for="editTitle{$index}">{Lang::T('Title')}</label>
                                                                <input type="text" class="form-control" id="editTitle{$index}" name="title" value="{$slide.title}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="editDescription{$index}">{Lang::T('Description')}</label>
                                                                <textarea class="form-control" id="editDescription{$index}" name="description">{$slide.description}</textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="editLink{$index}">{Lang::T('Link')}</label>
                                                                <input type="text" class="form-control" id="editLink{$index}" name="link" value="{$slide.link}">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="editButton{$index}">{Lang::T('Button')}</label>
                                                                <input type="text" class="form-control" id="editButton{$index}" name="button" value="{$slide.button}">
                                                            </div>
                                                            <button type="submit" class="btn btn-primary" name="editSlide">{Lang::T('Save Changes')}</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#deleteSlideModal" data-slide-index="{{$index}}">Delete</a>
                                    </td>
                                </tr> {/foreach} {/if} </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteSlideModal" tabindex="-1" role="dialog" aria-labelledby="deleteSlideModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteSlideModalLabel">Delete Slider</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{Lang::T('Are you Sure you want to Delete this Slider?')}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="{$_url}plugin/captive_portal_slider_delete&slideIndex={$index}" class="btn btn-danger">{Lang::T('Delete')}</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sliderAdd">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add New Slider</h4>
            </div>
            <form method="post" action="{$_url}plugin/captive_portal_slider" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" name="image" id="image" required>
                        <p class="help-block">Image</p>
                    </div>
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Title">
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea type="text" name="description" class="form-control" id="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="link">Link:</label>
                        <input type="text" name="link" class="form-control" id="link">
                    </div>
                    <div class="form-group">
                        <label for="button">Button:</label>
                        <input type="text" name="button" class="form-control" id="button">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Slider</button>
                </div>
        </div>
        </form>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var portalLink = "https://github.com/focuslinkstech";
        $('#version').html('Captive Portal Plugin by: <a href="' + portalLink + '">Focuslinks Tech</a>');
    });
</script>
{include file="sections/footer.tpl"}