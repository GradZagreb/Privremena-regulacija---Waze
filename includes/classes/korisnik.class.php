<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class Korisnik {

	/* SELECT */
	public static function login($username, $password) {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT korisnik.id, ime, prezime, osoba.email, username, razina FROM korisnik, osoba WHERE korisnik.osoba=osoba.id AND username=? AND password=SHA2(CONCAT(?,salt),512) AND korisnik.odobren=1 AND korisnik.aktivan=1 AND korisnik.obrisan=0');
		$sql->bind_param('ss', $username, $password);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$sql->bind_result($id, $ime, $prezime, $email, $username, $razina);  
			
			$sql->fetch();

			$k=array("id" => $id, "ime" => $ime, "prezime" => $prezime, "email" => $email, "username" => $username, "razina" => $razina);

			$mysql->close();
			return (Object)$k;
			
			
		}
		else {
			$mysql->close();
			return false;
		}
	}

	
	public static function dohvatiTokenZaKorisnika($korisnik, $token, $vrsta){

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT token FROM korisnik, korisnikToken WHERE korisnik.id=korisnikToken.korisnik AND korisnik.username=? AND korisnikToken.token=? AND korisnikToken.vrijediDo>NOW() AND korisnikToken.obrisano=0 AND korisnikToken.vrsta=? ORDER BY korisnikToken.id DESC LIMIT 1');
		$sql->bind_param('ssi', $korisnik, $token, $vrsta);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$sql->bind_result($token);  
			
			$sql->fetch();
			$mysql->close();
			return $token;

		}
		else {
			$mysql->close();
			return false;
		}

		$mysql->close();
	}



	public static function provjeriUsername($username) {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT * FROM korisnik WHERE username=?');
		$sql->bind_param('s', $username);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$mysql->close();
			return true;
		}
		else {
			$mysql->close();
			return false;
		}
	}

	
	
	public static function dohvatiKorisnike() {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT korisnik.id, ime, prezime, osoba.email, username, razina, korisnikRazina.naziv, slika, korisnik.napomena FROM korisnik, osoba, korisnikRazina WHERE korisnik.osoba=osoba.id AND korisnik.razina=korisnikRazina.id AND korisnik.obrisan=0');
		//$sql->bind_param('ii', $_SESSION["razina"],$_SESSION["id"]);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows>0){
			$sql->bind_result($id, $ime, $prezime, $email, $username, $razina, $razinaNaziv, $slika, $napomena);  
			$polje=array();
			while ($sql->fetch()){


				$k=array("id" => $id, "ime" => $ime, "prezime" => $prezime, "email" => $email, "razina" => $razina, "razinaNaziv" => $razinaNaziv, "username" => $username, "slika" => $slika, "napomena" => $napomena);


				$polje[]=(Object)$k;

			}

			$mysql->close();
			return $polje;
			
		}
		else {
			$mysql->close();
			return false;
		}
	}


	public static function dohvatiKorisnika($id) {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT korisnik.id, ime, prezime, osoba.email, username, razina, slika, korisnik.napomena FROM korisnik, osoba WHERE korisnik.osoba=osoba.id AND  korisnik.id=? AND korisnik.obrisan=0');
		$sql->bind_param('i', $id);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$sql->bind_result($id, $ime, $prezime,  $email, $username, $razina, $slika, $napomena);  
			
			$sql->fetch();


			$k=array("id" => $id, "ime" => $ime, "prezime" => $prezime, "email" => $email,  "razina" => $razina, "username" => $username, "slika" => $slika, "napomena" => $napomena);

			$mysql->close();
			return (Object)$k;

		}
		else {
			$mysql->close();
			return false;
		}
	}

	public static function dohvatiKorisnikaUsername($username) {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT korisnik.id, ime, prezime, osoba.email, username, razina, slika, korisnik.napomena FROM korisnik, osoba WHERE korisnik.osoba=osoba.id AND  korisnik.username=? AND korisnik.obrisan=0');
		$sql->bind_param('s', $username);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$sql->bind_result($id, $ime, $prezime, $email, $username, $razina, $slika, $napomena);  
			
			$sql->fetch();


			$k=array("id" => $id, "ime" => $ime, "prezime" => $prezime, "email" => $email, "razina" => $razina, "username" => $username, "slika" => $slika, "napomena" => $napomena);

			$mysql->close();
			return (Object)$k;

		}
		else {
			$mysql->close();
			return false;
		}
	}

	//dohvaća korisnika za id osobe predan kao parametar
	public static function dohvatiKorisnikaZaOsobu($idOsobe) {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare('SELECT korisnik.id, ime, prezime,  osoba.email, username, razina, slika, korisnik.napomena FROM korisnik, osoba WHERE korisnik.osoba=osoba.id AND korisnik.osoba=? AND korisnik.obrisan=0');
		$sql->bind_param('i', $idOsobe);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){
			$sql->bind_result($id, $ime, $prezime, $email, $username, $razina, $slika, $napomena);  
			
			$sql->fetch();


			$k=array("id" => $id, "ime" => $ime, "prezime" => $prezime, "email" => $email, "razina" => $razina, "username" => $username, "slika" => $slika, "napomena" => $napomena);

			$mysql->close();
			return (Object)$k;

			
		}
		else {
			$mysql->close();
			return false;
		}
	}


	

	/* INSERT, UPDATE, DELETE */


	public static function pohraniKorisnika($ime, $prezime, $email, $razina){

		$db = new db();
		$mysql = $db->mysql;

		$slika=NULL;
		$napomena=NULL;

		$true=true;
		$salt=bin2hex(openssl_random_pseudo_bytes(32,$true));
		$password=bin2hex(openssl_random_pseudo_bytes(8,$true));


		$username=mb_strtolower(mb_substr($ime,0,1,"utf-8").explode(" ",$prezime)[0],"utf-8");
		setlocale(LC_CTYPE, 'en_US.UTF-8');
		$username=iconv('utf-8', 'ascii//TRANSLIT', $username);
		$baseusername=preg_replace('/[^A-Za-z0-9\-]/', '', $username);

		$username=$baseusername;

		$broj=2;
		while (self::provjeriUsername($username)){
			$username=$baseusername.$broj;
			$broj++;
		}

		
		$mysql->begin_transaction();


		$dateTime = date("Y-m-d H:i:s", time());

		$sql = $mysql->prepare('SELECT id FROM osoba WHERE email=?');
		$sql->bind_param('s', $email);
		$sql->execute();
		$sql->store_result();

		//ako već postoji unesena navedena osoba i samo treba kreirati korisnika
		if ($sql->num_rows>0){

			$true=true;
			$salt=bin2hex(openssl_random_pseudo_bytes(32,$true));
			$sql = $mysql->prepare('INSERT INTO korisnik(osoba, username, salt, password, razina, slika, napomena, unio, datumUnosa, odobren, aktivan) VALUES ((SELECT id FROM osoba WHERE ime=? AND prezime=? AND email=?), ?, ?, SHA2(CONCAT(?,?),512), ?, ?, ?, ?, ?, 1, 1)');
			$sql->bind_param('sssssssissis', $ime, $prezime, $email, $username, $salt, $password, $salt, $razina, $slika, $napomena, $_SESSION["id"], $dateTime);


			if (!$sql->execute()){
				$mysql->rollback();
				$mysql->close();
				return false;
			}
		}
		//ako navedena osoba već nije unesena, dodaje se prvo osoba pa onda korisnik
		else {

			$sql = $mysql->prepare('INSERT INTO osoba(ime, prezime, email, unio, datumUnosa) VALUES (?, ?, ?, ?, ?)');
			$sql->bind_param('sssis', $ime, $prezime, $email, $_SESSION["id"], $dateTime);
			
			if (!$sql->execute()){
				$mysql->rollback();
				$mysql->close();
				return false;
			}

			
			$sql = $mysql->prepare('INSERT INTO korisnik(osoba, username, salt, password, razina, slika, napomena, unio, datumUnosa, odobren, aktivan) VALUES ((SELECT id FROM osoba WHERE ime=? AND prezime=? AND email=? AND datumUnosa=?), ?, ?, SHA2(CONCAT(?,?),512), ?, ?, ?, ?, ?, 1, 1)');
			$sql->bind_param('ssssssssissis', $ime, $prezime, $email, $dateTime, $username, $salt, $password, $salt, $razina, $slika, $napomena, $_SESSION["id"], $dateTime);


			if (!$sql->execute()){
				$mysql->rollback();
				$mysql->close();
				return false;
			}
			

		}


		$sql = $mysql->prepare('SELECT id FROM korisnik WHERE username=? AND datumUnosa=?');
		$sql->bind_param('ss', $username, $dateTime);
		$sql->execute();
		$sql->store_result();

		$sql->bind_result($idKorisnika);  
		$sql->fetch();
		

		$mysql->commit();
		$mysql->close();
		return array("username"=>$username, "password"=>$password);
		

	}

	//$_GET["id"], $id, $ime, $prezime, $email, $razina
	public static function urediKorisnika($id, $ime, $prezime, $email, $razina){

		$db = new db();
		$mysql = $db->mysql;

		$mysql->begin_transaction();


		$sql = $mysql->prepare('UPDATE osoba SET ime=?, prezime=?, email=? WHERE id=(SELECT osoba FROM korisnik WHERE id=?)');
		$sql->bind_param('sssi', $ime, $prezime, $email, $id);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return -1;
		}

		$sql = $mysql->prepare('UPDATE korisnik SET razina=? WHERE id=?');
		$sql->bind_param('ii', $razina, $id);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return -2;
		}
		
		$mysql->commit();
		$mysql->close();
		return 1;

	}


	public static function promijeniLozinku($korisnik, $password){

		$db = new db();
		$mysql = $db->mysql;

		$true=true;
		$salt=bin2hex(openssl_random_pseudo_bytes(32,$true));
		//$password=bin2hex(openssl_random_pseudo_bytes(8,$true));

		$sql = $mysql->prepare('UPDATE korisnik SET salt=?, password=SHA2(CONCAT(?,?),512) WHERE id=? AND obrisan=0');
		$sql->bind_param('sssi', $salt, $password, $salt, $korisnik);
		if (!$sql->execute()){			
			$mysql->close();
			return false;
		}
			

		$mysql->close();
		return true;
		

	}

	public static function promijeniLozinkuProfila($korisnik, $staraLozinka, $novaLozinka){

		$db = new db();
		$mysql = $db->mysql;


		$sql = $mysql->prepare('SELECT * FROM korisnik WHERE id=? AND password=SHA2(CONCAT(?,salt),512) AND korisnik.obrisan=0');
		$sql->bind_param('is', $korisnik, $staraLozinka);
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows!=1){
			$mysql->close();
			return false;
		}
		
		$true=true;
		$salt=bin2hex(openssl_random_pseudo_bytes(32,$true));
		//$password=bin2hex(openssl_random_pseudo_bytes(8,$true));

		$sql = $mysql->prepare('UPDATE korisnik SET salt=?, password=SHA2(CONCAT(?,?),512) WHERE id=? AND obrisan=0');
		$sql->bind_param('sssi', $salt, $novaLozinka, $salt, $korisnik);
		if (!$sql->execute()){			
			$mysql->close();
			return false;
		}
			

		$mysql->close();
		return true;
		

	}

	

	public static function zabiljeziLogin($korisnik){

		$db = new db();
		$mysql = $db->mysql;
		$mysql->begin_transaction();
		
		$sql = $mysql->prepare('UPDATE korisnik SET zadnjaPrijava=NOW() WHERE id=?');
		$sql->bind_param('i', $korisnik);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return false;
		}

		$mysql->commit();
		$mysql->close();
		return true;
		
	}


	

	public static function pohraniTokenZaKorisnika($korisnik, $token, $vrsta){

		$db = new db();
		$mysql = $db->mysql;
		$mysql->begin_transaction();

		$vrijediDo=date('Y-m-d',strtotime('+30 days',strtotime("now")));
		$sql = $mysql->prepare('INSERT INTO korisniktoken(korisnik, token, vrsta, vrijediDo, datumUnosa) VALUES (?, ?, ?, ?, NOW())');
		$sql->bind_param('isis', $korisnik, $token, $vrsta, $vrijediDo);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return false;
		}

		$mysql->commit();
		$mysql->close();
		return true;
		
	}

	public static function ponistiTokenZaKorisnika($korisnik, $token, $vrsta){

		$db = new db();
		$mysql = $db->mysql;
		$mysql->begin_transaction();
		
		$sql = $mysql->prepare('UPDATE korisnikToken SET obrisano=1, datumBrisanja=NOW() WHERE korisnik=(SELECT id FROM korisnik WHERE username=? AND obrisan=0) AND token=? AND vrsta=? AND obrisano=0');
		$sql->bind_param('ssi', $korisnik, $token, $vrsta);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return false;
		}

		$mysql->commit();
		$mysql->close();
		return true;
		
	}


	


	public static function obrisiKorisnika($id) {

		$db = new db();
		$mysql = $db->mysql;

		
		$sql = $mysql->prepare('UPDATE korisnik SET obrisan=1, obrisao=?, datumBrisanja=NOW() WHERE id=?');
		$sql->bind_param('ii', $_SESSION["id"], $id);
		
		if (!$sql->execute()){
			$mysql->close();
			return false;
		}
		else {
			$mysql->close();
			return true;
		}

	}	


}

	
	
	


?>