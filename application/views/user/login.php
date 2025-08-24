<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title></title>
  <!-- Favicon-->
  <link rel="icon" href="<?php echo base_url(); ?>assets/front/images/base/fav.png" type="image/x-icon">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/dist/css/bootstrap.min.css"> -->
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <script src="<?php echo base_url(); ?>assets/back/js/jquery.min.js"></script>

  <style type="text/css">
    @import url('https://rsms.me/inter/inter-ui.css');

    ::selection {
      background: #2D2F36;
    }

    ::-webkit-selection {
      background: #2D2F36;
    }

    ::-moz-selection {
      background: #2D2F36;
    }

    body {
      background: white;
      font-family: 'Inter UI', sans-serif;
      margin: 0;
      padding: 20px;
    }

    .page {
      background: #fff;
      display: flex;
      flex-direction: column;
      height: calc(100% - 40px);
      position: absolute;
      place-content: center;
      width: calc(100% - 40px);
    }

    @media (max-width: 767px) {
      .page {
        height: auto;
        margin-bottom: 20px;
        padding-bottom: 20px;
      }
    }

    .container {
      display: flex;
      height: 320px;
      margin: 0 auto;
      width: 640px;
    }

    @media (max-width: 767px) {
      .container {
        flex-direction: column;
        height: 630px;
        width: 320px;
      }
    }

    .left {
      background: #ECECEF;
      height: calc(100% - 40px);
      top: 20px;
      position: relative;
      width: 50%;
    }

    @media (max-width: 767px) {
      .left {
        height: 100%;
        left: 20px;
        width: calc(100% - 40px);
        max-height: 270px;
      }
    }

    .login {
      font-size: 50px;
      font-weight: 900;
      margin: 25px 40px 25px;
    }

    .eula {
      color: #999;
      font-size: 14px;
      line-height: 1.5;
      margin: 25px;
    }

    .right {
      background: #474A59;
      box-shadow: 0px 0px 40px 16px rgba(0, 0, 0, 0.22);
      color: #F1F1F2;
      position: relative;
      width: 50%;
    }

    @media (max-width: 767px) {
      .right {
        flex-shrink: 0;
        height: 100%;
        width: 100%;
        max-height: 350px;
      }
    }

    svg {
      position: absolute;
      width: 320px;
    }

    path {
      fill: none;
      stroke: url(#linearGradient);
      ;
      stroke-width: 4;
      stroke-dasharray: 240 1386;
    }

    .form {
      margin: 40px;
      position: absolute;
    }

    label {
      color: #c2c2c5;
      display: block;
      font-size: 14px;
      height: 16px;
      margin-top: 20px;
      margin-bottom: 5px;
    }

    input {
      background: transparent;
      border: 0;
      color: #fff;
      font-size: 17px;
      height: 30px;
      line-height: 30px;
      outline: none !important;
      width: 100%;
    }

    input::-moz-focus-inner {
      border: 0;
    }

    #submit {
      color: #707075;
      margin-top: 40px;
      transition: color 300ms;
    }

    #submit:focus {
      color: #f2f2f2;
    }

    #submit:active {
      color: #d0d0d2;
    }

    .blink_me {
      animation: blinker 1s linear infinite;
    }

    @keyframes blinker {
      50% {
        opacity: 0;
      }
    }

    .alert-danger {
      background-color: red;
      width: 80%;
      padding: 10px;
      margin: 10px;
      color: #fff;
      font-size: 12px;
    }
  </style>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
</head>

<body>

  <div class="page">
    <div class="container">
      <div class="left">
        <div class="login animate__animated animate__bounceIn">
          <center>
            <img src="<?php echo base_url('assets/back/images/logo.png'); ?>" width="120px" height="120px" />
            <br /><?= $this->session->flashdata('pesan') ?>
          </center>
        </div>
        <div class="eula animate__animated animate__bounce">
          <center style="font-weight: bold; font-size: 20px; color: #000;">Basketball System <br /> <span class="blink_me" style="font-size: 12px">LOGIN PARENT</span></center>
        </div>
      </div>
      <div class="right">
        <svg viewBox="0 0 320 300">
          <defs>
            <linearGradient inkscape:collect="always" id="linearGradient" x1="13" y1="193.49992" x2="307" y2="193.49992" gradientUnits="userSpaceOnUse">
              <stop style="stop-color:#FE0002;" offset="0" id="stop876" />
              <stop style="stop-color:#0E2839;" offset="1" id="stop878" />
            </linearGradient>
          </defs>
          <path d="m 40,120.00016 239.99984,-3.2e-4 c 0,0 24.99263,0.79932 25.00016,35.00016 0.008,34.20084 -25.00016,35 -25.00016,35 h -239.99984 c 0,-0.0205 -25,4.01348 -25,38.5 0,34.48652 25,38.5 25,38.5 h 215 c 0,0 20,-0.99604 20,-25 0,-24.00396 -20,-25 -20,-25 h -190 c 0,0 -20,1.71033 -20,25 0,24.00396 20,25 20,25 h 168.57143" />
        </svg>
        <?php echo form_open("user/auth"); ?>
        <div class="form animate__animated animate__fadeIn">
          <label for="email">No. HP</label>
          <input type="text" id="email" name="uname">
          <label for="password">Password<i style="float: right; color: #F1F1F2" toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></i></label>
          <input type="password" id="password" name="password">
          <input type="submit" id="submit" value="Login">
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>


  <script>
    setTimeout(function() {
      $('#gone').hide('fast');
    }, 5000);

    $(".toggle-password").click(function() {

      $(this).toggleClass("fa-eye fa-eye-slash");
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    });
  </script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.2.0/anime.min.js"></script>

  <script type="text/javascript">
    var current = null;
    document.querySelector('#email').addEventListener('focus', function(e) {
      if (current) current.pause();
      current = anime({
        targets: 'path',
        strokeDashoffset: {
          value: 0,
          duration: 700,
          easing: 'easeOutQuart'
        },
        strokeDasharray: {
          value: '240 1386',
          duration: 700,
          easing: 'easeOutQuart'
        }
      });
    });
    document.querySelector('#password').addEventListener('focus', function(e) {
      if (current) current.pause();
      current = anime({
        targets: 'path',
        strokeDashoffset: {
          value: -336,
          duration: 700,
          easing: 'easeOutQuart'
        },
        strokeDasharray: {
          value: '240 1386',
          duration: 700,
          easing: 'easeOutQuart'
        }
      });
    });
    document.querySelector('#submit').addEventListener('focus', function(e) {
      if (current) current.pause();
      current = anime({
        targets: 'path',
        strokeDashoffset: {
          value: -730,
          duration: 700,
          easing: 'easeOutQuart'
        },
        strokeDasharray: {
          value: '530 1386',
          duration: 700,
          easing: 'easeOutQuart'
        }
      });
    });
  </script>


</body>