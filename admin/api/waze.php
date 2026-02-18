<?php

include "../../config.inc.php";

if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=WAZE_USER || $_SERVER['PHP_AUTH_PW']!=WAZE_PASS) {
    echo "Please authenticate";
    die();
} else {
    
    spl_autoload_register(function ($class) { 
        require_once("../../includes/classes/" . mb_strtolower($class, 'UTF-8') . ".class.php");
    });   
    
    // primjer JSON-a za Waze
    /*
    {
        "incidents": [
            {
                +"id": "3f4r45ff233",
                +"creationtime": "2024-07-04T13:31:17-04:00",
                "updatetime": "2024-11-17T04:40:41-05:00",
                "description": "Complete road closure due to road works",
                +"street": "N Liberty St",
                +"direction": "BOTH_DIRECTIONS",
                +"polyline": "42.1601432984533 -119.3525208937842 42.1781676611244 -119.35679623266",
                +"starttime": "2024-06-05T00:01:00-04:00",
                +"endtime": "2024-11-22T15:30:00-05:00",
                +"type": "ROAD_CLOSED"
            },
            {
                "id": "zxf3kvmrpf",
                "creationtime": "2024-08-04T13:31:30-04:00",
                "updatetime": "2024-12-17T04:40:41-05:00",
                "description": "St Johns Sdrd from William Graham to Woodbine in the Town of Aurora. Closed until Dec 31. ",
                "type": "ROAD_CLOSED",
                "subtype": "ROAD_CLOSED_CONSTRUCTION",
                "street": "St John's Sideroad",
                "direction": "BOTH_DIRECTIONS",
                "polyline": "44.02712 -99.43131 44.023011 -99.363349 44.02712 -99.43131 44.0343021 -99.399426",
                "starttime": "2024-04-18T00:01:00-04:00",
                "endtime": "2024-12-31T23:59:00-05:00"
            }
        ]
    }
    */

    $povrat=array();
    foreach (Zatvaranje::dohvatiAktualnaZatvaranja() as $zat){
        $dateTime = new DateTime($zat->datumUnosa);
        $iso8601 = $dateTime->format(DateTime::ATOM); // DateTime::ATOM odgovara ISO 8601 formatu

        $inc=array();
        $inc["id"]=$zat->id;
        $inc["creationtime"]=$iso8601;
        $inc["type"]="ROAD_CLOSED";

        if ($zat->uzrok==2)
            $inc["subtype"]="ROAD_CLOSED_EVENT";
        else
            $inc["subtype"]="ROAD_CLOSED_CONSTRUCTION";
        
        $kord=json_decode($zat->koordinate);

        $koordinate=array();
        foreach($kord as $ko){
            $koordinate[]=$ko[0]." ".$ko[1];
        }
        
        $inc["polyline"]= implode(" ", $koordinate);
        $inc["street"]=$zat->wazeUlica;

        if ($zat->smjer==1)
            $inc["direction"]="ONE_DIRECTION";
        else if ($zat->smjer==2)
            $inc["direction"]="BOTH_DIRECTIONS";

        $dateTime = new DateTime($zat->vrijemeOd);
        $iso8601 = $dateTime->format(DateTime::ATOM); // DateTime::ATOM odgovara ISO 8601 formatu

        $inc["starttime"]=$iso8601;

        $dateTime = new DateTime($zat->vrijemeDo);
        $iso8601 = $dateTime->format(DateTime::ATOM); // DateTime::ATOM odgovara ISO 8601 formatu

        $inc["endtime"]=$iso8601;

        $povrat["incidents"][]=$inc;
    }


    echo json_encode($povrat);
}


?>