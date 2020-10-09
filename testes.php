<?php 
date_default_timezone_set("America/Vancouver");
//YYYY-MM-DD HH:MI:SS
echo date("Y-m-d H:i:sP");

$data = new DateTime("now",new DateTimeZone("America/Sao_Paulo"));
echo $data->format("Y-m-d H:i:s");
