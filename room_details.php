<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - ROOM DETAILS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); ?>

    <?php
    if(!isset($_GET['id'])){
        redirect('rooms.php');
    }

    $data = filteration($_GET);

    $room_res = select("SELECT *FROM `rooms` WHERE `id` = ? AND `Status` = ? AND `removed` = ?",[$data['id'],1,0],'iii');

    if(mysqli_num_rows($room_res) == 0){
        redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);
    ?>

    

<div class="container">
    <div class="row">

        <div class="col-12 my-5 mb-4 px-4">
            <h2 class="fw-bold"><?php echo $room_data['Name'] ?></h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
            </div> 
        </div>

        <div class="col-lg-7 col-md-12 px-4">
            <div id="roomcarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                        $room_img = ROOMS_IMG_PATH."thumb.png";

                        $img_q = mysqli_query($con,"SELECT * FROM `rooms_image`
                        WHERE `Room_id` = '$room_data[id]'");
    
                        if(mysqli_num_rows($img_q) > 0){
                            $active_class = 'active';
                            while($img_res = mysqli_fetch_assoc($img_q)){
                            echo "
                                    <div class='carousel-item $active_class'>
                                        <img src='".ROOMS_IMG_PATH.$img_res['Image']."' class='d-block w-100 rounded'>
                                    </div>
                                 ";
                                 $active_class = '';
                            }
                        }else{
                            echo"<div class='carousel-item active'>
                                    <img src='$room_img' class='d-block w-100'>
                                 </div>
                                ";
                        }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#roomcarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#roomcarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="col-lg-5 col-md-12 px-4">
            <div class="card mb-4 border-0 shadow-sw rounded-3">
                <div class="card-body">
                    <?php
                        echo<<<price
                            <h4>₹$room_data[Price] per night</h4>
                        price;

                        $rating_q = "SELECT AVG(Rating) AS `avg_rating` FROM `rating_review`
                            WHERE `Room_Id`='$room_data[id]' ORDER BY `Sr_No` DESC LIMIT 20";
                
                        $rating_res = mysqli_query($con,$rating_q);
                        $rating_fetch = mysqli_fetch_assoc($rating_res);

                        $rating_data = "";

                        if($rating_fetch['avg_rating'] != NULL){
                            for($i=0; $i < $rating_fetch['avg_rating']; $i++){
                                $rating_data .="<i class='bi bi-star-fill text-warning'></i> "; 
                            }
                        }

                        echo<<<rating
                            <div class="mb-3">
                                $rating_data
                            </div>
                        rating;

                        // features fatching
                        $fea_q = mysqli_query($con,"SELECT f.Name FROM `features` f 
                        INNER JOIN `room_feature` rfea ON f.id = rfea.feature_id
                        WHERE rfea.room_id = '$room_data[id]'");
    
                        $feature_data = "";
                        while($fea_row = mysqli_fetch_assoc($fea_q)){
                            $feature_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                                $fea_row[Name]
                                            </span>";
                        } 
                        echo<<<feature
                            <div class="mb-3">
                                <h6 class="mb-1">Features</h6>
                                $feature_data
                            </div>
                        feature;

                        // facility fatching 
                        $fac_q = mysqli_query($con,"SELECT f.Name FROM `facility` f 
                        INNER JOIN `room_facility` rfac ON f.id = rfac.facility_id
                        WHERE rfac.room_id = '$room_data[id]'");

                        $facility_data = "";
                        while($fac_row = mysqli_fetch_assoc($fac_q)){
                            $facility_data .="<span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                            $fac_row[Name]
                                            </span>";
                        }
                        echo<<<facility
                            <div class="mb-3">
                                <h6 class="mb-1">Facility</h6>
                                $facility_data
                            </div>
                        facility;

                        echo<<<guest
                           <div class="mb-3">
                                <h6 class="mb-1">Guest</h6>
                                <span class="badge rounded-pill bg-light text-dark text-wrap">
                                    $room_data[Adult] Adults
                                </span>
                                <span class="badge rounded-pill bg-light text-dark text-wrap">
                                    $room_data[Children] Children
                                </span>
                            </div>
                        guest;

                        echo<<<area
                            <div class="mb-3">
                                <h6 class="mb-1">Area</h6>
                                <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                                    $room_data[Area] sq. ft.
                                </span>
                            </div>
                        area;

                        if(!$settings_r['Shutdown']){
                            $login = 0;
                            if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                                $login = 1;
                            }
                            echo<<<book
                                <button onclick='CheckLoginToBook($login,$room_data[id])' class="btn w-100 text-white custom-bg shadow-none mb-1">Book Now</button>
                            book;
                        }
                    ?>
                </div>
            </div>                    
        </div>

        <div class="col-lg-9 col-md-12 px-4">
            <div class="mb-5">
                <h5>Discription</h5>
                <p>
                    <?php echo $room_data['Discription'] ?>
                </p>
            </div>
            <div>
                <h5>Reviews & Ratings</h5>
                <?php
                    $review_q = "SELECT rr.*,uc.Name AS uname,uc.Profile, r.Name AS rname FROM `rating_review` rr
                        INNER JOIN `user_crud` uc ON rr.User_Id = uc.id
                        INNER JOIN `rooms` r ON rr.Room_Id = r.id
                        WHERE rr.Room_Id = '$room_data[id]'
                        ORDER BY `Sr_No` DESC LIMIT 15";
            
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
                            echo<<<review
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="$img_path$row[Profile]" width="50px" height="50px" class="rounded-circle" loading="lazy">
                                        <h6 class="m-0 ms-2 fw-bold">$row[uname]</h6>
                                    </div>
                                    <p class="mb-1">
                                        $row[Review]
                                    </p>
                                    <div>
                                        $stars
                                    </div>
                                </div>
                            review;
                        }
                    }
                ?>    
                
            </div>
        </div>
    </div>
</div>

   
<!-- Footer -->
    <?php require('inc/footer.php'); ?>

</body>
</html>