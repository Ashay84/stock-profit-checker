<?php

//upload.php
//var_dump($_FILES);

require_once('../models/stock.php');
if(isset($_FILES['stock_file']))
{
    $error = '';
    $total_line = '';
    //session_start();

    if($_FILES['stock_file']['name'] != '')
    {
        $allowed_extension = array('csv');
        $file_array = explode(".", $_FILES["stock_file"]["name"]);
        $extension = end($file_array);
        if(in_array($extension, $allowed_extension))
        {   session_start();
            $new_file_name = rand() . '.' . $extension;
            $_SESSION['csv_file_name'] = $new_file_name;
            move_uploaded_file($_FILES['stock_file']['tmp_name'], '../files/'.$new_file_name);
            $file_content = file('../files/'. $new_file_name, FILE_SKIP_EMPTY_LINES);
            $total_line = count($file_content);
           // $_SESSION['total_count'] = $total_line;
            $_SESSION['latest_id']= (new Stock())->getLatestStockId();

        }
        else
        {
            $error = 'Only CSV file format is allowed';
        }
    }
    else
    {
        $error = 'Please Select File';
    }

    if($error != '')
    {
        $output = array(
            'error'  => $error
        );
    }
    else
    {
        $output = array(
            'success'  => true,
            'total_line' => ($total_line - 1)
        );
    }

    echo json_encode($output);
}
else
{
    $output = array(
        'error'  => 'Please upload a file'
    );
    echo json_encode($output);
}



?>