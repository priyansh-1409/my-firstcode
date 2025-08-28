<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - CONTECT</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
      <h2 class="fw-bold h-font text-center">Contect Us</h2>
      <div class="h-line bg-dark"></div> 
      <p class="text-center mt-3">
        Lorem ipsum dolor sit amet consectetur adipisicing elit.
         Aut provident dignissimos quia <br> quisquam at officiis ipsa
          similique! Ducimus, vitae alias.
      </p> 
    </div>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4">
                <iframe class="w-100 rounded mb-4" height="320" src="<?php echo $contect_r['Iframe'] ?>"  loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> 
                <h5>Address</h5>
                <a href="<?php echo $contect_r['G_map'] ?>" target="_blank" class="d-inline-block text-decoration-none text-dark mb-2">
                <i class="bi bi-geo-alt-fill"></i> <?php echo $contect_r['Address'] ?>
                </a>
                <h5 class="mt-3">Call us</h5>
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
                <h5 class="mt-4">Email</h5>
                <a href="mailto: <?php echo $contect_r['Email'] ?>" class="d-inline-block text-decoration-none text-dark">
                <i class="bi bi-envelope"></i> <?php echo $contect_r['Email'] ?>
                </a>
                <h5 class="mt-4">Follow us</h5>
                <?php
                if($contect_r['Tw'] != ''){
                    echo<<<data
                        <a href="$contect_r[Tw]" class="d-inline-block text-dark fs-5 me-2">
                             <i class="bi bi-twitter me-1"></i>
                        </a>
                    data;
                }
                ?>
                <a href="<?php echo $contect_r['Fw'] ?>" class="d-inline-block text-dark fs-5 me-2">
                        <i class="bi bi-facebook me-1"></i>
                </a>
                <a href="<?php echo $contect_r['Insta'] ?>" class="d-inline-block text-dark fs-5">
                        <i class="bi bi-instagram me-1"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4">
                <form method="POST">
                    <h5>Send a Massage</h5>
                    <div class="mt-3">
                        <label class="form-label" style="font-weight: 500;">Name</label>
                        <input name="name" required type="text" class="form-control shadow-none">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" style="font-weight: 500;">Email</label>
                        <input name="email" required type="email" class="form-control shadow-none">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" style="font-weight: 500;">Subject</label>
                        <input name="subject" required type="text" class="form-control shadow-none">
                    </div>
                    <div class="mt-3">
                        <label class="form-label" style="font-weight: 500;">Massage</label>
                        <textarea name="message" required class="form-control shadow-none" rows="5" style="resize: none"></textarea>
                    </div>
                    <button type="submit" name="send" class="btn text-white custom-bg mt-3">SEND</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    if(isset($_POST['send'])){
        $frm_data = filteration($_POST);

        $q = "INSERT INTO `user_query`(`Name`, `Email`, `Subject`, `Message`) VALUES (?,?,?,?)";
        $values = [$frm_data['name'],$frm_data['email'],$frm_data['subject'],$frm_data['message']];

        $res = insert($q,$values,'ssss');
        if($res == 1){
            alert('success','Mail send!');
        }else{
            alert('error','Failed! Server down');
        }
    }
?>
   
<!-- Footer -->
    <?php require('inc/footer.php'); ?>

</body>
</html>