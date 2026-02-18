<?php

if ($_GET["id"])
    $zat=Zatvaranje::dohvatiZatvaranje($_GET["id"]);
else if ($_GET["predlozak"])
    $zat=Zatvaranje::dohvatiZatvaranje($_GET["predlozak"]);

if (!in_array($zat->aktivno, [0,1]) && $_GET["id"]) die("Ne smijete uređivati navedeno zatvaranje jer je deaktivirano");

?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script src="/includes/moment/moment-with-locales.js"></script>
<script src="/includes/dateTimePicker/bootstrap4/bootstrap-datetimepicker.js"></script>
<link rel="stylesheet" href="/includes/dateTimePicker/bootstrap4/bootstrap-datetimepicker.css">

<style>
    
    .decade {
        font-size: 13px;
    }

    .bootstrap-datetimepicker-widget table td span {
        width: 80px;
    }

    .hide {
        display: none;
    }

    .ulica {
        cursor: pointer;
        margin-bottom: 5px;
        color: #5f61e6;
    }
</style>
<?php

if ($_POST["lokacijaPocetak"]){

    $lokacijaPocetak=trim($_POST["lokacijaPocetak"]) == "" ? NULL : trim($_POST["lokacijaPocetak"]);
    $lokacijaKraj=trim($_POST["lokacijaKraj"]) == "" ? NULL : trim($_POST["lokacijaKraj"]);
    $vrijemeOd=trim($_POST["vrijemeOd"]) == "" ? NULL : date_format(date_create_from_format("d.m.Y. H:i",trim($_POST["vrijemeOd"])),"Y-m-d H:i");
    $vrijemeDo=trim($_POST["vrijemeDo"]) == "" ? NULL : date_format(date_create_from_format("d.m.Y. H:i",trim($_POST["vrijemeDo"])),"Y-m-d H:i");
    $uzrok=trim($_POST["uzrok"]) == "" ? NULL : trim($_POST["uzrok"]);
    $smjer=trim($_POST["smjer"]) == "" ? NULL : trim($_POST["smjer"]);
    $koordinate=trim($_POST["koordinate"]) == "" ? NULL : trim($_POST["koordinate"]);
    $wazeUlica=trim($_POST["wazeUlica"]) == "" ? NULL : trim($_POST["wazeUlica"]);

    $aktivno = $_POST["aktivno"] ? 1 : 0;

    $stranka=NULL;
    $klasa=NULL;

    if ($lokacijaPocetak==NULL || $lokacijaKraj==NULL || $vrijemeOd==NULL || $vrijemeDo==NULL || $koordinate==NULL){
        $uspjesno=-1;
    }
    else {
        if ($_GET["id"]){
            if (Zatvaranje::urediZatvaranje($_GET["id"], $stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $wazeUlica, $aktivno)){
                $uspjesno=1;
            }
            else {
                $uspjesno=-2;
            } 
        }
        else {
            if (Zatvaranje::pohraniZatvaranje($stranka, $lokacijaPocetak, $lokacijaKraj, $vrijemeOd, $vrijemeDo, $klasa, $uzrok, $smjer, $koordinate, $wazeUlica, $aktivno)){
                $uspjesno=1;
            }
            else {
                $uspjesno=-2;
            } 
        }
    }
}


if ($_GET["id"])
    $zat=Zatvaranje::dohvatiZatvaranje($_GET["id"]);
else if ($_GET["predlozak"])
    $zat=Zatvaranje::dohvatiZatvaranje($_GET["predlozak"]);


