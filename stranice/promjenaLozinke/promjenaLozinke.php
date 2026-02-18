<?php

if ($_SESSION["razina"]>1 && $_GET["id"]) die("Nemate ovlasti");

if ($_POST["novaLozinka"]){

    $staraLozinka=trim($_POST["staraLozinka"]) == "" ? NULL : trim($_POST["staraLozinka"]);
    $novaLozinka=trim($_POST["novaLozinka"]) == "" ? NULL : trim($_POST["novaLozinka"]);
    $novaLozinkaPonovo=trim($_POST["novaLozinkaPonovo"]) == "" ? NULL : trim($_POST["novaLozinkaPonovo"]);

    if (($_SESSION["razina"]>1 && $staraLozinka==NULL) || $novaLozinka==NULL || $novaLozinkaPonovo==NULL){
        $uspjesno=-1;
    }
    else {

        if ($novaLozinka != $novaLozinkaPonovo){
            $uspjesno=-3;
        }
        else {
            if ($_SESSION["razina"]==1){
                if ($_GET["id"]){
                    if (Korisnik::promijeniLozinku($_GET["id"],$novaLozinka)){
                        $uspjesno=1;
                    }
                    else {
                        $uspjesno=-2;
                    } 

                }
                else {
                    if (Korisnik::promijeniLozinku($_SESSION["id"],$novaLozinka)){
                        $uspjesno=1;
                    }
                    else {
                        $uspjesno=-2;
                    } 
                }
            }
            else {
                if (Korisnik::promijeniLozinkuProfila($_SESSION["id"],$staraLozinka,$novaLozinka)){
                    $uspjesno=1;
                }
                else {
                    $uspjesno=-2;
                } 
            }
            
        }
        
    }
}


?>



                      
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Postavljanje nove lozinke</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="card-header">
            <?php if ($uspjesno == 1){ ?>
                <div class="alert alert-success alert-dismissible" role="alert">Uspješno pohranjeno!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == -1 ){ ?>
                <div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == -2 ){ ?>  
                <div class="alert alert-danger alert-dismissible" role="alert">Greška pri pohrani!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } else if ($uspjesno == -3 ){ ?>  
                <div class="alert alert-danger alert-dismissible" role="alert">Nova lozinka i nova lozinka ponovo nisu jednake!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php } ?>

            
        </div>
        
        <div class="card-body">
            <form method="POST" id="forma">
                
                <?php if ($_SESSION["razina"]>1) {?>
                <div class="mb-3 row">
                    <label for="staraLozinka" class="col-md-2 col-form-label">Postojeća lozinka: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="password" name="staraLozinka" value="" id="staraLozinka">
                    </div>
                </div>
                <?php } ?>

                <div class="mb-3 row">
                    <label for="novaLozinka" class="col-md-2 col-form-label">Nova lozinka: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="password" name="novaLozinka" value="" id="novaLozinka">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="novaLozinkaPonovo" class="col-md-2 col-form-label">Nova lozinka ponovo: *</label>
                    <div class="col-md-10">
                        <input class="form-control required" type="password" name="novaLozinkaPonovo" value="" id="novaLozinkaPonovo">
                    </div>
                </div>

              
               
                
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
            $(".card-header").prepend('<div class="alert alert-danger alert-dismissible" role="alert">Popuni obavezna polja!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }

        var novaLozinka=$("input[name='novaLozinka']").val();
        var novaLozinkaPonovo=$("input[name='novaLozinkaPonovo']").val();

        if (novaLozinka!=novaLozinkaPonovo){
            $(".card-header").prepend('<div class="alert alert-danger alert-dismissible" role="alert">Nova lozinka i nova lozinka ponovo nisu jednake!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
            window.scrollTo(0,0);
            return false;
        }

        $("#forma").submit();
    }


</script>
    