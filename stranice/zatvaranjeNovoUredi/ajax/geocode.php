

<?php

include "../../../config.inc.php";

function getCoordinates($address) {

    $address="Zagreb, ".$address;
    
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json";


    $proxy = PROXYURL; 

    $ch = curl_init($url);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    if ($proxy){
        curl_setopt($ch, CURLOPT_PROXY, $proxy); 
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    if (CURLREFERER != "")
        curl_setopt($ch, CURLOPT_REFERER, CURLREFERER);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    } else {
        curl_close($ch);

        $polje=json_decode($response, true);

        foreach ($polje as $p){
            if (str_contains($p["display_name"], "Grad Zagreb")){
                return array("lat"=>$p["lat"], "lon"=>$p["lon"]);
                die();
            }
        }

        $coord=json_decode($response, true)[0];
        return array("lat"=>$coord["lat"], "lon"=>$coord["lon"]);
        die();
        
    }
}

$coordinates = getCoordinates($_POST["adresa"]);
echo json_encode($coordinates);
die();


?>