{include file="sections/header.tpl"}
<section class="content-header">
    <h1>
      <div class="btn-group">
          <button type="button" class="btn btn-success">
              Hotspot Settings
          </button>
          <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
              <span class="caret"></span>
              <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu" role="menu">
              <li><a href="{$_url}plugin/hotspot_settings">{Lang::T('General Settings')}</a></li>
              <li class="divider"></li>
              <li><a href="{$_url}plugin/captive_portal_login" target="_blank">Preview Hotspot Login Page</a></li>
              <li><a href="{$app_url}/system/plugin/download.php?download=1" target="_blank">Download Login Page</a></li>
          </ul>
      </div>
  </h1>
    <ol class="breadcrumb">
        <li><a href="{$app_url}/system/plugin/download.php?download=1"><i class="fa fa-dashboard"></i> Click Here To Download Login Page</a></li>
        <li class="active">Hotspot Settings</li>
    </ol>
</section>

<section class="content">
    <div class="table-responsive">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">{Lang::T('General Settings')}</a></li>
                
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1" style="overflow-x:auto;">
                    <div class="box-body no-padding" id="">
                        <form method="POST" action="" enctype="multipart/form-data">
                         <div class="box-body">

                               <div class="form-group">
    <label for="hotspot_title">Hotspot Page Title</label>
    <input type="text" class="form-control" name="hotspot_title" id="hotspot_title" value="{$hotspot_title}" required>
    <small class="form-text text-muted">In this field, you can enter the name of your ISP company. It will appear as the main title on the hotspot page.</small>
</div>


                                 <div class="form-group">
                                    <label for="description">Brief Description Of Company/Tagline</label>
                                    <input type="text" class="form-control" name="description" id="description" value="{$description}" required>
                                </div>


<div class="form-group">
    <label for="router_name">Router Name:</label>
    <input type="text" class="form-control" name="router_name" id="router_name" value="{$router_name}" required>
    <small class="form-text text-muted">This is the most important part of the form. Go to Network and then Routers, and copy the exact router name.</small>
</div>




<!-- FAQ fields -->
<div class="form-group">
    <label for="frequently_asked_questions_headline1">FAQ Headline 1</label>
    <input type="text" class="form-control" name="frequently_asked_questions_headline1" id="frequently_asked_questions_headline1" value="{$frequently_asked_questions_headline1}" required>
</div>

<div class="form-group">
    <label for="frequently_asked_questions_answer1">FAQ Answer 1</label>
    <textarea class="form-control" id="frequently_asked_questions_answer1" name="frequently_asked_questions_answer1" required>{$frequently_asked_questions_answer1}</textarea>
</div>

<div class="form-group">
    <label for="frequently_asked_questions_headline2">FAQ Headline 2</label>
    <input type="text" class="form-control" id="frequently_asked_questions_headline2" name="frequently_asked_questions_headline2" value="{$frequently_asked_questions_headline2}" required>
</div>

<div class="form-group">
    <label for="frequently_asked_questions_answer2">FAQ Answer 2</label>
    <textarea class="form-control" id="frequently_asked_questions_answer2" name="frequently_asked_questions_answer2" required>{$frequently_asked_questions_answer2}</textarea>
</div>


<div class="form-group">
    <label for="frequently_asked_questions_headline3">FAQ Headline 3</label>
    <input type="text" class="form-control" name="frequently_asked_questions_headline3" id="frequently_asked_questions_headline3" value="{$frequently_asked_questions_headline3}" required>
</div>
<div class="form-group">
    <label for="frequently_asked_questions_answer3">FAQ Answer 3</label>
    <textarea class="form-control" id="frequently_asked_questions_answer3" name="frequently_asked_questions_answer3" required>{$frequently_asked_questions_answer3}</textarea>
</div>


<!-- Save Changes button -->

    <button type="submit" class="btn btn-info pull-right">Save Changes</button>
</form>

                <div class="tab-pane" id="tab_6" style="overflow-x:auto;">
                    <div class="box-body no-padding" id="">
                        <!-- Content for Pages Settings tab -->
                        This feature will be Coming Soon
                    </div>
                </div>
            </div>
        </div>
        <div>
            <pre><b>USAGE:</b>
                <br>Make sure you change this custom Settings and personalize them.
                <br>Then download the login.html by clicking on download login page.
                <br>Then upload the downloaded login.html file to your mikrotik router.
                <br>Make sure you add your website URL in mikrotik hotspot wall garden.
            </pre>
        </div>
</section>

{include file="sections/footer.tpl"}
