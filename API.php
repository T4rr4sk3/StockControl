<?php

require 'vendor/autoload.php';

use Src\Classes\Item;

$item = new Item();


echo $item->listarTodosJson('Mouse');

//var_dump($item);