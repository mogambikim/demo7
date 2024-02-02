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
        <li class="active">Overview</li>
    </ol>
</section>
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>150</h3>

                    <p>ADS VIEWS</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="#" class="small-box-footer">available on Pro Version <i class="fa fa-arrow-circle-right"></i
                ></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px"></sup></h3>

                    <p>BANNER VIEWS</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="#" class="small-box-footer">available on Pro Version <i class="fa fa-arrow-circle-right"></i
                ></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>44</h3>

                    <p>Social Wi-Fi Login</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="#" class="small-box-footer">available on Pro Version <i class="fa fa-arrow-circle-right"></i
                ></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>65</h3>

                    <p>Unique Visitors</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">available on Pro Version <i class="fa fa-arrow-circle-right"></i
                ></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable"></section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable"></section>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var portalLink = "https://giccthcuccb.com/freeispradius";
            $('#version').html('Captive Portal Plugin by: <a href="' + portalLink + '">Focus</a>');
        });
    </script>
    {include file="sections/footer.tpl"}
</section>