<?php 

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