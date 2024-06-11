<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>{Lang::T('Login')} - {$_c['CompanyName']}</title>
    <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />

    <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
    <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(to right, #ff5f6d, #ffc371);
            color: #333;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .site-logo {
            color: #fff;
            text-shadow: 2px 2px 4px #000;
            font-size: 2.5rem;
        }

        .panel {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
        }

        .panel-heading {
            background-color: #ff5f6d !important;
            color: #fff !important;
            font-size: 1.2rem;
        }

        .form-group .input-group-addon {
            background-color: #ff5f6d;
            color: #fff;
        }

        .form-control {
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #ff5f6d;
            border-color: #ff5f6d;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #ff4a57;
            border-color: #ff4a57;
        }

        .btn-group .btn-primary {
            width: 100%;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .announcement {
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            padding: 10px;
            border-radius: 10px;
        }

        .announcement h4 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .announcement p {
            font-size: 1rem;
        }

        @media (max-width: 767px) {
            .form-head {
                text-align: center;
            }

            .panel-heading {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-head mb20">
            <h1 class="site-logo h2 mb5 mt5 text-center text-uppercase text-bold">
                {$_c['CompanyName']}
            </h1>
            <hr style="border-color: #fff;">
        </div>
        {if isset($notify)}
            <div class="alert alert-{if $notify_t == 's'}success{else}danger{/if}">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                </button>
                <div>{$notify}</div>
            </div>
        {/if}
        <div class="row">
            <div class="col-md-8">
                <div class="panel announcement">
                    <h4>{Lang::T('Announcement')}</h4>
                    <p>{include file="$_path/../pages/Announcement.html"}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">{Lang::T('Login / Activate Voucher')}</div>
                    <div class="panel-body">
                        <form id="activationForm" action="{$_url}login/activation" method="post">
                            <div class="form-group">
                                <label>{Lang::T('Username')}</label>
                                <div class="input-group">
                                    {if $_c['country_code_phone']!= ''}
                                        <span class="input-group-addon" id="basic-addon1">+</span>
                                    {else}
                                        <span class="input-group-addon" id="basic-addon1"><i class="fas fa-phone-alt"></i></span>
                                    {/if}
                                    <input type="text" class="form-control" name="username" required placeholder="08xxxxxxx">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>{Lang::T('Enter voucher code here')}</label>
                                <input type="text" class="form-control" name="voucher" required autocomplete="off" placeholder="{Lang::T('Code Voucher')}">
                            </div>
                            <div class="btn-group btn-group-justified mb15">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" onclick="activateAndLogin()">{Lang::T('Login / Activate Voucher')}</button>
                                </div>
                            </div>
                            <br>
                            <center class="footer-links">
                                <a href="./pages/Privacy_Policy.html" target="_blank">Privacy</a>
                                &bull;
                                <a href="./pages/Terms_of_Conditions.html" target="_blank">ToC</a>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mikrotik login form -->
    <div class="container" style="display:none;">
        <form id="loginForm" class="form" name="login" action="http://192.168.180.1/login" method="post" onSubmit="return doLogin()">
            <input type="hidden" name="dst" value="http://192.168.180.1/status" />
            <input type="hidden" name="popup" value="true" />
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input id="usernameInput" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="username" type="text" value="" placeholder="Username">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input id="passwordInput" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" name="password" type="password" placeholder="******************">
            </div>
            <div class="flex items-center justify-between">
                <button id="submitBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Click Here To Connect
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function activateAndLogin() {
            // Submit activation form
            $('#activationForm').submit();

            // Wait for 10 seconds
            setTimeout(function() {
                // Get the username and voucher values
                var username = $('input[name="username"]').val();
                var voucher = $('input[name="voucher"]').val();

                // Set the values to Mikrotik login form
                $('#usernameInput').val(username);
                $('#passwordInput').val(voucher);

                // Submit the Mikrotik login form
                $('#loginForm').submit();
            }, 10000);
        }

        function doLogin() {
            // Custom logic for Mikrotik login (if needed)
            return true;
        }
    </script>
</body>

