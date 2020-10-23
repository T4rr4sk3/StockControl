<?php 

function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

set_error_handler("exception_error_handler");

function OpenCon($name)
{
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$db = $name;//"test"
$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n".$conn->error);

return $conn;
}

function CloseCon($conn)
{
$conn->close();
}

function better_str($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function redirect($url, $statusCode = 303)
  {
     header('Location: ' . $url, true, $statusCode);
     die();
  }