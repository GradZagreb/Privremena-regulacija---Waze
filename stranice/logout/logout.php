<?php


spl_autoload_register(function ($class) { 
    if (strpos($class, "PHPMailer") === false && strpos($class, "Fpdi") === false){
        require_once($_SERVER["DOCUMENT_ROOT"]."/includes/classes/" . mb_strtolower($class, 'UTF-8') . ".class.php");
    }
    else if (strpos($class, "PHPMailer") !== false){
        require_once($_SERVER["DOCUMENT_ROOT"].'/includes/PHPmailer/Exception.php');
		require_once($_SERVER["DOCUMENT_ROOT"].'/includes/PHPmailer/PHPMailer.php');
		require_once($_SERVER["DOCUMENT_ROOT"].'/includes/PHPmailer/SMTP.php');
    }
});


session_start();
session_destroy();
session_unset();
$_SESSION=array();

setcookie('PHPSESSID', '', time() - 86400, '/');

$cookie = isset($_COOKIE['zapamtiMe']) ? $_COOKIE['zapamtiMe'] : '';
if ($cookie){
    list ($user, $token, $mac) = explode(':', $cookie);
    Korisnik::ponistiTokenZaKorisnika($user, $token, 1);
    unset($_COOKIE['zapamtiMe']); 
    setcookie('zapamtiMe', "", time()-3600,"/"); 
}


header('Location: /');

?>