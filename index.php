<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - HOME</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        .availibility-form{
            margin-top: -50px;
            z-index: 2;
            position: relative;
        }
        @media screen and (max-width: 575px) {
            .availibility-form{
                margin-top: 0px;
                padding: 0 35px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php require('inc/header.php'); ?>

<!-- Cerousel -->
   <div class="container-fluid px-lg-4 mt-4">
        <div class="swiper mySwiper1 w-100">
            <div class="swiper-wrapper">
                <?php
                $res = selectAll('carousel');
                while($row = mysqli_fetch_assoc($res)){
                    $path = CAROUSEL_IMG_PATH;
                    echo<<<data
                        <div class="swiper-slide tem">
                            <img src="$path$row[Image]" class="w-100 d-block">
                        </div>
                    data;
                }
                ?>
         </div>
     </div>
  </div>

  <!-- Check Availibility form -->

  <div class="container availibility-form">
    <div class="row">
        <div class="col-lg-12 bg-white shadow p-4 rounded">
                <h5 class="mb-4">Check Booking Availibility</h5>
                <form action="rooms.php">
                    <div class="row align-items-end">
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Check-in</label>
                            <input type="date" class="form-control shadow-none" name="checkin" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Check-out</label>
                            <input type="date" class="form-control shadow-none" name="checkout" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight: 500;">Adult</label>
                            <select class="form-select shadow-none" name="adult" required>
                                <option value="">---</option>
                            <?php
                                    $guests_q = mysqli_query($con,"SELECT MAX(Adult) AS `max_adult`, MAX(Children) AS `max_children`
                                                FROM `rooms` WHERE `Status`='1' AND `removed`='0'");
                                    $guests_res = mysqli_fetch_assoc($guests_q);
                                    
                                    for($i=1; $i<=$guests_res['max_adult']; $i++){
                                        echo"<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-3">
                            <label class="form-label" style="font-weight: 500;">Children</label>
                            <select class="form-select shadow-none" name="children" required>
                                <option value="">---</option>
                                <?php
                                    for($i=1; $i<=$guests_res['max_children']; $i++){
                                        echo"<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="check_availibility">
                        <div class="col-lg-1 mb-lg-3 mt-2">
                            <button type="submit" class="btn text-white shadow-none custom-bg">Submit</button>
                        </div>
                    </div>
                </form>
        </div>
    </div>
  </div>


  <!-- Our rooms -->

  <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR ROOMS</h2>
  
<div class="container">
    <div class="row">
        <?php
            $room_res = select("SELECT *FROM `rooms` WHERE `Status` = ? AND `removed` = ? ORDER BY `id` DESC LIMIT 3",[1,0],'ii');

            while($room_data = mysqli_fetch_assoc($room_res)){
                // get features of room
                $fea_q = mysqli_query($con,"SELECT f.Name FROM `features` f 
                INNER JOIN `room_feature` rfea ON f.id = rfea.feature_id
                WHERE rfea.room_id = '$room_data[id]'");

                $feature_data = "";
                while($fea_row = mysqli_fetch_assoc($fea_q)){
                    $feature_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                        $fea_row[Name]
                                    </span>";
                } 
                
                // get facility of room
                $fac_q = mysqli_query($con,"SELECT f.Name FROM `facility` f 
                INNER JOIN `room_facility` rfac ON f.id = rfac.facility_id
                WHERE rfac.room_id = '$room_data[id]'");

                $facility_data = "";
                while($fac_row = mysqli_fetch_assoc($fac_q)){
                    $facility_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                        $fac_row[Name]
                                    </span>";
                }

                // get thumbnail of Image

                $room_thumb = ROOMS_IMG_PATH."thumb.png";

                $thumn_q = mysqli_query($con,"SELECT * FROM `rooms_image`
                WHERE `Room_id` = '$room_data[id]' 
                AND `Thumb` = '1'");

                if(mysqli_num_rows($thumn_q) > 0){
                    $thumb_res = mysqli_fetch_assoc($thumn_q);
                    $room_thumb = ROOMS_IMG_PATH.$thumb_res['Image'];
                }

                $book_btn = "";

                if(!$settings_r['Shutdown']){
                    $login = 0;
                    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                        $login = 1;
                    }
                        $book_btn = "<button onclick='CheckLoginToBook($login,$room_data[id])' class='btn btn-sm text-white custom-bg shadow-none'>Book Now</button>";
                }


                $rating_q = "SELECT AVG(Rating) AS `avg_rating` FROM `rating_review`
                            WHERE `Room_Id`='$room_data[id]' ORDER BY `Sr_No` DESC LIMIT 20";
                
                $rating_res = mysqli_query($con,$rating_q);
                $rating_fetch = mysqli_fetch_assoc($rating_res);

                $rating_data = "";

                if($rating_fetch['avg_rating'] != NULL){
                    $rating_data = "
                        <div class='rating mb-4'>
                            <h6 class='mb-1'>Rating</h6>
                            <span class='badge rounded-pill bg-light'>
                        "; 
                        
                        for($i=0; $i < $rating_fetch['avg_rating']; $i++){
                            $rating_data .="<i class='bi bi-star-fill text-warning'></i> "; 
                        }
                        $rating_data .="</span>
                                </div>";
                                
                }

                // Print room card

                echo<<<data
                    <div class="col-lg-4 col-mb-6 my-3">
                        <div class="card border-0 shadow" style="max-width: 350px; margin: auto">
                            <img src="$room_thumb" class="card-img-top">
                            <div class="card-body">
                                <h5>$room_data[Name]</h5>
                                <h6 class="mb-4">₹$room_data[Price] per night</h6>
                                <div class="features mb-4">
                                    <h6 class="mb-1">Features</h6>
                                    $feature_data
                                </div>
                                <div class="facilities mb-4">
                                    <h6 class="mb-1">Facilities</h6>
                                    $facility_data
                                </div>
                                <div class="Guest mb-4">
                                    <h6 class="mb-1">Guest</h6>
                                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                                        $room_data[Adult] Adults
                                    </span>
                                    <span class="badge rounded-pill bg-light text-dark text-wrap">
                                        $room_data[Children] Children
                                    </span>
                                </div>
                                $rating_data
                                <div class="d-flex justify-content-evenly mb-2">
                                $book_btn
                                <a href="room_details.php?id=$room_data[id]" class="btn btn-sm btn-outline-dark shadow-none">Read Details</a>
                                </div>
                            </div>
                        </div>
                    </div>                   
                data;
            }
        ?>

        <div class="col-lg-12 text-center mt-5">
            <a href="rooms.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms >>></a>
        </div>
    </div>
</div>

<!-- Facilities -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR FACILITIES</h2>

<div class="container">
    <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
        <?php
            $res = mysqli_query($con,"SELECT *FROM `facility` ORDER BY `id` DESC LIMIT 5");
            $path = FACILITY_IMG_PATH;

            while($row = mysqli_fetch_assoc($res)){
                echo<<<data
                    <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
                        <img src="$path$row[Icon]" width="80px">
                        <h5 class="mt-3">$row[Name]</h5>
                    </div>
                data;
            }
        ?>
        <div class="col-lg-12 text-center mt-5">
            <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Facilities >>></a>
        </div>
    </div>
</div>

<!-- TESTIMONIALS -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">TESTIMONIALS</h2>

<div class="container mt-5">
    <!-- Swiper -->
  <div class="swiper swiper-testimonials w-100">
    <div class="swiper-wrapper mb-5">

      <?php

       $review_q = "SELECT rr.*,uc.Name AS uname,uc.Profile, r.Name AS rname FROM `rating_review` rr
                    INNER JOIN `user_crud` uc ON rr.User_Id = uc.id
                    INNER JOIN `rooms` r ON rr.Room_Id = r.id
                    ORDER BY `Sr_No` DESC LIMIT 6";
        
        $review_res = mysqli_query($con,$review_q);
        $img_path = USERS_IMG_PATH;

        if(mysqli_num_rows($review_res) == 0){
            echo 'No reviews yet!';
        }else{
            while($row = mysqli_fetch_assoc($review_res)){
                $stars = "<i class='bi bi-star-fill text-warning'></i> ";
                for($i=1; $i < $row['Rating']; $i++){
                    $stars .= " <i class='bi bi-star-fill text-warning'></i>";   
                }
            
            echo<<<slides
                <div class="swiper-slide bg-white p-4">
                    <div class="align-items-center text-center mb-3">
                        <img src="$img_path$row[Profile]" loading="lazy" width="50px" height="50px" class="rounded-circle">
                        <h6 class="m-0 fw-bold">$row[uname]</h6>
                    </div>
                    <p> $row[Review] </p>
                    <div class="rating">
                        $stars
                    </div>
                </div>
            slides;
                
            }
        }

      ?>      

    </div>
    <div class="swiper-pagination"></div>
  </div>
  <div class="col-lg-12 text-center mt-5">
    <a href="about.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Know More >>></a>
  </div>
</div>

<!-- Reach us -->

<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">REACH US</h2>

<div class="container">
    <div class="row">
        <div class="col-lg-8 col-mb-8 p-4 mb-lg-0 mb-3 bg-white rounded">
            <iframe class="w-100 rounded" height="320" src="<?php echo $contect_r['Iframe'] ?>"  loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="col-lg-4 col-mb-4">
            <div class="bg-white p-4 rounded mb-4">
                <h5>Call us</h5>
                <a href="tel: +<?php echo $contect_r['N1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
                    <i class="bi bi-telephone-fill"></i> +<?php echo $contect_r['N1'] ?>
                </a>
                <br>
                <?php
                if($contect_r['N2'] != ''){
                    echo<<<data
                        <a href="tel: +$contect_r[N2]" class="d-inline-block text-decoration-none text-dark">
                            <i class="bi bi-telephone-fill"></i> +$contect_r[N2]
                        </a>
                    data;
                }
                ?>
            </div>
            <div class="bg-white p-4 rounded mb-4">
                <h5>Follow us</h5>
                <?php
                if($contect_r['Tw'] != ''){
                    echo<<<data
                        <a href="#" class="d-inline-block mb-3">
                            <span class="badge bg-light text-dark fs-6 p-2">
                                <i class="bi bi-twitter me-1"></i> Twitter
                            </span>
                        </a>
                        <br>
                    data;
                }
                ?>
                <a href="<?php echo $contect_r['Fw'] ?>" class="d-inline-block mb-3">
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-facebook me-1"></i> Facebook
                    </span>
                </a>
                <br>
                <a href="<?php echo $contect_r['Insta'] ?>" class="d-inline-block">
                    <span class="badge bg-light text-dark fs-6 p-2">
                        <i class="bi bi-instagram me-1"></i> Instagram
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<?php require('inc/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- Initialize Swiper -->
  <script>
        var swiper = new Swiper(".mySwiper1", {
        spaceBetween: 30,
        effect: "fade",
        centeredSlides: true,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
                  },
        });


      var swiper = new Swiper(".swiper-testimonials", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      slidesPerView: "3",
      loop: true,
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: false,
      },
      pagination: {
        el: ".swiper-pagination",
      },
      breakpoints: {
        320: {
            slidesPerView: 1,
        },
        640: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
      }
    });
  </script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>