?>



                      
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><?php echo $_GET["id"] ? "Uredi postojeće zatvaranje" : "Novo zatvaranje prometnice"; ?></h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="card-header">
            <?php if ($uspjesno == 1){ ?>
                <div class="alert alert-success alert-dismissible" role="alert">Uspješno pohranjeno!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == -1 ){ ?>
                <div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == -2 ){ ?>  
                <div class="alert alert-danger alert-dismissible" role="alert">Greška pri pohrani!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } ?>

            
        </div>
        
        <div class="card-body">
            <form method="POST" id="forma">

                <div class="mb-3 row">
                    <label for="lokacijaPocetak" class="col-md-2 col-form-label">Ruta/lokacija početak: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="text" name="lokacijaPocetak" value="<?php echo $zat->lokacijaPocetak; ?>" id="lokacijaPocetak">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="lokacijaKraj" class="col-md-2 col-form-label">Ruta/lokacija kraj: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="text" name="lokacijaKraj" value="<?php echo $zat->lokacijaKraj; ?>" id="lokacijaKraj">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="vrijemeOd" class="col-md-2 col-form-label">Vrijeme od: *</label>
                    <div class="col-md-10">
                        <input class="form-control required datetimepicker" type="text" name="vrijemeOd" value="<?php echo $zat->vrijemeOd ? date_format(date_create_from_format("Y-m-d H:i:s",$zat->vrijemeOd),"d.m.Y. H:i") : ""; ?>" id="">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="vrijemeDo" class="col-md-2 col-form-label">Vrijeme do: *</label>
                    <div class="col-md-10">
                        <input class="form-control required datetimepicker" type="text" name="vrijemeDo" value="<?php echo $zat->vrijemeDo ? date_format(date_create_from_format("Y-m-d H:i:s",$zat->vrijemeDo),"d.m.Y. H:i") : ""; ?>" id="">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="aktivno" class="col-md-2 col-form-label">Aktivno:</label>
                    <div class="col-md-10 check-parent">
                        <input class="form-check-input" type="checkbox" name="aktivno" <?php echo $zat->aktivno == 1 || !$_GET["id"] && !$_GET["predlozak"] ? "checked" : ""; ?> id="">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="uzrok" class="col-md-2 col-form-label">Uzrok: *</label>
                    <div class="col-md-10">
                        
                        <select class="form-select required" name="uzrok">
                            <option value="1" <?php if ($zat->uzrok==1) echo "selected"; ?>>Radovi</option>
                            <option value="2" <?php if ($zat->uzrok==2) echo "selected"; ?>>Događaj</option>
                        </select>
                        
                        
                    </div>
                </div>


                <div class="mb-3 row">
                    <label for="smjer" class="col-md-2 col-form-label">Smjer zatvaranja: *</label>
                    <div class="col-md-10">
                        
                        <select class="form-select required" name="smjer">
                            <option value="1" <?php if ($zat->smjer==1) echo "selected"; ?>>Jednosmjerno</option>
                            <option value="2" <?php if ($zat->smjer==2) echo "selected"; ?>>Dvosmjerno</option>
                        </select>
                        <p style="font-style: italic; font-size: 14px; margin-top: 10px;">Ako je zatvaranje jednosmjerno, u sljedećem koraku ćete u tom smjeru morati povlačiti liniju zatvaranja na mapi. Ako je zatvaranje dvosmjerno, svejedno je u kojem smjeru ćete povlačiti liniju zatvaranja.</p>
                        
                    </div>
                </div>

                <button type="button" class="btn btn-info btnPrikaziMapu mb-4 <?php if ($_GET["id"] || $_GET["predlozak"]) echo "hide";?>" onclick="prikaziMapu(); return false;">Prikaži mapu</button>

                <div class="mb-3 row mapa <?php if (!$_GET["id"] && !$_GET["predlozak"]) echo "hide";?>">
                    <label for="klasa" class="col-md-2 col-form-label">Mapa: </label>
                    <div class="col-md-10">
                        <p style="font-style: italic;">Mišem na mapi označite rutu privremenog zatvaranja na način da klikanjem "iscrtate" liniju zatvaranja. Isto tako, odaberite je li zatvaranje jednosmjerno ili dvosmjerno. Ako je zatvaranje jednosmjerno, u tom smjeru morate povlačiti liniju zatvaranja na mapi.</p>
                        <div><button type="button" class="btn btn-warning btnPonistiLinije mb-4" onclick="ponistiLinije(); return false;">Poništi oznake</button></div>
                        <div id="map" style="height: 500px; cursor: default !important;"></div>
                    </div>
                </div>

                <input type="hidden" name="koordinate" value="" id="koordinate">
                <input type="hidden" name="wazeUlica" value="" id="wazeUlica">
                
            <button type="button" class="btn btn-primary btnPohrani <?php if (!$_GET["id"] && !$_GET["predlozak"]) echo "hide";?>" onclick="provjeraForme(); return false;">Pohrani</button>
            </form>
        </div>
    </div>
    <!--/ Basic Bootstrap Table -->


    
</div>
<!-- / Content -->

<!-- Footer -->
<footer class="content-footer footer bg-footer-theme">
    
</footer>
<!-- / Footer -->

<div class="content-backdrop fade"></div>


<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel1">Waze ulica</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col mb-3">
                Napomena: Waze može prikazati zatvaranje ceste samo ako se radi o jednom nazivu ulice duž cijelog zatvaranja.<br><br>
                Odaberite jednu od ponuđenih ulica na koju se zatvaranje odnosi:
                </div>
                
               
            </div>

            <div class="row">
                <div class="col mb-3 ulicePopis">
                   
                </div>
                
               
            </div>
       
        </div>
        <div class="modal-footer">
          
        </div>
    </div>
    </div>
</div>



