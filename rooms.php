<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - ROOMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); 

    $checkin_default = "";
    $checkout_default = "";
    $adult_default = "";
    $children_default = "";
    
    if(isset($_GET['check_availibility'])){

        $frm_data = filteration($_GET);

        $checkin_default  = $frm_data['checkin'];
        $checkout_default = $frm_data['checkout'];
        $adult_default    = $frm_data['adult'];
        $children_default = $frm_data['children'];
    }

    ?>

    <div class="my-5 px-4">
      <h2 class="fw-bold h-font text-center">OUR ROOMS</h2>
      <div class="h-line bg-dark"></div> 
    </div>

<div class="container-fluid">
    <div class="row">
        
        <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                <div class="container-fluid flex-lg-column align-items-stretch">
                    <h4 class="mt-2">FILTERS</h4>
                    <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterdropdown" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filterdropdown">
                        <!-- Availibility -->
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                <span>CHECK AVAILIBILITY</span>
                                <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                            </h5>
                            <label class="form-label">Check-in</label>
                            <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>" id="checkin" onchange="chk_avail_filter()">
                            <label class="form-label">Check-out</label>
                            <input type="date" class="form-control shadow-none" value="<?php echo $checkout_default ?>" id="checkout" onchange="chk_avail_filter()">                       
                        </div>
                        <!-- facility -->
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                <span>FACILITIES</span>
                                <button id="facility_btn" onclick="facility_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                            </h5>
                            <?php
                            
                                $facilities_q = selectAll('facility');
                                while($row = mysqli_fetch_assoc($facilities_q)){
                                    echo<<<facility
                                        <div class="mb-2">
                                            <input type="checkbox" onclick="fetch_room()" name="facility" value="$row[id]" class="form-check-input shadow-none me-1" id="$row[id]">
                                            <label class="form-label" for="$row[id]">$row[Name]</label>
                                        </div> 
                                    facility;
                                }

                            ?>
                            
                        </div>
                        <!-- Guest -->
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                <span>GUESTS</span>
                                <button id="guests_btn" onclick="guest_clear()" class="btn shadow-none btn-sm text-secondary d-none">Reset</button>
                            </h5>
                            <div class="d-flex">
                                <div class="me-3">
                                    <label class="form-label">Adults</label>
                                    <input type="number" value="<?php echo $adult_default ?>" min="1" id="adults" oninput="guests_filter()" class="form-control shadow-none">
                                </div>
                                <div>
                                    <label class="form-label">Children</label>
                                    <input type="number" value="<?php echo $children_default ?>" id="children" oninput="guests_filter()" class="form-control shadow-none">
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="col-lg-9 col-md-12 px-4" id="rooms-data">
            <div class="spinner-border text-success mb-3 d-block ms-auto me-auto" id="loader" role="status">
                <span class="visually-hidden">Loading...</span>
            </div> 
        </div>
    </div>
</div>

<!-- Showing Available room data -->
<script>
    let rooms_data = document.getElementById('rooms-data');

    let checkin = document.getElementById('checkin');
    let checkout = document.getElementById('checkout');
    let chk_avail_btn = document.getElementById('chk_avail_btn');

    let adults = document.getElementById('adults');
    let children = document.getElementById('children');
    let guests_btn = document.getElementById('guests_btn');

    let facility_btn = document.getElementById('facility_btn');

    function fetch_room(){
        let chk_avail = JSON.stringify({
            checkin:  checkin.value,
            checkout: checkout.value
        });
        let guests = JSON.stringify({
            adults:  adults.value,
            children: children.value
        });

        facility_list = {"facility_data_list":[]};

        let get_facility = document.querySelectorAll('[name="facility"]:checked');
        if(get_facility.length > 0){
           get_facility.forEach((fcty)=>{
            facility_list.facility_data_list.push(fcty.value);
            });
           facility_btn.classList.remove('d-none');
        }else{
           facility_btn.classList.add('d-none'); 
        }

        facility_list = JSON.stringify(facility_list);

        let xhr = new XMLHttpRequest();
        xhr.open("GET","Ajex/rooms.php?fetch_rooms&chk_avail="+chk_avail+"&guests="+guests+"&facility_list="+facility_list,true);

        xhr.onprogress = function(){
            rooms_data.innerHTML = `
                <div class="spinner-border text-success mb-3 d-block ms-auto me-auto" id="loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div> 
            `;
        }

        xhr.onload = function(){
            rooms_data.innerHTML = this.responseText;
        }
         
        xhr.send();
    }
    // Check Availability
    function chk_avail_filter(){
        if(checkin.value != '' && checkout.value != ''){
            fetch_room();
            chk_avail_btn.classList.remove('d-none');
        }
    }
    // Reset date
    function chk_avail_clear(){
        checkin.value = '';
        checkout.value = '';
        chk_avail_btn.classList.add('d-none');
        
        fetch_room();
        
    }
    // adults & children filter
    function guests_filter(){
        if(adults.value > 0 || children.value >= 0){
            fetch_room();
            guests_btn.classList.remove('d-none');
        }
    }
    // adults & children clear
    function guest_clear(){
        adults.value  = '';
        children.value= '';     
        guests_btn.classList.add('d-none');
        fetch_room();
    }
    // facility clear
    function facility_clear(){
        let get_facility = document.querySelectorAll('[name="facility"]:checked');
        if(get_facility.length > 0){
           get_facility.forEach((fcty)=>{
            fcty.checked = false;
            });
           facility_btn.classList.add('d-none');
        fetch_room();
        }
    }
    fetch_room();   
</script>

<!-- Footer -->
    <?php require('inc/footer.php'); ?>

</body>
</html>