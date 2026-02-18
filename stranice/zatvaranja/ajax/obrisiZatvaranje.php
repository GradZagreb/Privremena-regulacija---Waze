<?php

    session_start();

    if (!$_SESSION["id"]) die(false);

	spl_autoload_register(function ($class) {
	    require_once($_SERVER["DOCUMENT_ROOT"]."/includes/classes/" . mb_strtolower($class, 'UTF-8') . ".class.php");
	});

	if (isset($_POST["id"])){
		if (Zatvaranje::obrisiZatvaranje(trim($_POST["id"])))
            echo true;
        else
            echo false;

    }
    else {
        echo false;
    }

	die();

?>