<script>


    $('.datetimepicker').datetimepicker({
        // Formats
        // follow MomentJS docs: https://momentjs.com/docs/#/displaying/format/
        format: 'DD.MM.YYYY. HH:mm',
        locale: 'hr',
        sideBySide: true,
        showClear: true,
        ignoreReadonly: true,
        showClose: true,
        showTodayButton: true,

        // Your Icons
        // as Bootstrap 4 is not using Glyphicons anymore
        icons: {
            time: 'fa-regular fa-clock',
            date: 'fa-solid fa-calendar',
            up: 'fa-solid fa-chevron-up',
            down: 'fa-solid fa-chevron-down',
            previous: 'fa-solid fa-chevron-left',
            next: 'fa-solid fa-chevron-right',
            today: 'fa-solid fa-check',
            clear: 'fa-solid fa-trash',
            close: 'fa-solid fa-times'
        }
    });

    // Inicijalizacija mape
    var map = L.map('map').setView([45.8150, 15.9819], 13); // Koordinate Zagreba

        
    // Dodavanje OSM pločica
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var drawnCoordinates = [];
    var polyline;

    // Dodavanje događaja klika za crtanje linije
    map.on('click', function(e) {
        var coord = [e.latlng.lat, e.latlng.lng];
        drawnCoordinates.push(coord);

        if (polyline) {
            map.removeLayer(polyline);
        }
        polyline = L.polyline(drawnCoordinates, { color: 'red' }).addTo(map);
    });

    

    function geocode(adresa) {
    
        $.ajax({
            url: "/stranice/zatvaranjeNovoUredi/ajax/geocode.php",
            type: "post",
            data: {
                adresa: adresa
            } ,
            success: function (response) {
                var kord=JSON.parse(response);
                map.setView([kord.lat, kord.lon], 18); // Koordinate Zagreba
                $(".mapa").removeClass("hide");
                map.invalidateSize();
                $(".btnPrikaziMapu").addClass("hide");
                $(".btnPohrani").removeClass("hide");
                Swal.close();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Greška prilikom geokodiranja!");
                return false;
            }
        });
    }

    var koordinate=<?php echo $zat->koordinate ? $zat->koordinate : "[]";?>;

    function ponistiLinije(){
        drawnCoordinates=new Array();
        koordinate=new Array();
        if (polyline) {
            map.removeLayer(polyline);
        }
        polyline = L.polyline(drawnCoordinates, { color: 'red' }).addTo(map);

    }

    
    function iscrtajLinije(){
     
        if (polyline) {
            map.removeLayer(polyline);
        }
        if (koordinate.length>0){
            polyline = L.polyline(koordinate, { color: 'red' }).addTo(map);
            map.setView([koordinate[0][0], koordinate[0][1]], 18);
            drawnCoordinates=koordinate;
        }
    }

    iscrtajLinije();

    function odabirUlice(ulica){
        $("input[name='wazeUlica']").val($(ulica).html());
        $("#forma").submit();
    }


    function provjeraForme(){
       
        $(".alert").remove();
        
        if (!validateForm()){
            $(".card-header").prepend(' <div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }

        if (drawnCoordinates.length==0){
            $(".card-header").prepend(' <div class="alert alert-danger alert-dismissible" role="alert">Na mapi odaberite koordinate zatvaranja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }

        $("input[name='koordinate']").val(JSON.stringify(drawnCoordinates));

        $.ajax({
            url: "/stranice/zatvaranjeNovoUredi/ajax/wazeDohvacanjeUlice.php",
            type: "post",
            data: {
                koordinate: drawnCoordinates[0]
            } ,
            success: function (response) {
                if (response!=false){
                    var ulice=JSON.parse(response);
                   
                    if (ulice.length>1){
                        $(".ulicePopis").html("");
                        $(ulice).each(function (index, element) {
                            $(".ulicePopis").append("<div class='ulica' onclick='odabirUlice(this); return false;'>"+element+"</div>");
                        });
                    }
                    else if (ulice.length==1){
                        $("input[name='wazeUlica']").val(ulice[0]);
                        $("#forma").submit();
                    }
                    else {
                        Swal.fire(
                            'Upozorenje!',
                            'Waze na temelju koordinate ne pronalazi naziv ulice.',
                            'warning'
                        )
                        return false;
                    }
                   
                    
                    $("#basicModal").modal("show");
                    return false;
                }
                else {
                    Swal.fire(
                        'Upozorenje!',
                        'Nije moguće dohvatiti naziv ulice putem Waze API-ja. Zatvaranje će se pohraniti, ali neće biti vidljivo na Waze platformi. Pokušajte kasnije ponovo pohraniti zatvaranje putem uređivanja.',
                        'warning'
                    )
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("Greška prilikom dohvaćanja naziva ulice s Wazea!");
                return false;
            }
        });

    }

    function prikaziMapu(){
        
        if (!validateForm()){
            $(".card-header").prepend(' <div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }


        Swal.fire({
            title: "Učitavanje mape...",
            html: "Mapa se učitava i uskoro će biti spremna za označavanje.",
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });


        var adresa = $("input[name='lokacijaPocetak']").val();

        if ($.trim(adresa)==""){
            $(".card-header").prepend(' <div class="alert alert-danger alert-dismissible" role="alert">Unesite adrese!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }

        geocode(adresa);
        
    }
    
</script>
    