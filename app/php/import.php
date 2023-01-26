<?php

//import.php
require_once('../models/stock.php');
header('Content-type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

set_time_limit(0);

ob_implicit_flush(1);

session_start();

//var_dump($_SESSION);

if (isset($_SESSION['csv_file_name'])) {
    //$connect = new PDO("mysql:host=localhost; dbname=stock_db", "root", "");

    $file_data = fopen('../files/' . $_SESSION['csv_file_name'], 'r');

    fgetcsv($file_data);

    //$eRow = 0;
    $errors='';
    while ($row = fgetcsv($file_data)) {

        if (empty($row[1]) || empty($row[2]) || empty($row[3])) {
            $errors .= '<p>One of the field missing on row no :' . $row[0] . '</p>';
            continue;
        }


        $newDate = date("Y-m-d", strtotime($row[1]));
        $data = array(
            ':stock_date' => $newDate,
            ':stock_name' => strtoupper($row[2]),
            ':price' => $row[3]
        );
        (new Stock())->store($data);

//        sleep(1);

        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    if (isset($errors))
        return array(
            'error' => $errors
        );

    unset($_SESSION['csv_file_name']);
    unset($_SESSION['latest_id']);
}

?>
