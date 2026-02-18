<?php 
session_start();

ini_set('display_errors', 1);
error_reporting(E_ERROR & E_PARSE);

const INDEX=1;

require_once("config.inc.php");

spl_autoload_register(function ($class) { 

  if (strpos($class, "PHPMailer") === false && strpos($class, "Fpdi") === false){
      require_once("./includes/classes/" . mb_strtolower($class, 'UTF-8') . ".class.php");
  }
  else if (strpos($class, "PHPMailer") !== false){
      require_once('./includes/PHPmailer/Exception.php');
      require_once('./includes/PHPmailer/PHPMailer.php');
      require_once('./includes/PHPmailer/SMTP.php');
  }
});



function urediDatum($dateTime){
  if (trim($dateTime)=="")
    return "";
    
  $polje=explode(" ",$dateTime);
  $datum=explode("-",$polje[0]);
  return ltrim($datum[2],"0").".".ltrim($datum[1],"0").".".ltrim($datum[0],"0").". ".$polje[1];
}


$rubrika=$_GET["rubrika"] ? $_GET["rubrika"] : "zatvaranja";


$cookie = isset($_COOKIE['zapamtiMe']) ? $_COOKIE['zapamtiMe'] : '';
if (!$_SESSION["id"] && $cookie && !$_POST["username"]) {
    list ($user, $token, $mac) = explode(':', $cookie);
    if (!hash_equals(hash_hmac('sha256', $user . ':' . $token, SECRET_KEY_LOGIN), $mac)) {
        Korisnik::ponistiTokenZaKorisnika($user, $token, 1);
        unset($_COOKIE['zapamtiMe']); 
        setcookie('zapamtiMe', "", time()-3600,"/"); 
    }
    else {
        $usertoken = Korisnik::dohvatiTokenZaKorisnika($user, $token, 1);

        if (hash_equals($usertoken, $token)) {
            
            $k=Korisnik::dohvatiKorisnikaUsername($user);
            if ($k->id>0){
                $_SESSION["originalniKorisnik"]=$k->id;
                $_SESSION["originalnaRazina"]=$k->razina;
                $_SESSION["id"]=$k->id;
                $_SESSION["ime"]=$k->ime;
                $_SESSION["prezime"]=$k->prezime;
                $_SESSION["email"]=$k->email;
                $_SESSION["razina"]=$k->razina;
                Korisnik::zabiljeziLogin($k->id);
                $loginGreska=0;
                
            }
            else {
                $loginGreska=1;
            }
        }
        else {
            Korisnik::ponistiTokenZaKorisnika($user, $token, 1);
            unset($_COOKIE['zapamtiMe']); 
            setcookie('zapamtiMe', "", time()-3600,"/"); 
        }
    }
}
else if (isset($_POST["username"])){

    $k=Korisnik::login($_POST["username"], $_POST["password"]);

    if ($k->id>0){
        $_SESSION["originalniKorisnik"]=$k->id;
        $_SESSION["originalnaRazina"]=$k->razina;
        $_SESSION["id"]=$k->id;
        $_SESSION["ime"]=$k->ime;
        $_SESSION["prezime"]=$k->prezime;
        $_SESSION["email"]=$k->email;
        $_SESSION["razina"]=$k->razina;
        Korisnik::zabiljeziLogin($k->id);
        $loginGreska=0;

        if ($_POST["zapamtiMe"]){
            $token = bin2hex(random_bytes(128)); // generate a token, should be 128 - 256 bit
            if (Korisnik::pohraniTokenZaKorisnika($k->id, $token, 1)){
                $cookie = $k->username . ':' . $token;
                $mac = hash_hmac('sha256', $cookie, SECRET_KEY_LOGIN);
                $cookie .= ':' . $mac;
                setcookie('zapamtiMe', $cookie,time()+60*60*24*30,"/","",false,true);
                
            }
            
        }
       

    }
    else {
        $loginGreska=1;
    }
}



