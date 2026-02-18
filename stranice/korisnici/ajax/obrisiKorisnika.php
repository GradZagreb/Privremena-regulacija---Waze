<?php

    session_start();

    if (!$_SESSION["id"]) die(false);

    if ($_SESSION["id"]>1) die(false);

	spl_autoload_register(function ($class) {
	    require_once($_SERVER["DOCUMENT_ROOT"]."/includes/classes/" . mb_strtolower($class, 'UTF-8') . ".class.php");
	});


	if (isset($_POST["id"])){
		if (Korisnik::obrisiKorisnika(trim($_POST["id"])))
            echo true;
        else
            echo false;

    }
    else {
        echo false;
    }

	die();

?>