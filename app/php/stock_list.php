<?php
require_once('../models/stock.php');


$data = (new Stock())->getStockNames();

$arr = array_column($data,'stock_name');
echo json_encode($arr);