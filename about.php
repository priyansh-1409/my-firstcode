<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - ABOUT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<style>
.box{
    border-top-color: #2ec1ac !important;
}
</style>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
      <h2 class="fw-bold h-font text-center">About Us</h2>
      <div class="h-line bg-dark"></div> 
      <p class="text-center mt-3">
        Lorem ipsum dolor sit amet consectetur adipisicing elit.
         Aut provident dignissimos quia <br> quisquam at officiis ipsa
          similique! Ducimus, vitae alias.
      </p> 
    </div>

    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
                <h3 class="mb-3">Lorem ipsum dolor sit.</h3>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    Suscipit repellendus recusandae autem harum officia to!
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    Suscipit repellendus recusandae autem harum officia to!
                </p>
            </div>
            <div class="col-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
                <img src="images/mainpage/8.jpg" class="w-100">
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="images/mainpage/9.jpg" width="70px">
                    <h4 class="mt-3">100+ROOMS</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="images/mainpage/9.jpg" width="70px">
                    <h4 class="mt-3">200+CUSTOMERS</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="images/mainpage/9.jpg" width="70px">
                    <h4 class="mt-3">150+REVIES</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="images/mainpage/9.jpg" width="70px">
                    <h4 class="mt-3">200+STAFFS</h4>
                </div>
            </div>
        </div>
    </div>

    <h3 class="my-5 fw-bold h-font text-center">MANAGEMENT TEAMS</h3>

    <div class="container px-4">
        <!-- Swiper -->
        <div class="swiper mySwiper">
            <div class="swiper-wrapper mb-5">
                <?php
                $about_r = selectAll('team_details');
                $path = ABOUT_IMG_PATH;

                while($row = mysqli_fetch_assoc($about_r)){
                    echo<<<data
                        <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                            <img src="$path$row[Picture]" class="w-100">
                            <h5 class="mt-2">$row[Name]</h5>
                        </div>
                    data;
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
        </div> 
    </div>
<!-- Footer -->
    <?php require('inc/footer.php'); ?>


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    var swiper = new Swiper(".mySwiper", {
    spaceBetween: 40,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        320: {
            slidesPerView: 1,
        },
        640: {
            slidesPerView: 2,
        },
        768: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 4,
        },
      }
    });
</script>

</body>
</html>