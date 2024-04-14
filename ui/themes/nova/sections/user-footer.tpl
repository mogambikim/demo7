        </div>
      </div>
    </div>
    </div>
  </div>
		 <footer class="md:block hidden" id="footer">
        <div class="site-footer px-6 bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-300 py-4 ltr:ml-[248px] rtl:mr-[248px]">
          <div class="grid md:grid-cols-2 grid-cols-1 md:gap-5">
            <div class="text-center ltr:md:text-start rtl:md:text-right text-sm">
             {$_c['CompanyFooter']}
            </div>
            <div class="ltr:md:text-right rtl:md:text-end text-center text-sm">
              Billing Software by <a href="https://freeispradius.com" rel="nofollow noreferrer noopener"
                    target="_blank">FreeIspRadius</a>, Theme by <a href="https://FreeIspRadius.com" rel="nofollow noreferrer noopener"
                    target="_blank">FreeIspRadius.</a>
              </a>
            </div>
          </div>
        </div>
      </footer>
      <!-- END: Footer For Desktop and tab -->

      <div class="bg-white bg-no-repeat custom-dropshadow footer-bg dark:bg-slate-700 flex justify-around items-center
    backdrop-filter backdrop-blur-[40px] fixed left-0 bottom-0 w-full z-[9999] bothrefm-0 py-[12px] px-4 md:hidden">
    <a href="#">
          <div>
            <span class=" relative cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center mb-1 dark:text-white
          text-slate-900">
        <iconify-icon icon="heroicons-outline:bell"></iconify-icon>
        <span class="absolute right-[17px] lg:hrefp-0 -hrefp-2 h-4 w-4 bg-red-500 text-[8px] font-semibold flex flex-col items-center
            justify-center rounded-full text-white z-[99]">
          2
        </span>
            </span>
            <span class=" block text-[11px] text-slate-600 dark:text-slate-300">
        Notifications
      </span>
          </div>
        </a>
        <a href="{$_url}accounts/profile" class="relative bg-white bg-no-repeat backdrop-filter backdrop-blur-[40px] rounded-full footer-bg dark:bg-slate-700
      h-[65px] w-[65px] z-[-1] -mt-[40px] flex justify-center items-center">
          <div class="h-[50px] w-[50px] rounded-full relative left-[0px] hrefp-[0px] custom-dropshadow">
            <img src="https://robohash.org/{$_user['id']}?set=set3&size=100x100&bgset=bg1" onerror="this.src='system/uploads/user.default.jpg'" alt="" class="w-full h-full rounded-full border-2 border-slate-100">
          </div>
        </a>
        <a href="{$_url}logout">
          <div>
            <span class="relative cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center mb-1 dark:text-white
          text-slate-900 ">
        <iconify-icon icon="heroicons-outline:login"></iconify-icon>
        <span class="">

        </span>
            </span>
            <span class="block text-[11px] text-slate-600 dark:text-slate-300">
        {Lang::T('Logout')}
      </span>
          </div>
        </a>
      </div>
    </div>
  </main>
  {if $_c['tawkto'] != ''}
            <!--Start of Tawk.to Script-->
            <script type="text/javascript">
                var Tawk_API = Tawk_API || {},
                    Tawk_LoadStart = new Date();
                (function() {
                    var s1 = document.createElement("script"),
                        s0 = document.getElementsByTagName("script")[0];
                    s1.async = true;
                    s1.src='https://embed.tawk.to/{$_c['tawkto']}';
                    s1.charset = 'UTF-8';
                    s1.setAttribute('crossorigin', '*');
                    s0.parentNode.insertBefore(s1, s0);
                })();
            </script>
            <!--End of Tawk.to Script-->
        {/if}
		 {if isset($xfooter)}
            {$xfooter}
        {/if}



        {literal}
            <script>
                var listAtts = document.querySelectorAll(`[api-get-text]`);
                listAtts.forEach(function(el) {
                    $.get(el.getAttribute('api-get-text'), function(data) {
                        el.innerHTML = data;
                    });
                });
                $(document).ready(function() {
                    var listAtts = document.querySelectorAll(`button[type="submit"]`);
                    listAtts.forEach(function(el) {
                        if (el.addEventListener) { // all browsers except IE before version 9
                            el.addEventListener("click", function() {
                                $(this).html(
                                    `<span class="loading"></span>`
                                );
                                setTimeout(() => {
                                    $(this).prop("disabled", true);
                                }, 100);
                            }, false);
                        } else {
                            if (el.attachEvent) { // IE before version 9
                                el.attachEvent("click", function() {
                                    $(this).html(
                                        `<span class="loading"></span>`
                                    );
                                    setTimeout(() => {
                                        $(this).prop("disabled", true);
                                    }, 100);
                                });
                            }
                        }

                    });
                });
            </script>
        {/literal}

  <!-- scripts -->
  <script type="text/javascript" src="{$_theme}/assets/highchart/js/highcharts.js"></script>
  <script type="text/javascript" src="{$_theme}/assets/highchart/js/themes/gray.js"></script>
  <script src="{$_theme}/assets/js/jquery-3.6.0.min.js" sync></script>
  <script src="{$_theme}/assets/js/popper.js"></script>
  <script src="{$_theme}/assets/js/SimpleBar.js"></script>
  <script src="{$_theme}/assets/js/iconify.js"></script>
  <script src="{$_theme}/assets/js/rt-plugins.js"></script>
  <script src="{$_theme}/assets/js/app.js"></script>
</body>
</html>
