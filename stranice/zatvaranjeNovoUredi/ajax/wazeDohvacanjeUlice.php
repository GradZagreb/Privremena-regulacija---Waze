<?php

include "../../../config.inc.php";

function wazeResverseGeocoding($lat, $lon){
    $url = "https://www.waze.com/row-partnerhub-api/waze-map/streetsInfo?lat=$lat&lon=$lon&token=".WAZE_TOKEN;

    $proxy = PROXYURL;

    // cURL inicijalizacija
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Vraća rezultat umesto da ga ispiše
    if ($proxy){
        curl_setopt($ch, CURLOPT_PROXY, $proxy); // Postavlja proxy
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); // Tip proxyja: HTTP, HTTPS, SOCKS4, SOCKS5
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    if (CURLREFERER){
        curl_setopt($ch, CURLOPT_REFERER, CURLREFERER);
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    } else {
        curl_close($ch);
        $polje=json_decode($response,true)["result"];
        $ulice=array();
        foreach ($polje as $ul){
            $ulice[]=$ul["names"][0];
        }
        $ulice=array_unique($ulice);
        $povrat=array();
        foreach ($ulice as $ul){
            $povrat[]=$ul;
        }   
        return json_encode($povrat);
    }

}

$koordinate=$_POST["koordinate"];
echo wazeResverseGeocoding($koordinate[0], $koordinate[1]);
die();

?>