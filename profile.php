<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - PROFILE </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); 

    if(!isset($_SESSION['login']) && $_SESSION['login'] == true){
        redirect('index.php');
    }

    $u_exist = select("SELECT * FROM `user_crud` WHERE `id`=? LIMIT 1",[$_SESSION['uId']],'s');

    if(mysqli_num_rows($u_exist) == 0){
        redirect('index.php');
    }

    $u_fetch = mysqli_fetch_assoc($u_exist);
    ?>
    

<div class="container">
    <div class="row">

        <!-- Home & Profile Page Address  -->
        <div class="col-12 my-5 px-4">
            <h2 class="fw-bold">PROFILE</h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="#" class="text-secondary text-decoration-none">PROFILE</a>
            </div> 
        </div>

        <!-- User data manage -->
        <div class="col-12 mb-5 px-4">
            <div class="bg-white p-3 p-md-4 rounded shadow-sm">
                <form id="info-profile">
                    <h5 class="mb-3 fw-bold">Basic Information</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Name</label>
                            <input name="name" value="<?php echo $u_fetch['Name'] ?>" type="text" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input name="phone" value="<?php echo $u_fetch['Phone'] ?>" type="number" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">DOB</label>
                            <input name="dob" value="<?php echo $u_fetch['Dob'] ?>" type="date" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pin Code</label>
                            <input name="pincode" value="<?php echo $u_fetch['Pin_Code'] ?>" type="number" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-8 mb-4">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $u_fetch['Address'] ?></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
                </form>
            </div> 
        </div>

        <!-- User Picture Manage -->
        <div class="col-md-4 mb-5 px-4">
            <div class="bg-white p-3 p-md-4 rounded shadow-sm">
                <form id="profile-form">
                    <h5 class="mb-3 fw-bold">Picture</h5>
                    <img src="<?php echo USERS_IMG_PATH.$u_fetch['Profile'] ?>" class="rounded-circle img-fluid mb-2" style="height: 250px; width: 250px">
                    
                    <label class="form-label">New Picture</label>
                    <input name="profile" type="file" accept=".jpg, .jpeg, .png, .webp" class=" mb-3 form-control shadow-none" required>

                    <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
                </form>
            </div> 
        </div>

        <!-- User Password Manage -->
        <div class="col-md-8 mb-5 px-4">
            <div class="bg-white p-3 p-md-4 rounded shadow-sm">
                <form id="pass-form">
                    <h5 class="mb-3 fw-bold">Change Password</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input name="new_pass" type="password" class="form-control shadow-none" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Confirm Password</label>
                            <input name="confirm_pass" type="password" class="form-control shadow-none" required>
                        </div>
                    </div>
                        <button type="submit" class="btn text-white custom-bg shadow-none">Save Changes</button>
                </form>
            </div> 
        </div>

    </div>
</div>
   
<!-- Footer -->
    <?php require('inc/footer.php'); ?>


    <script>

        // User data manage here    
        let info_profile = document.getElementById('info-profile');

        info_profile.addEventListener('submit',function(e){
            e.preventDefault();

            let data = new FormData();

            data.append('info_profile','');
            data.append('name',info_profile.elements['name'].value);
            data.append('phone',info_profile.elements['phone'].value);
            data.append('dob',info_profile.elements['dob'].value);
            data.append('pincode',info_profile.elements['pincode'].value);
            data.append('address',info_profile.elements['address'].value);

                let xhr = new XMLHttpRequest();
                xhr.open("POST","Ajex/profile.php");

                xhr.onload = function(){
                    if(this.responseText == 'phone_already'){
                        alert('error',"Phone number is alredy registred!");
                    }else if(this.responseText == 0){
                        alert('error','No changes made!');
                    }else{
                        alert('success','Changes saved!'); 
                    }
                }

                xhr.send(data);
          
        });

        //Picture form control
        let profile_form = document.getElementById('profile-form');

        profile_form.addEventListener('submit',function(e){

            e.preventDefault();

            let data = new FormData();

            data.append('profile_form','');
            data.append('profile',profile_form.elements['profile'].files[0]);

            let xhr = new XMLHttpRequest();
                xhr.open("POST","Ajex/profile.php");

                xhr.onload = function(){

                    if(this.responseText == 'inv_img'){
                    alert('error',"Only JPG, WEBP, & PNG images are allowed!");
                    }else if(this.responseText == 'upload_failed'){
                        alert('error',"Image upload failed!");
                    }else if(this.responseText == 0){
                        alert('error','Updation Failed!');
                    }else{
                        window.location.href = window.location.pathname; 
                    }
                }

                xhr.send(data);
        });

        //Password form control
        let pass_form = document.getElementById('pass-form');

        pass_form.addEventListener('submit',function(e){

            e.preventDefault();

            let  new_pass     = pass_form.elements['new_pass'].value;
            let  confirm_pass = pass_form.elements['confirm_pass'].value;

            if(new_pass != confirm_pass){
                alert('error','Password Missmatched!');
                return false;
            }

            let data = new FormData();
            data.append('pass_form','');
            data.append('new_pass',new_pass);
            data.append('confirm_pass',confirm_pass);

            let xhr = new XMLHttpRequest();
                xhr.open("POST","Ajex/profile.php");

                xhr.onload = function(){

                    if(this.responseText == 'missmatched'){
                    alert('error',"Password Missmatched!");
                    }else if(this.responseText == 0){
                        alert('error','Updation Failed!');
                    }else{
                        alert('success','Changes Saved!'); 
                        pass_form.reset();
                    }
                }

                xhr.send(data);
        });
    </script>
</body>
</html>