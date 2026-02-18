<?php


class Zatvaranje {
	
	/* SELECT */

	public static function dohvatiZatvaranja($aktivnoParam=1) {

		$db = new db();
		$mysql = $db->mysql;
		
		$sql = $mysql->prepare("SELECT id, stranka, lokacijaPocetak, lokacijaKraj, vrijemeOd, vrijemeDo, uzrok, smjer, klasa, unio, datumUnosa, aktivno FROM zatvaranje WHERE zatvaranje.obrisano=0 AND zatvaranje.aktivno=? ORDER BY vrijemeDo DESC");
		$sql->bind_param('i', $aktivnoParam);
		
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows>0){

			$sql->bind_result($id, $stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $uzrok, $smjer, $klasa, $unio, $datumUnosa, $aktivno); 
			
			$polje=array();
			
			while ($sql->fetch()){ 
				$n=array("id" => $id, "stranka" => $stranka, "lokacijaPocetak" => $lokacijaPocetak, "lokacijaKraj" => $lokacijaKraj, "vrijemeOd" => $vrijemeOd, "vrijemeDo" => $vrijemeDo, "uzrok" => $uzrok, "smjer" => $smjer, "klasa" => $klasa, "unio" => $unio, "datumUnosa" => $datumUnosa, "aktivno" => $aktivno);

				$polje[]=(Object)$n;
			}
		
			return $polje;
		}
		else {
			$mysql->close();
			return array();
		}
	}


	public static function dohvatiAktualnaZatvaranja() {

		$db = new db();
		$mysql = $db->mysql;

		$sql = $mysql->prepare("SELECT id, stranka, lokacijaPocetak, lokacijaKraj, vrijemeOd, vrijemeDo, klasa, uzrok, smjer, koordinate, unio, datumUnosa, wazeUlica FROM zatvaranje WHERE zatvaranje.obrisano=0 AND zatvaranje.aktivno=1 ORDER BY vrijemeOd, zatvaranje.id DESC");
		
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows>0){

			$sql->bind_result($id, $stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $unio, $datumUnosa, $wazeUlica); 
			
			$polje=array();
			
			while ($sql->fetch()){ 
			
				$now = new DateTime();
				$givenDate = new DateTime($vrijemeDo);

				if ($givenDate <= $now) {
					
					$now->add(new DateInterval('P1D'));
					
					$vrijemeDo=$now->format('Y-m-d')." ".$givenDate->format('H:i:s');

					$sql2 = $mysql->prepare('UPDATE zatvaranje SET vrijemeDo=? WHERE id=? AND obrisano=0');
					$sql2->bind_param('si', $vrijemeDo, $id);
					
					if (!$sql2->execute()){
						continue;
					}

				} 

				
				$n=array("id" => $id, "stranka" => $stranka, "lokacijaPocetak" => $lokacijaPocetak, "lokacijaKraj" => $lokacijaKraj, "vrijemeOd" => $vrijemeOd, "vrijemeDo" => $vrijemeDo, "klasa" => $klasa, "uzrok" => $uzrok, "smjer" => $smjer, "koordinate" => $koordinate, "unio" => $unio, "datumUnosa" => $datumUnosa, "wazeUlica" => $wazeUlica);


				$polje[]=(Object)$n;
			}
		
			return $polje;
		}
		else {
			$mysql->close();
			return array();
		}
	}

	
	public static function dohvatiZatvaranje($id) {

		$db = new db();
		$mysql = $db->mysql;

		
        $sql = $mysql->prepare("SELECT stranka, lokacijaPocetak, lokacijaKraj, vrijemeOd, vrijemeDo, klasa, uzrok, smjer, koordinate, unio, datumUnosa, aktivno  FROM zatvaranje WHERE zatvaranje.obrisano=0 AND zatvaranje.id=?");
        $sql->bind_param('i', $id);
		
		$sql->execute();
		$sql->store_result();

		if ($sql->num_rows==1){

			$sql->bind_result($stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $unio, $datumUnosa, $aktivno); 
			$sql->fetch();

            $z=array("id" => $id, "stranka" => $stranka, "lokacijaPocetak" => $lokacijaPocetak, "lokacijaKraj" => $lokacijaKraj, "vrijemeOd" => $vrijemeOd, "vrijemeDo" => $vrijemeDo, "klasa" => $klasa, "uzrok" => $uzrok, "smjer" => $smjer, "koordinate" => $koordinate, "unio" => $unio, "datumUnosa" => $datumUnosa, "aktivno" => $aktivno);

            return (Object)$z;

			
		}
		else {
			$mysql->close();
			return false;
		}
	}

	




	/* INSERT, UPDATE, DELETE */

	public static function pohraniZatvaranje($stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $wazeUlica, $aktivno){
		
		$db = new db();
		$mysql = $db->mysql;

		$dateTime = date("Y-m-d H:i:s", time());
		$mysql->begin_transaction();
		

		$sql = $mysql->prepare('INSERT INTO zatvaranje (stranka, lokacijaPocetak, lokacijaKraj, wazeUlica, vrijemeOd, vrijemeDo, klasa, smjer, koordinate, unio, datumUnosa, aktivno, uzrok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)');
		$sql->bind_param('sssssssisiii', $stranka, $lokacijaPocetak, $lokacijaKraj, $wazeUlica, $vrijemeOd, $vrijemeDo, $klasa, $smjer, $koordinate, $_SESSION["id"], $aktivno, $uzrok);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return false;
		}
		
		$mysql->commit();
		$mysql->close();
		return true;
		

	}	

	
	public static function urediZatvaranje($id, $stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $wazeUlica, $aktivno) {
		
		$db = new db();
		$mysql = $db->mysql;

		$dateTime = date("Y-m-d H:i:s", time());
		$mysql->begin_transaction();

		$sql = $mysql->prepare('UPDATE zatvaranje SET stranka=?, lokacijaPocetak=?, lokacijaKraj=?, wazeUlica=?, vrijemeOd=?, vrijemeDo=?, klasa=?, smjer=?, koordinate=?, uredio=?, datumUredjivanja=?, aktivno=?, uzrok=? WHERE id=? AND obrisano=0');
		$sql->bind_param('sssssssisisiii', $stranka, $lokacijaPocetak, $lokacijaKraj, $wazeUlica, $vrijemeOd, $vrijemeDo, $klasa, $smjer, $koordinate, $_SESSION["id"], $dateTime, $aktivno, $uzrok, $id);
		
		if (!$sql->execute()){
			$mysql->rollback();
			$mysql->close();
			return false;
		}
		
		$mysql->commit();
		$mysql->close();
		return true;
		

	}	

	
	public static function deaktiviraj($id) {
		
		$db = new db();
		$mysql = $db->mysql;

		
		$sql = $mysql->prepare('UPDATE zatvaranje SET aktivno=-1, deaktivirao=?, datumDeaktivacije=NOW() WHERE id=? AND aktivno=1');
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


	public static function aktiviraj($id) {
		
		$db = new db();
		$mysql = $db->mysql;

		
		$sql = $mysql->prepare('UPDATE zatvaranje SET aktivno=1, aktivirao=?, datumAktivacije=NOW() WHERE id=? AND aktivno=0');
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

	public static function obrisiZatvaranje($id) {
		
		$db = new db();
		$mysql = $db->mysql;

		
		$sql = $mysql->prepare('UPDATE zatvaranje SET obrisano=1, obrisao=?, datumBrisanja=NOW() WHERE id=?');
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