<?php 

if (!defined("INDEX") || INDEX!=1) die("Direktan pristup zabranjen");

if (!$_SESSION["id"]) die("Morate biti prijavljeni");

if ($_SESSION["razina"]>1) die("Nemate ovlasti");


?>

<style>
    .card.main .table-responsive {
        min-height: 350px;
    }
</style>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="/includes/sweetalert2/bootstrap-4.min.css">
<script src="/includes/sweetalert2/sweetalert2.min.js"></script>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Korisnici</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card main">
        <div class="card-header">
            <a href="/korisnikNoviUredi" target="_blank"><button type="button" class="btn btn-primary" style="float: right;"><span class="tf-icons bx bx-plus me-1"></span>Novi korisnik</button></a>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table datatables">
            <thead>
                <tr>
                <th>ID</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>E-mail</th>
                <th>Korisničko ime</th>
                <th>Razina</th>
                <th></th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php

                    foreach (Korisnik::dohvatiKorisnike() as $kor){
                                                
                        echo '<tr data-id="'.$kor->id.'"><td>'.$kor->id.'</td><td>'.$kor->ime.'</td><td>'.$kor->prezime.'</td><td>'.$kor->email.'</td><td>'.$kor->username.'</td><td>'.$kor->razinaNaziv.'</td><td><div class="d-inline-block"><a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow me-1" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded bx-md"></i></a><div class="dropdown-menu dropdown-menu-end m-0"><a href="/korisnikNoviUredi/?id='.$kor->id.'" class="dropdown-item">Uredi</a><a href="/promjenaLozinke/?id='.$kor->id.'" class="dropdown-item">Promjena lozinke</a><div class="dropdown-divider"></div><a href="javascript:;" class="dropdown-item text-danger Obriši-record" onclick="obrisiKorisnika('.$kor->id.'); return false;">Obriši</a></div></div></td></tr>';
                    }
                ?>    
            
           
                
                
            
                
            </tbody>
            </table>
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

    $( document ).ready(function() {
        // Handler for .ready() called.
        $('.datatables').DataTable({
            language: {
                url: '/includes/datatables/hr.json'
            },
            lengthMenu: [[10, 20, 50, 100],[10, 20, 50, 100]],
            pageLength: 50,
            order: [[2, 'asc'], [1, 'asc']]
        });
    });


    function obrisiKorisnika(id){

        Swal.fire({
            title: 'Obriši korisnika?',
            text: "Jeste li sigurni da želite obrisati odabranog korisnika?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#5f61e6',
            cancelButtonColor: '#e6381a',
            cancelButtonText: "Ne, odustani!",
            confirmButtonText: 'Da, obriši!'
        }).then((result) => {
            if (result.isConfirmed) {

            
                $.ajax({
                    url: "/stranice/korisnici/ajax/obrisiKorisnika.php",
                    type: "post",
                    data: {
                        id: id
                    } ,
                    success: function (response) {

                        if (response){
                            $(".table.datatables").DataTable()
                                .row("tr[data-id='"+id+"']")
                                .remove()
                                .draw();

                        }
                        else {

                            Swal.fire(
                                'Greška!',
                                'Greška prilikom brisanja.',
                                'error'
                            )
                            
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus+" "+errorThrown);
                        console.log(jqXHR);
                    
                        Swal.fire(
                                'Greška!',
                                'Greška prilikom brisanja 2.',
                                'error'
                            )

                        return false;
                    }
                });

            }
        })

    }

</script>

    