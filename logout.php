<?php

require('Admin/inc/masseges.php');

session_start();
session_destroy();
redirect('index.php');
?>