if (isset($_SESSION["id"]) && $_GET["rubrika"]=="rezultatiPdf"){
  require_once("/stranice/".$_GET["rubrika"]."/".$_GET["rubrika"].".php");
}
else if (isset($_SESSION["id"])){?>

    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <!DOCTYPE html>

    <html
      lang="en"
      class="light-style layout-menu-fixed layout-compact"
      dir="ltr"
      data-theme="theme-default"
      data-assets-path="./assets/"
      data-template="vertical-menu-template-free">
      <head>
        <meta charset="utf-8" />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title>Zatvaranja i zauzimanja</title>

        <meta name="description" content="" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="/favicon.ico" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
          href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
          rel="stylesheet" />

        <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
        <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="/assets/css/demo.css" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
        <link rel="stylesheet" href="/assets/vendor/libs/apex-charts/apex-charts.css" />

        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="/includes/sweetalert2/bootstrap-4.min.css">

        <link href="/includes/fontawesome6/css/fontawesome.css" rel="stylesheet" />
        <link href="/includes/fontawesome6/css/brands.css" rel="stylesheet" />
        <link href="/includes/fontawesome6/css/solid.css" rel="stylesheet" />
      

        <!-- DataTables -->
        
        <link rel="stylesheet" href="/includes/datatables/datatables.min.css" />
        <link rel="stylesheet" href="/includes/datatables/datatables-responsive.min.css" />
        <script src="/includes/datatables/datatables.min.js"></script>

        <!-- Helpers -->
        <script src="/assets/vendor/js/helpers.js"></script>
        <script src="/assets/js/config.js"></script>

        <style>
          
          .check-parent {
            display: flex;
            align-items: center;
          }

        </style>
      </head>

      <body>
        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
          <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
              <div class="app-brand demo">
                <a href="/" class="app-brand-link">
                  <span class="app-brand-logo demo">
                   
                    <img src="/img/logo.svg" style="height: 50px;">
                  </span>
                  <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 15px;">Zatvaranje cesta<br>Zauzimanje površina</span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                  <i class="bx bx-chevron-left bx-sm align-middle"></i>
                </a>
              </div>

              <div class="menu-inner-shadow"></div>

              <ul class="menu-inner py-1">
                


                <li class="menu-header small text-uppercase">
                  <span class="menu-header-text">Zatvaranja</span>
                </li>
                <!-- Apps -->
               
                <li class="menu-item <?php if ($rubrika=="zatvaranja" && (!isset($_GET["aktivno"]) || $_GET["aktivno"]==1) || $rubrika=="zatvaranjeNovoUredi") echo "active";?>">
                  <a href="/zatvaranja" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-check"></i><div data-i18n="Zatvaranja cesta">Aktivna</div>
                  </a>
                </li>

                <li class="menu-item <?php if ($rubrika=="zatvaranja" && isset($_GET["aktivno"]) && $_GET["aktivno"]==0) echo "active";?>">
                  <a href="/zatvaranja/?aktivno=0" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-question-mark"></i><div data-i18n="Zatvaranja cesta">U pripremi</div>
                  </a>
                </li>

                <li class="menu-item <?php if ($rubrika=="zatvaranja" && $_GET["aktivno"]==-1) echo "active";?>">
                  <a href="/zatvaranja/?aktivno=-1" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-x"></i><div data-i18n="Zatvaranja cesta">Neaktivna</div>
                  </a>
                </li>
                
                <?php if ($_SESSION["razina"]==1){ ?>
                <li class="menu-header small text-uppercase">
                  <span class="menu-header-text">Korisnici</span>
                </li>
                <!-- Apps -->
                <li class="menu-item <?php if ($rubrika=="korisnici" || $rubrika=="korisnikNoviUredi") echo "active";?>">
                  <a href="/korisnici" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i><div data-i18n="Korisnici">Korisnici</div>
                  </a>
                </li>
                <?php } ?>

          
              </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
              <!-- Navbar -->

              <nav
                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                  <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="bx bx-menu bx-sm"></i>
                  </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                 

                  <ul class="navbar-nav flex-row align-items-center ms-auto">
                    <!-- Place this tag where you want the button to render. -->
                   

                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <div class="avatar avatar-online">
                          <!-- <img src="./assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" /> -->
                          <i class="bx bx-user"></i>
                        </div>
                      </a>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                          <a class="dropdown-item" href="#">
                            <div class="d-flex">
                              <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                  <!-- <img src="./assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" /> -->
                                  <i class="bx bx-user"></i>
                                  <!--  <i class="menu-icon tf-icons bx bx-user"></i> -->
                                </div>
                              </div>
                              <div class="flex-grow-1">
                                <span class="fw-medium d-block"><?php echo $_SESSION["ime"]." ".$_SESSION["prezime"];?></span>
                                <small class="text-muted">Korisnik</small>
                              </div>
                            </div>
                          </a>
                        </li>
                        <li>
                          <div class="dropdown-divider"></div>
                        </li>

                        <li>
                          <a class="dropdown-item" href="/promjenaLozinke">
                            <i class="bx bx-lock-alt me-2"></i>
                            <span class="align-middle">Promjena lozinke</span>
                          </a>
                        </li>

           

                        <li>
                          <a class="dropdown-item" href="/stranice/logout/logout.php">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Odjava</span>
                          </a>
                        </li>
                      </ul>
                    </li>
                    <!--/ User -->
                  </ul>
                </div>
              </nav>

              <!-- / Navbar -->

              <!-- Content wrapper -->
              <div class="content-wrapper">
                <!-- Content -->
                <?php
                
                if (isset($rubrika)) {
                    if (file_exists("./stranice/" . $rubrika . "/" . $rubrika . ".php"))
                        require_once("./stranice/" . $rubrika . "/" . $rubrika . ".php");
                    else
                        echo "Pogrešna rubrika";
                } else  {
                    require_once("./stranice/zatvaranja/zatvaranja.php");                          
                }
                
                ?>

              </div>
              <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
          </div>

          <!-- Overlay -->
          <div class="layout-overlay layout-menu-toggle"></div>
        </div>
        <!-- / Layout wrapper -->

        <!-- <script src="./assets/vendor/libs/jquery/jquery.js"></script> -->
        <script src="/assets/vendor/libs/popper/popper.js"></script>
        <script src="/assets/vendor/js/bootstrap.js"></script>
        <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
        <script src="/assets/vendor/js/menu.js"></script>

        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="/assets/vendor/libs/apex-charts/apexcharts.js"></script>

        <!-- Main JS -->
        <script src="/assets/js/main.js"></script>


        <script src="/includes/sweetalert2/sweetalert2.min.js"></script>
        <!-- <script src="./includes/datepicker/datepicker.min.js"></script> -->

        <!-- Page JS -->
        <script src="/assets/js/dashboards-analytics.js"></script>

        <!-- Place this tag in your head or just before your close body tag. -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>
      </body>
    </html>


<?php
}
else {
?>

<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="./assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Zatvaranja i zauzimanja</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="./assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />

    <!-- Helpers -->
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">

              <div class="row col-12">
                <img src="/img/logo.svg" style="width: 50%; margin-left: 25%;">
              </div>

              <div class="row col-12" style="text-align: center;">
                <h4 class="mb-2">Zatvaranje cesta i zauzimanje javnih površina</h4>
              </div>
             
              <p class="mb-2 mt-4">Prijavite se za pristup aplikaciji.</p>

              <form id="formAuthentication" class="mb-3" action="index.php" method="post">
              <?php if ($loginGreska==1) { ?>
                  
                  <div class="alert alert-danger alert-dismissible" role="alert">
                    Pogrešno korisničko ime ili lozinka!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                    </button>
                  </div>
              <?php } ?>
                <div class="mb-3">
                  <label for="email" class="form-label" style="font-style: italic;">Korisničko ime</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Unesite korisničko ime"
                    autofocus
                    value="" />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password" style="font-style: italic;">Lozinka</label>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password" 
                      value=""/>
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check">
                   
                    <input class="form-check-input" type="checkbox" id="zapamtiMe" name="zapamtiMe" checked/>
                    <label class="form-check-label" for="zapamtiMe"> Ostani prijavljen 30 dana! </label>
                  </div>
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Prijavi se</button>
                </div>
              </form>

            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
      
    </div>

    <div style="text-align: center;"><a href="https://themeselection.com/" target="_blank">ThemeSelection</a></div>
    <!-- / Content -->
    
    <!-- Core JS -->

    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="/assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
<?php } ?>
<script>
  function validateForm(){
      var greska=0;
      
      $(".required:visible").each(function( index, element ) {
          var type=$(element)[0].type;

          if ($.trim($(element).val())=="" || (type=="select-multiple" && $(element).val().length==0)){
              
              if (type=="select-one" && $(element).parent().find(".select2").length==0 && !$(element).is(":visible")){
                  return true;
              }
              
              greska++;

              if (type=="text" || type=="number" || type=="password"){
                  $(element).css("background-color","#ffe0db");
              }
              else if (type=="select-one"){
                  $(element).css("background-color","#ffe0db");
                  $(element).parent().find(".select2-selection").css("background","#ffe0db");
              }
              else if (type=="select-multiple"){
                  $(element).css("background-color","#ffe0db");
                  $(element).parent().find(".select2-selection").css("background","#ffe0db");
              }
              else if (type=="textarea"){
                  $(element).css("background-color","#ffe0db");
                  $(element).parent().find(".note-editable").css("background-color","#ffe0db"); 
              }
              else if (type=="file"){
                  $(element).css("background-color","#ffe0db");
                  $(element).parent().find(".custom-file-label").css("background-color","#ffe0db"); 
              }
          }
          else {
              if (type=="text" || type=="number"){
                  $(element).css("background-color","");
              }
              else if (type=="select-one"){
                  $(element).css("background-color","");
                  $(element).parent().find(".select2-selection").css("background","");
              }
              else if (type=="select-multiple"){
                  $(element).css("background-color","");
                  $(element).parent().find(".select2-selection").css("background","");
              }
              else if (type=="textarea"){
                  $(element).css("background-color","");
                  $(element).parent().find(".note-editable").css("background-color","");
                  
              }
              else if (type=="file"){
                  $(element).css("background-color","");
                  $(element).parent().find(".custom-file-label").css("background-color",""); 
              }
          }
      });

      if (greska==0)
          return true;
      else
          return false;
  }



</script>