<!DOCTYPE html>
<html lang="en" dir="ltr" class="dark">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <title>{Lang::T('Register')} - {$_c['CompanyName']}</title>
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
        <link rel="stylesheet" href="ui/ui/styles/sweetalert2.min.css" />
    <script src="ui/ui/scripts/sweetalert2.all.min.js"></script>
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
              <h4 class="font-medium">{Lang::T('Register Member')}</h4>
              <div class="text-slate-500 dark:text-slate-400 text-base">
                <small></small>
              </div>
            </div> {if isset($notify)} <div> {$notify} <br>
            </div>{/if}
            <!-- BEGIN: Login Form -->
            <div class="wizard card">
              <div class="card-body p-6">
                <div class="wizard-steps flex z-[5] items-center relative justify-center md:mx-8">
                  <div class="  active pass  relative z-[1] items-center item flex flex-start flex-1
                                  last:flex-none group wizard-step" data-step="1">
                    <div class="number-box">
                      <span class="number"> 1 </span>
                      <span class="no-icon text-3xl">
                        <iconify-icon icon="bx:check-double"></iconify-icon>
                      </span>
                    </div>
                    <div class="bar-line"></div>
                    <div class="circle-box">
                      <span class="w-max">Account</span>
                    </div>
                  </div>
                  <div class="  relative z-[1] items-center item flex flex-start flex-1
                                  last:flex-none group wizard-step" data-step="2">
                    <div class="number-box">
                      <span class="number"> 2 </span>
                      <span class="no-icon text-3xl">
                        <iconify-icon icon="bx:check-double"></iconify-icon>
                      </span>
                    </div>
                    <div class="bar-line"></div>
                    <div class="circle-box">
                      <span class="w-max">{Lang::T('Password')}</span>
                    </div>
                  </div>
                  <div class="  relative z-[1] items-center item flex flex-start flex-1
                                  last:flex-none group wizard-step" data-step="3">
                    <div class="number-box">
                      <span class="number"> 3 </span>
                      <span class="no-icon text-3xl">
                        <iconify-icon icon="bx:check-double"></iconify-icon>
                      </span>
                    </div>
                    <div class="bar-line"></div>
                    <div class="circle-box">
                      <span class="w-max">Address</span>
                    </div>
                  </div>
                  <div class="relative z-[1] items-center item flex flex-start flex-1
                                  last:flex-none group wizard-step" data-step="4">
                    <div class="number-box">
                      <span class="number"> 4 </span>
                      <span class="no-icon text-3xl">
                        <iconify-icon icon="bx:check-double"></iconify-icon>
                      </span>
                    </div>
                    <div class="bar-line"></div>
                    <div class="circle-box">
                      <span class="w-max">Done</span>
                    </div>
                  </div>
                </div>
                <form class="wizard-form mt-10" action="{$_url}register/post" method="post">
                  <div class="wizard-form-step active" data-step="1">
                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
                      <div class="lg:col-span-3 md:col-span-2 col-span-1">
                        <h4 class="text-base text-slate-800 dark:text-slate-300 my-6">Enter Your Account Details</h4>
                      </div>
                      <div class="input-area">
                        <label for="" class="form-label">{Lang::T('Phone Number')}*</label>
                        <input name="username" type="text" class="form-control" placeholder="{if $_c['country_code_phone']!= ''}{$_c['country_code_phone']}{/if} {Lang::T('Phone Number')}">
                      </div>
                      <div class="input-area">
                        <label for="" class="form-label">{Lang::T('Full Name')}*</label>
                        <input type="text" required class="form-control" id="fullname" value="{$fullname}" name="fullname" placeholder="Enter your full name">
                      </div>
                      <div class="input-area">
                        <label for="" class="form-label">{Lang::T('Email')}*</label>
                        <input id="email" required type="text" class="form-control" placeholder="xxxxxxx@xxxx.xx" value="{$email}" name="email">
                      </div>
                    </div>
                  </div>
                  <div class="wizard-form-step" data-step="2">
                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
                      <div class="lg:col-span-3 md:col-span-2 col-span-1">
                        <h4 class="text-base text-slate-800 dark:text-slate-300 my-6">{Lang::T('Password')}</h4>
                      </div>
                      <div class="input-area">
                        <label for="" class="form-label">{Lang::T('Password')}*</label>
                        <input type="password" required class="form-control" id="password" name="password">
                      </div>
                      <div class="input-area">
                        <label for="" class="form-label">{Lang::T('Confirm Password')}*</label>
                        <input type="password" required class="form-control" id="cpassword" name="cpassword">
                      </div>
                    </div>
                  </div>
                  <div class="wizard-form-step" data-step="3">
                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
                      <div class="lg:col-span-3 md:col-span-2 col-span-1">
                        <h4 class="text-base text-slate-800 dark:text-slate-300 my-6">Address</h4>
                      </div>
                      <div class="input-area lg:col-span-3 md:col-span-2 col-span-1">
                        <label for="" class="form-label">{Lang::T('Address')}*</label>
                        <textarea name="address" required id="address" rows="3" class="form-control" value="{$address}"></textarea>
                      </div>
                    </div>
                  </div>
                  <div class="wizard-form-step" data-step="4">
                    <div class="grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
                      <div class="lg:col-span-3 md:col-span-2 col-span-1">
                        <h4 class="text-base text-slate-800 dark:text-slate-300 my-6">Terms and Conditions</h4>
                      </div>
                      <div class="checkbox-area">
                        <label class="inline-flex items-center cursor-pointer">
                          <input type="checkbox" required class="hidden" name="checkbox">
                          <span class="h-4 w-4 border flex-none border-slate-100 dark:border-slate-800 rounded inline-flex ltr:mr-3 rtl:ml-3 relative
                                        transition-all duration-150 bg-slate-100 dark:bg-slate-900">
                            <img src="{$_theme}/assets/images/icon/ck-white.svg" alt="" class="h-[10px] w-[10px] block m-auto opacity-0">
                          </span>
                          <span class="text-slate-500 dark:text-slate-400 text-sm leading-6">You Accept Our T &amp; C </span>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="mt-6   space-x-3">
                    <button class="btn btn-dark prev-button" type="button">prev</button>
                    <button class="btn btn-dark next-button" type="button">next</button>
                  </div>
                </form>
              </div>
            </div>
            <!-- END: Login Form -->
            <div class="mx-auto font-normal text-slate-500 dark:text-slate-400 2xl:mt-12 mt-6 uppercase text-sm text-center"> Already registered? <a href="{$_url}login" class="text-slate-900 dark:text-white font-medium hover:underline"> {Lang::T('Login')}</a>
            </div>
          </div>
        </div>
        <div class="auth-footer3 text-white py-5 px-5 text-xl w-full"> &copy; 2023 {$_c['CompanyName']} &nbsp; <a href="pages/Privacy_Policy.html" target="_blank">Privacy</a> &nbsp; <a href="pages/Terms_and_Conditions.html" target="_blank">T &amp; C</a>
        </div>
      </div>
    </div>
    <!-- scripts -->
    <script src="{$_theme}/scripts/vendors.js"></script>
    <script src="{$_theme}/assets/js/jquery-3.6.0.min.js"></script>
    <script src="{$_theme}/assets/js/rt-plugins.js"></script>
    <script src="{$_theme}/assets/js/app.js"></script>
  </body>
</html>
