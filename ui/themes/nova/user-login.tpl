<!DOCTYPE html>
<html lang="en" dir="ltr" class="dark">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
     <title>{Lang::T('Login')} - {$_c['CompanyName']}</title>
    <link rel="icon" type="image/png" href="{$_theme}/images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{$_theme}/assets/css/rt-plugins.css">
    <link href="https://unpkg.com/aos@2.3.0/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="">
    <link rel="stylesheet" href="{$_theme}/assets/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.1/dist/sweetalert2.all.min.js"></script>
    <!-- START : Theme Config js-->
    <script src="{$_theme}/assets/js/settings.js" sync></script>
    <!-- END : Theme Config js-->
  </head>
  <body class=" font-inter skin-default">
    {if isset($notify)}
    <script>
        // Display SweetAlert toast notification
        Swal.fire({
            icon: '{if $notify_t == "s"}success{else}error{/if}',
            title: '{$notify}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    </script>
{/if}
    <div class="loginwrapper bg-cover bg-no-repeat bg-center" style="background-image: url({$_theme}/images/bg.png);">
      <div class="lg-inner-column">
        <div class="left-columns lg:w-1/2 lg:block hidden">
          <div class="logo-box-3">
            <a class="flex items-center" href="./">
              <img src="{$_theme}/images/logo.png" class="black_logo" alt="logo">
              <img src="{$_theme}/images/logo.png" class="white_logo" alt="logo">
              <span class="ltr:ml-3 rtl:mr-3 text-xl font-Inter font-bold text-slate-900 dark:text-white">{$_c['CompanyName']}</span>
            </a>
          </div>
        </div>
        <div class="lg:w-1/2 w-full flex flex-col items-center justify-center">
          <div class="auth-box-3">
            <div class="mobile-logo text-center mb-6 lg:hidden block">
              <a class="flex items-center" href="./">
                <img src="{$_theme}/images/logo.png" class="black_logo" alt="logo">
                <img src="{$_theme}/images/logo.png" class="white_logo" alt="logo">
                <span class="ltr:ml-3 rtl:mr-3 text-xl font-Inter font-bold text-slate-900 dark:text-white">{$_c['CompanyName']}</span>
              </a>
            </div>
            <div class="text-center 2xl:mb-10 mb-5">
              <h4 class="font-medium">Sign In</h4>
              <div class="text-slate-500 dark:text-slate-400 text-base"> {Lang::T('Log in to Member Panel')} </div>
            </div>
            <!-- BEGIN: Login Form -->
            <form class="space-y-4" action="{$_url}login/post" method="post">
<form class="space-y-4" action="{$_url}login/post" method="post">
  <div class="fromGroup">
    <label class="block capitalize form-label">{Lang::T('Username')}</label>
    <div class="relative ">
<input type="text" name="username" class="form-control py-2" required value="{$username|default:''}" placeholder="{Lang::T('username')}">
    </div>
  </div>
  <div class="fromGroup">
    <label class="block capitalize form-label">{Lang::T('Password')}</label>
    <div class="relative ">
      <input type="password" name="password" class="form-control py-2" required value="{$password|default:''}" placeholder="{Lang::T('Password')}">
    </div>
  </div>
              <div class="flex justify-between">
                <label class="flex items-center cursor-pointer">
                  <input type="checkbox" class="hiddens">
                  <span class="text-slate-500 dark:text-slate-400 text-sm leading-6 capitalize">Keep me signed in</span>
                </label>
                <a class="text-sm text-slate-800 dark:text-slate-400 leading-6 font-medium" href="">Forgot Password? </a>
              </div>
              <button type="submit" class="btn btn-dark block w-full text-center">{Lang::T('Login')}</button>
            </form>
            <!-- END: Login Form -->
            <div class="mx-auto font-normal text-slate-500 dark:text-slate-400 2xl:mt-12 mt-6 uppercase text-sm text-center"> Don't have Account? <a href="{$_url}register" class="text-slate-900 dark:text-white font-medium hover:underline"> {Lang::T('Register')}</a>
            </div>
          </div>
        </div>
        <div class="auth-footer3 text-white py-5 px-5 text-xl w-full"> &copy; 2023 {$_c['CompanyName']} &nbsp; <a href="./pages/Privacy_Policy.html" target="_blank">Privacy</a> &nbsp;  <a href="./pages/Terms_and_Conditions.html" target="_blank">T &amp; C</a>
        </div>
      </div>
    </div>
    <!-- scripts -->
    <script src="{$_theme}/scripts/vendors.js"></script>
    <script src="{$_theme}/assets/js/jquery-3.6.0.min.js"></script>
    <script src="{$_theme}/assets/js/rt-plugins.js"></script>
    <script src="{$_theme}/assets/js/app.js"></script>

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

  </body>
</html>
