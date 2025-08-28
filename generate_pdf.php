<?php
require('Admin/inc/masseges.php');
require('Admin/inc/db_config.php');

session_start();

if(!isset($_SESSION['login']) && $_SESSION['login'] == true){
    redirect('index.php');
}

if(isset($_GET['gen_pdf']) && isset($_GET['id'])){
    $frm_data = filteration($_GET);

    $query = "SELECT bo.*,bd.*,uc.Email FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.Booking_Id = bd.Booking_Id
    INNER JOIN `user_crud` uc ON bo.User_Id = uc.id
    WHERE ((bo.Booking_Status = 'Booked' AND bo.Arrival = 1) 
    OR (bo.Booking_Status = 'Cancelled' AND bo.Refund = 1))
    AND bo.Booking_Id = '$frm_data[id]'";

    $res = mysqli_query($con,$query);

    if(mysqli_num_rows($res) == 0){
        header('location: index.php');
        exit;
    }

    $data = mysqli_fetch_assoc($res);

    $date      = date("d-m-y",strtotime($data['Date_Time']));
    $checkin   = date("d-m-y",strtotime($data['Check_In']));
    $checkout  = date("d-m-y",strtotime($data['Check_Out']));

    $table_data = "
        <h2> BOOKING RECIET</h2>
        <table border='1'>
            <tr>
                <td>Order ID: $data[Order_Id]</td>
                <td>Booking Date: $date</td>
            </tr>
            <tr>
                <td colspan='2'>Status: $data[Booking_Status]</td>
            </tr>
            <tr>
                <td>Name: $data[User_Name]</td>
                <td>Email: $data[Email]</td>
            </tr>
            <tr>
                <td>Phone.No.: $data[Phone]</td>
                <td>Address: $data[Address]</td>
            </tr>
            <tr>
                <td>Room Name: $data[Room_Name]</td>
                <td>Cost: ₹$data[Price] per night</td>
            </tr>
            <tr>
                <td>Check in:  $checkin</td>
                <td>Check out: $checkout</td>
            </tr>
    ";

    if($data['Booking_Status'] == 'Cancelled'){
        $refund = ($data['Refund']) ? "Amount Refunded" : "Not Yet Refunded";

        $table_data.="<tr>
            <td>Amount Paid: ₹$data[Trans_Amt]</td>
            <td>Refund: $refund</td>
        </tr>
        ";
    }else{
        $table_data.="<tr>
            <td>Room No: $data[Room_No]</td>
            <td>Amount Paid: ₹$data[Trans_Amt]</td>
        </tr>
        ";
    }
    $table_data.="</table>";

    echo $table_data;
}else{
    header('location: index.php');
}

?>