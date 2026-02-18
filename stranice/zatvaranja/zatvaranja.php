<style>
    .card.main .table-responsive {
        min-height: 350px;
    }

    .iconDeaktiviraj {
        font-size: 30px;
        cursor: pointer;
    }

    .sirina {
        white-space: nowrap;
    }
</style>
<?php 

?>
<!-- SweetAlert2 -->
<link rel="stylesheet" href="/includes/sweetalert2/bootstrap-4.min.css">
<script src="/includes/sweetalert2/sweetalert2.min.js"></script>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">Zatvaranja cesta</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card main">
        <div class="card-header">
            <a href="/zatvaranjeNovoUredi" target="_blank"><button type="button" class="btn btn-primary" style="float: right;"><span class="tf-icons bx bx-plus me-1"></span>Novo zatvaranje</button></a>
            
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table datatables">
            <thead>
                <tr>
                <th>ID</th>
                <th>Početak</th>
                <th>Kraj</th>
                <th>Vrijeme od</th>
                <th>Vrijeme do</th>
                <th>Smjer</th>
                <th></th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php
                    
                    $aktivno=!isset($_GET["aktivno"]) ? 1 : $_GET["aktivno"];
                    foreach (Zatvaranje::dohvatiZatvaranja($aktivno) as $zat){

                        $smjer=$zat->smjer==1 ? "Jednosmjerno" : "Dvosmjerno";
                        
                        $kopiranje='<a href="/zatvaranjeNovoUredi/?predlozak='.$zat->id.'" class="dropdown-item">Kopiranje</a>';
                        $uredi=$zat->aktivno==0 || $zat->aktivno==1 ? '<a href="/zatvaranjeNovoUredi/?id='.$zat->id.'" class="dropdown-item">Uredi</a>' : '';

                        $brisanje=$zat->aktivno==0 ? '<a href="javascript:;" class="dropdown-item text-danger Obriši-record" onclick="obrisiZatvaranje('.$zat->id.'); return false;">Obriši</a>' : "";

                        $deaktiviraj = $zat->aktivno==1 ?  '<i class="menu-icon text-danger iconDeaktiviraj tf-icons bx bx-x" onclick="deaktiviraj('.$zat->id.'); return false;"></i>' : "";

                        $aktiviraj = $zat->aktivno==0 ?  '<i class="menu-icon text-success iconDeaktiviraj tf-icons bx bx-check" onclick="aktiviraj('.$zat->id.'); return false;"></i>' : "";
                        
                        echo '<tr data-id="'.$zat->id.'">
                                <td>'.$zat->id.'</td>
                                <td>'.$zat->lokacijaPocetak.'</td>
                                <td>'.$zat->lokacijaKraj.'</td>
                                <td data-sort="'.$zat->vrijemeOd.'" class="sirina">'.date_format(date_create_from_format("Y-m-d H:i:s",trim($zat->vrijemeOd)),"d.m.Y. H:i").'</td>
                                <td data-sort="'.$zat->vrijemeDo.'" class="sirina">'.date_format(date_create_from_format("Y-m-d H:i:s",trim($zat->vrijemeDo)),"d.m.Y. H:i").'</td>
                                <td>'.$smjer.'</td>
                                <td class="sirina">
                                    '.$aktiviraj.$deaktiviraj.'
                                    <div class="d-inline-block">
                                        <a href="javascript:;" class="btn btn-icon dropdown-toggle hide-arrow me-1" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded bx-md"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            '.$uredi.$kopiranje.'
                                            
                                            <div class="dropdown-divider"></div>
                                            '.$brisanje.'
                                        </div>
                                    </div>
                                </td>
                            </tr>';
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
            columnDefs: [
                { "type": "my-custom-sort", targets: [3,4]}
            ],
            order: [[4, 'asc']]
        });

        $.fn.dataTable.ext.type.order['data-sort-pre'] = function (a) {
            return $(a).data('sort') || 0; // Ako nema atributa, koristi 0
        };

    });

   




    function obrisiZatvaranje(id){

        Swal.fire({
            title: 'Obriši zatvaranje?',
            text: "Jeste li sigurni da želite obrisati odabrano rješenje o zatvaranju?",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#5f61e6',
            cancelButtonColor: '#e6381a',
            cancelButtonText: "Ne, odustani!",
            confirmButtonText: 'Da, obriši!'
        }).then((result) => {
            if (result.isConfirmed) {

            
                $.ajax({
                    url: "/stranice/zatvaranja/ajax/obrisiZatvaranje.php",
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


    function deaktiviraj(id){

        Swal.fire({
            title: 'Deaktiviraj zatvaranje?',
            text: "Jeste li sigurni da želite deaktivirati odabrano aktivno zatvaranje?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#5f61e6',
            cancelButtonColor: '#e6381a',
            cancelButtonText: "Ne, odustani!",
            confirmButtonText: 'Da, deaktiviraj!'
        }).then((result) => {
            if (result.isConfirmed) {

            
                $.ajax({
                    url: "/stranice/zatvaranja/ajax/deaktiviraj.php",
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
                                'Greška prilikom deaktivacije.',
                                'error'
                            )
                            
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus+" "+errorThrown);
                        console.log(jqXHR);
                    
                        Swal.fire(
                                'Greška!',
                                'Greška prilikom deaktivacije 2.',
                                'error'
                            )

                        return false;
                    }
                });

            }
        })

    }


    function aktiviraj(id){

        Swal.fire({
            title: 'Aktiviraj zatvaranje?',
            text: "Jeste li sigurni da želite aktivirati odabrano zatvaranje?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#5f61e6',
            cancelButtonColor: '#e6381a',
            cancelButtonText: "Ne, odustani!",
            confirmButtonText: 'Da, aktiviraj!'
        }).then((result) => {
            if (result.isConfirmed) {

            
                $.ajax({
                    url: "/stranice/zatvaranja/ajax/aktiviraj.php",
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
                                'Greška prilikom aktivacije.',
                                'error'
                            )
                            
                        }

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log(textStatus+" "+errorThrown);
                        console.log(jqXHR);
                    
                        Swal.fire(
                                'Greška!',
                                'Greška prilikom aktivacije 2.',
                                'error'
                            )

                        return false;
                    }
                });

            }
        })

    }


</script>

    