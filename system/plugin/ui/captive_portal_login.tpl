<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <title>{$config.hotspot_title} : : Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{$config.favicon}">
    <link rel="stylesheet" type="text/css" href="system/plugin/captive_portal/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="system/plugin/captive_portal/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="system/plugin/captive_portal/css/style.css">
    <link rel="stylesheet" type="text/css" href="system/plugin/captive_portal/css/sweetalert2.min.css">
    <script src="system/plugin/captive_portal/js/popper.min.js"></script>
    <script src="system/plugin/captive_portal/js/jquery.slim.min.js"></script>
    <script src="system/plugin/captive_portal/js/bootstrap.bundle.js"></script>
    <script type="text/javascript" src="system/plugin/captive_portal/js/md5.js"></script>
    <script type="text/javascript" src="system/plugin/captive_portal/js/sweetalert2.all.min.js"></script>
    {if {$error}} && {$error}} != ''}
    <script type="text/javascript">
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: "{$error}",
          showConfirmButton: false,
          timer: 5000
        });
    </script>
    {/if}

</head>

<body>

    {if isset($chapid)}

    <form name="sendin" action="{$linkloginonly}" method="post">
        <input type="hidden" name="username" />
        <input type="hidden" name="password" />
        <input type="hidden" name="dst" value="{$linkorig}" />
        <input type="hidden" name="popup" value="true" />
    </form>
    {literal}
    <script type="text/javascript">
        function doLogin() {
        {if strlen($chapid) < 1}return true;{/if}
        document.sendin.username.value = document.login.username.value;
        document.sendin.password.value = hexMD5('{$chapid}' + document.login.password.value + '{$chapchallenge}');
        document.sendin.submit();
        return false;
        }
    </script>
    {/literal} {/if}
    <nav class="navbar navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
  {if $config.logo}
    <img src="{$config.logo}" alt="Logo" width="100" height="35" class="d-inline-block align-top">
  {else}
    {$config.hotspot_name}
  {/if}
</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            More
          </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="#"> Terms &amp; Conditions</a>
                            <a class="dropdown-item" href="#">Privacy Policy</a>
                            <hr class="dropdown-divider">
                            <a class="dropdown-item" href="#">Contact Us</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <br>
    <br>

    <header id="header">
        <div id="headerCarousel" class="carousel slide carousel-fade" data-ride="carousel">
            <!-- Slide Indicators -->
            <ol class="carousel-indicators">
                {foreach $sliderData as $key => $slide}
                <li data-target="#headerCarousel" data-slide-to="{$key}" {if $key==0 }class="active" {/if}></li>
                {/foreach}
            </ol>

            <div class="carousel-inner" role="listbox">
                {foreach $slides as $key => $slide}
                <div class="carousel-item {if $key == 0}active{/if}">
                    <div class="carousel-background"><img src="{$slide.image}" alt=""></div>
                    <div class="carousel-container">
                        <div class="carousel-content">
                            <h2>{$slide.title}</h2>
                            <p>{$slide.description}</p>
                            {if $slide.button}
                            <a href="{$slide.link}" class="contactus-btn">{$slide.button}</a> {/if}
                        </div>
                    </div>
                </div>
                {/foreach}
            </div>

            <!-- Carousel pre and next arrow -->
            <a class="carousel-control-prev" href="#headerCarousel" role="button" data-slide="prev">
                <i class="fa fa-chevron-left"></i>
            </a>
            <a class="carousel-control-next" href="#headerCarousel" role="button" data-slide="next">
                <i class="fa fa-chevron-right"></i>
            </a>
        </div>
    </header>

    <div class="footer">
        {if {$config.hotspot_member} == 'yes'}
        <a href="#Login" data-toggle="modal" data-target="#login" class="btn btn-primary me-3">Member</a> {/if} {if {$config.hotspot_trial} == 'yes' && {$trial} =='yes' }
        <a href="{$linkloginonly}?dst={$linkorigesc}&username=T-{$macesc}" class="btn btn-success me-3">Free Trial</a>
        <!--<a href="{$_url}plugin/captive_portal_login_trial" class="btn btn-success me-3">Free Trial</a> -->
        {/if}
    </div>


    <div class="bs-example">
        <!-- Modal HTML -->
        <div id="login" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div style="overflow-x:auto;" class="modal-content">
                    <div style="overflow-x:auto;" class="modal-header">
                        <h5 class="modal-title">MEMBER LOGIN</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        {if {$error}}
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>oops!</strong> {$error}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div> {/if}
                        <form name="login" action="{$linkloginonly}" method="post" {if isset($chapid)} onSubmit="return doLogin()" {/if}>
                            <input type="hidden" name="dst" value="http://google.com">
                            <input type="hidden" name="popup" value="true">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Enter your username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <br>
                        <div class="text-center"><span class="text-muted">Don't have an account?</span> <a href="{$_url}/index.php?_route=login">Sign up here</a></div>
                    </div>
                    <div class="">
                        <div class="text-center p-t-136">
                            <hr> Powered by:
                            <a class="txt2" href="./"> {$config.hotspot_name} </a>
                            <br> All Rights Reserved
                            <br> &copy; {$smarty.now|date_format:"%Y"}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        document.login.username.focus();
    </script>
</body>

</html>