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

    input:read-only, input:read-only:focus{
        background-color: rgba(34, 48, 62, .06);
    }
</style>
<?php


if ($_POST["ime"]){

    $ime=trim($_POST["ime"]) == "" ? NULL : trim($_POST["ime"]);
    $prezime=trim($_POST["prezime"]) == "" ? NULL : trim($_POST["prezime"]);
    $email=trim($_POST["email"]) == "" ? NULL : trim($_POST["email"]);

    $razina=trim($_POST["razina"]) == "" ? NULL : trim($_POST["razina"]);

    
    if ($_SESSION["razina"]>1)
        $razina=2;
    

    if ($ime==NULL || $prezime==NULL || $email==NULL || $razina==NULL){
        $uspjesno=-1;
    }
    else {
        if ($_GET["id"]){
            if (Korisnik::urediKorisnika($_GET["id"], $ime, $prezime, $email, $razina)){
                $uspjesno=1;
            }
            else {
                $uspjesno=-2;
            } 
        }
        else {
            $status=Korisnik::pohraniKorisnika($ime, $prezime, $email, $razina);
            //echo $status;
            if ($status!==false){
                $uspjesno=1;
            }
            else {
                $uspjesno=-2;
            } 
        }
    }
}

if ($_GET["id"]){
    $kor=Korisnik::dohvatiKorisnika($_GET["id"]);
}

    
?>



                      
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4"><?php echo $_GET["id"] ? "Uredi korisnika - ".$kor->ime."  ".$kor->prezime : "Novi korisnik"; ?></h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="card-header">
            <?php if ($uspjesno == 1 && !$_GET["id"]){ ?>
                <div class="alert alert-success alert-dismissible" role="alert">Uspješno kreiran korisnik<br><br>Korisničko ime: <?php echo $status["username"]?><br>Lozinka: <?php echo $status["password"]; ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == 1 && $_GET["id"]){ ?>
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
                    <label for="ime" class="col-md-2 col-form-label">Ime: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="text" name="ime" value="<?php echo $kor->ime; ?>" id="ime">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="prezime" class="col-md-2 col-form-label">Prezime: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="text" name="prezime" value="<?php echo $kor->prezime; ?>" id="prezime">
                    </div>
                </div>
                
                <div class="mb-3 row">
                    <label for="email" class="col-md-2 col-form-label">Email: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="text" name="email" value="<?php echo $kor->email; ?>" id="email">
                    </div>
                </div>

                <?php if ($_SESSION["razina"]==1){ ?>

                <div class="mb-3 row">
                    <label for="razina" class="col-md-2 col-form-label">Razina: *</label>
                    <div class="col-md-10">
                        <select class="form-select required" name="razina">
                            <option value="1" <?php if ($kor->razina==1) echo "selected"; ?>>Admin</option>
                            <option value="2" <?php if ($kor->razina==2) echo "selected"; ?>>Grad Zagreb</option>
                            <option value="3" <?php if ($kor->razina==3) echo "selected"; ?>>ZG ceste</option>
                        </select>
                    </div>
                </div>
                <?php } ?>

                <?php if ($_GET["id"]){ ?>
                <div class="mb-3 row">
                    <label for="username" class="col-md-2 col-form-label">Korisničko ime: </label>
                    <div class="col-md-10">
                        <input class="form-control" type="text" disabled name="username" value="<?php echo $kor->username; ?>" id="username">
                    </div>
                </div>
                <?php } ?>

                
                <button type="button" class="btn btn-primary" onclick="provjeraForme(); return false;">Pohrani</button>
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


<script>


    

    function provjeraForme(){


        $(".alert").remove();
        
        if (!validateForm()){
            $(".card-header").prepend(' <div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }



        $("#forma").submit();
    }

    
</script>
    