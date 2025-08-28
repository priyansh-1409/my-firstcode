<?php

require('Admin/inc/db_config.php');
require('Admin/inc/masseges.php');

session_start();

if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
    redirect('index.php');
}

if(isset($_POST['pay_now'])){
    $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(1111,99999999);
    $CUST_ID  = $_SESSION['uId'];
    $CHANNEL_ID  = 'Web_Aplication';
    $TXN_AMMOUNT  = $_SESSION['room']['payment'];


    $frm_data = filteration($_POST);
    
    $query1 = "INSERT INTO `booking_order`(`User_Id`, `Room_Id`, `Check_In`, `Check_Out`,`Booking_Status`, `Order_Id`,`Trans_Amt`) VALUES (?,?,?,?,?,?,?)";
    insert($query1,[$CUST_ID,$_SESSION['room']['id'],$frm_data['checkin'],$frm_data['checkout'],'Booked',$ORDER_ID,$_SESSION['room']['price']],'issssss');

    $booking_id = mysqli_insert_id($con); // fetch last insert id in database

    $query2 = "INSERT INTO `booking_details`(`Booking_Id`, `Room_Name`, `Price`, `Total_Pay`, `User_Name`, `Phone`, `Address`) VALUES (?,?,?,?,?,?,?)";
    insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],$TXN_AMMOUNT,$frm_data['name'],$frm_data['phone'],$frm_data['address']],'issssss');


}
?>


<!-- Page Desiging -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Page Loader</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
 <style>
    .loader-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(255, 255, 255, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .dots {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .dot {
      width: 15px;
      height: 15px;
      margin: 0 5px;
      background-color: #3498db;
      border-radius: 50%;
      animation: dot-blink 1.5s infinite ease-in-out;
    }

    .dot:nth-child(1) {
      animation-delay: 0s;
    }
    .dot:nth-child(2) {
      animation-delay: 0.3s;
    }
    .dot:nth-child(3) {
      animation-delay: 0.6s;
    }

    @keyframes dot-blink {
      0%, 100% {
        opacity: 0.2;
      }
      50% {
        opacity: 1;
      }
    }

    body {
      background-color: #f3f6f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Arial', sans-serif;
    }
    .container {
      max-width: 500px;
      text-align: center;
      background-color: #fff;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }
    .icon {
      font-size: 80px;
      color: #28a745;
      margin-bottom: 20px;
    }
    .heading {
      font-size: 28px;
      font-weight: bold;
      color: #333;
    }
    .message {
      font-size: 16px;
      color: #555;
      margin-bottom: 30px;
    }
    .btn-primary {
      background-color: #28a745;
      border-color: #28a745;
      font-weight: bold;
    }
    .btn-primary:hover {
      background-color: #218838;
      border-color: #1e7e34;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .container {
        width: 80%;
        padding: 20px;
      }

      .heading {
        font-size: 24px;
      }

      .message {
        font-size: 14px;
      }

      .icon {
        font-size: 60px;
      }
    }

    @media (max-width: 480px) {
      .heading {
        font-size: 20px;
      }

      .message {
        font-size: 12px;
      }

      .icon {
        font-size: 50px;
      }
    }
  </style>
</head>
<body>

  <!-- Loader -->
  <div class="loader-wrapper" id="loader">
    <h1>Sending </h1>
    <div class="dots">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
    </div>
  </div>

  <!-- Message to display after 3 seconds -->
  <div class="container visually-hidden" id="massage">
    <div class="icon">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <div class="heading">Payment Successful!</div>
    <div class="message">
      <p>Your payment has been successfully processed. Thank you for your bookings!</p>
    </div>
    <a href="bookings.php" class="btn btn-primary btn-lg">Go to Your Room</a>
  </div>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Script to remove the loader after 3 seconds and display the message -->
  <script>
    window.onload = function() {
      setTimeout(function() {
        document.getElementById("loader").style.display = "none";

        const msg = document.getElementById("massage");
           if(msg.classList.contains('visually-hidden')){
                msg.classList.remove('visually-hidden');
           }
      }, 3000);
    };
  </script>
</body>
</html>
