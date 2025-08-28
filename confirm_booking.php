<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - CONFIRM_BOOKING </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); ?>

    <?php
    if(!isset($_GET['id']) || $settings_r['Shutdown'] == true){
        redirect('rooms.php');
    }elseif(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
        redirect('rooms.php');
    }

    // filter and get room and user data

    $data = filteration($_GET);

    $room_res = select("SELECT *FROM `rooms` WHERE `id` = ? AND `Status` = ? AND `removed` = ?",[$data['id'],1,0],'iii');

    if(mysqli_num_rows($room_res) == 0){
        redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);

    $_SESSION['room'] = [
        "id" => $room_data['id'],
        "name" => $room_data['Name'],
        "price" => $room_data['Price'],
        "payment" => null,
        "available" => false,
    ];
    
    $user_res = select("select *from `user_crud` where `id`=? limit 1",[$_SESSION['uId']],"i");
    $user_data = mysqli_fetch_assoc($user_res);
    ?>
    

<div class="container">
    <div class="row">

        <div class="col-12 my-5 mb-4 px-4">
            <h2 class="fw-bold">CONFIRM BOOKING</h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                <span class="text-secondary"> > </span>
                <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
            </div> 
        </div>

        <div class="col-lg-7 col-md-12 px-4">
        
            <?php
                $room_thumb = ROOMS_IMG_PATH."thumb.png";

                $thumn_q = mysqli_query($con,"SELECT * FROM `rooms_image`
                WHERE `Room_id` = '$room_data[id]' 
                AND `Thumb` = '1'");

                if(mysqli_num_rows($thumn_q) > 0){
                    $thumb_res = mysqli_fetch_assoc($thumn_q);
                    $room_thumb = ROOMS_IMG_PATH.$thumb_res['Image'];
                }

                echo<<<data
                    <div class="card p-3 shadow-sm rounded">
                        <img src="$room_thumb" class="img-fluid rounded mb-3">
                        <h5>$room_data[Name]</h5>  
                        <h6>₹$room_data[Price] per Night</h6>  
                    </div>
                data;
            ?>

        </div>

        <div class="col-lg-5 col-md-12 px-4">
            <div class="card mb-4 border-0 shadow-sw rounded-3">
                <div class="card-body">
                  <form action="pay_now.php" id="booking_form" method="POST">
                    <h6 class="mb-2">BOOKING DETAILS</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" type="text" value="<?php echo $user_data['Name'] ?>" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone.No.</label>
                            <input name="phone" type="text" value="<?php echo $user_data['Phone'] ?>" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['Address'] ?></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Check-In</label>
                            <input name="checkin" onchange="check_availability()" type="date" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Check-Out</label>
                            <input name="checkout" onchange="check_availability()" type="date" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-12">
                            <div class="spinner-border text-success mb-3 d-none" id="info_loader" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h6 class="mb-3 text-danger" id="pay_info">Provide check-in & check-out date!</h6>
                            <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>Pay Now</button>
                        </div>
                    </div>  
                  </form>
                </div>
            </div>                    
        </div>

    </div>
</div>

   
<!-- Footer -->
    <?php require('inc/footer.php'); ?>

        <script>
            let booking_form = document.getElementById('booking_form');
            let info_loader  = document.getElementById('info_loader');
            let pay_info  = document.getElementById('pay_info');

            function check_availability(){
                let checkin_val = booking_form.elements['checkin'].value;
                let checkout_val = booking_form.elements['checkout'].value;

                booking_form.elements['pay_now'].setAttribute('disabled',true);

                if(checkin_val !='' && checkout_val !=''){

                    pay_info.classList.add('d-none');
                    pay_info.classList.replace('text-dark','text-danger');
                    info_loader.classList.remove('d-none');

                    let data = new FormData();

                    data.append('check_availability','');
                    data.append('check_in',checkin_val);
                    data.append('check_out',checkout_val);

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST","Ajex/confirm_booking.php",true);

                    xhr.onload = function(){
                       let data = JSON.parse(this.responseText); 
                       if(data.status == 'check_in_out_equal'){
                        pay_info.innerText = "You can not check-out on the same day!";
                       }else if(data.status == 'check_out_earlier'){
                        pay_info.innerText = "Check-out date is earlier check-in date!";
                       }else if(data.status == 'check_in_earlier'){
                        pay_info.innerText = "Check-in date is earlier then today's date!";
                       }else if(data.status == 'unavailable'){
                        pay_info.innerText = "Room not available for this check-in date!";
                       }else{
                        pay_info.innerHTML = "No. of Days: "+data.days+"<br> Total Ammount to Pay: ₹"+data.payment;
                        pay_info.classList.replace('text-danger','text-dark');
                        booking_form.elements['pay_now'].removeAttribute('disabled');
                       }

                       pay_info.classList.remove('d-none');
                       info_loader.classList.add('d-none');
                    }

                    xhr.send(data);
                }

            }
        </script>
</body>
</html>