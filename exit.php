<?php
include_once 'DBC.php';

session_start();
unset($_SESSION['nivel']);
session_destroy();
session_unset();
session_abort();

redirect('index.php',303);