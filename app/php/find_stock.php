<?php

$connect = new PDO("mysql:host=localhost; dbname=stock_db", "root", "");

$query = "SELECT * FROM stocks WHERE stock_name=? and stock_date between ? and ?";

$clauses = [$_POST['stock_list'], $_POST['from_date'], $_POST['to_date'] . ' 23:59:59'];

$statement = $connect->prepare($query);

$statement->execute($clauses);

if (!$statement->rowCount()) {
    $output = array(
        'error' => 'No data found'
    );
    echo json_encode($output);
    return;

}


$arr = array_map(function ($entry) {
    return ['stock_date' => $entry['stock_date'], 'price' => $entry['price']];
}, $statement->fetchAll(PDO::FETCH_ASSOC));


$seqArr = findMinAndMaxSequence($arr);

if (empty($seqArr)) {
    $output = array(
        'error' => 'No entry found to be profitable'
    );
    echo json_encode($output);
    return;
}

$query = "SELECT avg(price) as mean FROM stocks WHERE stock_name=? and stock_date between ? and ?";

$statement = $connect->prepare($query);

$statement->execute($clauses);

$mean = $statement->fetchAll(PDO::FETCH_ASSOC)[0]['mean'];

//var_dump($mean);

$output = array(
    'success' => true,
    'data' => $seqArr + ['mean' => $mean ,'std_dev' => std_deviation($arr)]
);


echo json_encode($output);

function findMinAndMaxSequence(array $arr): array
{
    //$min = $arr[0];
    $profit = 0;
    $profArr = [];
    for ($i = 0; $i < count($arr); $i++) {
        for ($j = $i + 1; $j < count($arr); $j++) {
            $expectedProfit = $arr[$j]['price'] - $arr[$i]['price'];
            if ($expectedProfit > $profit) {
                $profit = $expectedProfit;
                $profArr['buy_stock_date'] = $arr[$i]['stock_date'];
                $profArr['buy_stock_price'] = $arr[$i]['price'];

                $profArr['sell_stock_date'] = $arr[$j]['stock_date'];
                $profArr['sell_stock_price'] = $arr[$j]['price'];
                $profArr['profit'] = $expectedProfit;
            }

        }
    }

    return $profArr;

}

function std_deviation(array $arr)
{
    $prices= array_column($arr,'price');
    //var_dump($arr);
    $no_element = count($prices);
    $var = 0.0;
    $avg = array_sum($prices)/$no_element;
    foreach($prices as $i)
    {
        $var += pow(($i - $avg), 2);
    }
    return (float)round(sqrt($var/$no_element),4);
}