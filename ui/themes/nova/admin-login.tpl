<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>{Lang::T('Login')} - {$_c['CompanyName']}</title>
  <link rel="shortcut icon" href="ui/ui/images/logo.png" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
  <link rel="stylesheet" href="ui/ui/styles/bootstrap.min.css">
  <link rel="stylesheet" href="ui/ui/styles/modern-AdminLTE.min.css">
  <style>
    body {
      background: linear-gradient(45deg, #4b6cb7, #182848, #ff416c, #4b6cb7);
      background-size: 400% 400%;
      animation: gradient 15s ease infinite;
      height: 100vh;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      padding: 40px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      width: 100%;
      position: relative;
      overflow: hidden;
    }
    .login::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
      transform: rotate(45deg);
      z-index: -1;
      animation: shine 5s ease infinite;
    }
    .login__img {
      max-width: 150px;
      width: 100%;
      height: auto;
      filter: drop-shadow(0px 0px 10px rgba(0, 0, 0, 0.3));
      animation: pulse 2s ease infinite;
      margin-bottom: 30px;
    }
    .login__title {
      color: #182848;
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .login__content {
      margin-bottom: 20px;
    }
    .login__box {
      position: relative;
      margin-bottom: 20px;
    }
    .login__icon {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #182848;
    }
    .login__box-input {
      position: relative;
    }
    .login__input {
      width: 100%;
      padding: 10px 10px 10px 40px;
      border: none;
      border-bottom: 1px solid #182848;
      background: transparent;
      color: #182848;
    }
    .login__label {
      position: absolute;
      top: 0;
      left: 40px;
      color: #182848;
      pointer-events: none;
      transition: .5s;
    }
    .login__input:focus ~ .login__label,
    .login__input:valid ~ .login__label {
      top: -20px;
      left: 0;
      color: #182848;
      font-size: 12px;
    }
    .login__eye {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      color: #182848;
      cursor: pointer;
    }
    .login__check {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
    }
    .login__check-group {
      display: flex;
      align-items: center;
    }
    .login__check-input {
      display: none;
    }
    .login__check-label {
      color: #182848;
      font-size: 14px;
      cursor: pointer;
      padding-left: 25px;
      position: relative;
    }
    .login__check-label::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 18px;
      height: 18px;
      border: 1px solid #182848;
      background: transparent;
    }
    .login__check-input:checked ~ .login__check-label::before {
      background: #182848;
    }
    .login__forgot {
      color: #182848;
      font-size: 14px;
      text-decoration: none;
    }
    .login__button {
      width: 100%;
      padding: 10px;
      border: none;
      background: #4b6cb7;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      border-radius: 25px;
      cursor: pointer;
      transition: background .3s;
    }
    .login__button:hover {
      background: #3e5a8e;
    }
    .login__register {
      color: #182848;
      font-size: 14px;
      text-align: center;
      margin-top: 20px;
    }
    .login__register a {
      color: #182848;
      text-decoration: none;
    }
    .star {
      position: absolute;
      background: white;
      border-radius: 50%;
      animation: twinkle 2s ease-in-out infinite;
    }
    @keyframes gradient {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }
    @keyframes shine {
      0% {
        transform: rotate(45deg) translate(-50%, -50%);
      }
      50% {
        transform: rotate(45deg) translate(50%, 50%);
      }
      100% {
        transform: rotate(45deg) translate(-50%, -50%);
      }
    }
    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.1);
      }
      100% {
        transform: scale(1);
      }
    }
    @keyframes twinkle {
      0%, 100% {
        opacity: 1;
      }
      50% {
        opacity: 0.2;
      }
    }
  </style>
</head>
<body>
  <div id="stars"></div>
  <div class="login text-center">
    <img src="ui/ui/images/logo.png" alt="{$_c['CompanyName']}" class="login__img">
    <h1 class="login__title">{$_c['CompanyName']}</h1>
    {if isset($notify)}
      {$notify}
    {/if}
    <form action="{$_url}admin/post" method="post" class="login__form">
      <div class="login__content">
        <div class="login__box">
          <i class="ri-user-3-line login__icon"></i>
          <div class="login__box-input">
            <input type="text" required class="login__input" id="login-username" name="username" placeholder=" ">
            <label for="login-username" class="login__label">{Lang::T('Username')}</label>
          </div>
        </div>
        <div class="login__box">
          <i class="ri-lock-2-line login__icon"></i>
          <div class="login__box-input">
            <input type="password" required class="login__input" id="login-pass" name="password" placeholder=" ">
            <label for="login-pass" class="login__label">{Lang::T('Password')}</label>
            <i class="ri-eye-off-line login__eye" id="login-eye"></i>
          </div>
        </div>
      </div>
      <div class="login__check">
        <div class="login__check-group">
          <input type="checkbox" class="login__check-input" id="login-check">
          <label for="login-check" class="login__check-label">{Lang::T('Remember me')}</label>
        </div>
        <a href="#" class="login__forgot">{Lang::T('Forgot Password?')}</a>
      </div>
      <button type="submit" class="login__button">{Lang::T('Login')}</button>
<p class="login__register">
        <a href="https://freeispradius.com/CHANGELOG.md" target="_blank">{Lang::T('Updates')}</a> |
        <a href="https://chat.whatsapp.com/I8a7YGalCLD5c4QLcpiSvz" target="_blank">{Lang::T('Whatsapp')}</a> |
        <a href="https://t.me/freeispradius" target="_blank">{Lang::T('Telegram')}</a>
      </p>
    </form>
  </div>
  <script>
    // Twinkling star animation
    const starsContainer = document.getElementById('stars');
    const numStars = 100;
for (let i = 0; i < numStars; i++) {
  const star = document.createElement('div');
  star.classList.add('star');
  star.style.width = star.style.height = Math.random() * 3 + 'px';
  star.style.left = Math.random() * window.innerWidth + 'px';
  star.style.top = Math.random() * window.innerHeight + 'px';
  star.style.animationDelay = Math.random() * 2 + 's';
  starsContainer.appendChild(star);
}

// Password visibility toggle
const loginEye = document.getElementById('login-eye');
const loginPass = document.getElementById('login-pass');

loginEye.addEventListener('click', function() {
  if (loginPass.type === 'password') {
    loginPass.type = 'text';
    loginEye.classList.remove('ri-eye-off-line');
    loginEye.classList.add('ri-eye-line');
  } else {
    loginPass.type = 'password';
    loginEye.classList.remove('ri-eye-line');
    loginEye.classList.add('ri-eye-off-line');
  }
});
 </script>
</body>
</html>