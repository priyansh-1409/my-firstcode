<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/links.php');?>
    <title><?php echo $settings_r['Site_Title'] ?> - BOOKINGS </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
</head>
<body class="bg-light">
<!-- Header -->
    <?php require('inc/header.php'); 

    if(!isset($_SESSION['login']) && $_SESSION['login'] == true){
        redirect('index.php');
    }
    ?>
    

<div class="container">
    <div class="row">

        <div class="col-12 my-5 px-4">
            <h2 class="fw-bold">BOOKINGS</h2>
            <div style="font-size: 14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="#" class="text-secondary text-decoration-none">BOOKINGS</a>
            </div> 
        </div>

        <?php
        
        $query ="SELECT bo.*,bd.* FROM `booking_order` bo
                INNER JOIN `booking_details` bd ON bo.Booking_Id = bd.Booking_Id
                WHERE ((bo.Booking_Status = 'Booked') 
                OR (bo.Booking_Status = 'Cancelled'))
                AND (bo.User_Id = ?) 
                ORDER BY bo.Booking_Id DESC";
        
        $result = select($query,[$_SESSION['uId']],'i');

        while($data = mysqli_fetch_assoc($result)){

            $date      = date("d-m-y",strtotime($data['Date_Time']));
            $checkin   = date("d-m-y",strtotime($data['Check_In']));
            $checkout  = date("d-m-y",strtotime($data['Check_Out']));

            $status_bg = "";
            $btn = "";

            if($data['Booking_Status'] == 'Booked'){
                $status_bg = "bg-success";

                if($data['Arrival'] == 1){
                    $btn =" <a href='generate_pdf.php?gen_pdf&id=$data[Booking_Id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>
                          ";

                    if($data['Rate_Review'] == 0){
                        $btn.="<button type='button' onclick='review_room($data[Booking_Id],$data[Room_Id])' class='btn btn-dark btn-sm shadow-none' data-bs-toggle='modal' data-bs-target='#ReviewModal'>Rate & Review </button>
                              ";
                    }      
                }else{
                    $btn ="<button onclick='cancel_booking($data[Booking_Id])' type='button' class='btn btn-danger btn-sm shadow-none'>Cancel </button>";
                }
            }elseif($data['Booking_Status'] == 'Cancelled'){
                $status_bg = "bg-danger";

                if($data['Refund'] == 0){
                    $btn = "<span class='badge bg-primary'>Refund in proccess</span>";
                }else{
                    $btn =" <a href='generate_pdf.php?gen_pdf&id=$data[Booking_Id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>
                          ";
                }
            }else{
                $status_bg = "bg-warning";
                $btn ="<a href='generate_pdf.php?gen_pdf&id=$data[Booking_Id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>";
            }
        

            echo<<<bookings

            <div class='col-md-4 px-4 mb-4'>
                <div class='bg-white p-3 rounded shadow-sm'>
                    <h5 class='fw-bold'> $data[Room_Name]</h5>
                    <p>₹$data[Price] per night</p>
                    <p>
                        <b>Check in:</b> $checkin <br> 
                        <b>Check out:</b> $checkout 
                    </p>
                    <p>
                        <b>Ammount:</b> ₹$data[Total_Pay] <br> 
                        <b>Order id:</b> $data[Order_Id] <br>
                        <b>Date:</b> $date 
                    </p>
                    <p>
                        <span class='badge $status_bg'>$data[Booking_Status]</span>
                    </p>
                    $btn
                </div>
            </div>

            bookings;

        }    
        ?>

    </div>
</div>

<!-- Rate & Review Modal -->
<div class="modal fade" id="ReviewModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="review-form">
                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-chat-square-heart fs-3 me-2"></i>Rate & Review
                    </h5>
                    <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <select class="form-select shadow-none" name="rating">
                            <option value="1"selected>Excellent</option>
                            <option value="2">Good</option>
                            <option value="3">Ok</option>
                            <option value="4">Poor</option>
                            <option value="5">Bad</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Review</label>
                        <textarea name="review" type="text" rows="3" required class="form-control shadow-none"></textarea>
                    </div>

                    <input type="hidden" name="booking_id">
                    <input type="hidden" name="room_id">

                    <div class="text-end">
                        <button type="submit" class="btn custom-bg text-white shadow-none">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- After Cancel Booking Alert -->
 <?php
    if(isset($_GET['cancel_status'])){
        alert('success','Booking Cancelled!');
    }else if(isset($_GET['review_status'])){
        alert('success','Tanku for for Rating & Review!');
    }
 ?>
   
<!-- Footer -->
    <?php require('inc/footer.php'); ?>


    <script>
        function cancel_booking(id){
            if(confirm('Are you sure to cancel this booking?')){
                let xhr = new XMLHttpRequest();
                xhr.open("POST","Ajex/cancel_booking.php");
                xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');


                xhr.onload = function(){
                    if(this.responseText == 1){
                        window.location.href="bookings.php?cancel_status=true";
                    }else{
                        alert('error','Cancelation Failed');
                    }
                }

                xhr.send('cancel_booking&id='+id);
            }
        }

        let review_form = document.getElementById('review-form');

        function review_room(bid,rid){
            review_form.elements['booking_id'].value = bid;
            review_form.elements['room_id'].value = rid;
        }

        review_form.addEventListener('submit',function(e){
            e.preventDefault();

            let data = new FormData();

            data.append('review_form','');
            data.append('rating',review_form.elements['rating'].value);
            data.append('review',review_form.elements['review'].value);
            data.append('booking_id',review_form.elements['booking_id'].value);
            data.append('room_id',review_form.elements['room_id'].value);

                let xhr = new XMLHttpRequest();
                xhr.open("POST","Ajex/review_room.php");

                if(this.responseText == 0){
                    alert('error',"Rating & Review!");
                }

                xhr.onload = function(){

                    if(this.responseText == 1){
                        window.location.href = 'bookings.php?review_status=true';
                    }else{
                        var myModal = document.getElementById('ReviewModal');
                        var modal   = bootstrap.Modal.getInstance(myModal);
                        modal.hide();
    
                        alert('error',"Rating & Review!");
                    }
                }

                xhr.send(data);
        });
    </script>
</body>
</html>