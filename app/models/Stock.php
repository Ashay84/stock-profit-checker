<?php

class Stock
{

    private $connection;

    public function __construct()
    {
        $this->connection = new PDO("mysql:host=localhost; dbname=stock_db", "root", "");
    }


    private function queryModel($query, array $clause = []): array
    {

        $statement = $this->connection->prepare($query);

        $statement->execute($clause);

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    private function insert($arr)
    {
        //    $statement = $this->connection->prepare($query);

        // $statement->execute($clause);

        $query = "
  INSERT INTO stocks (stock_date, stock_name,price) 
     VALUES (:stock_date, :stock_name,:price)
  ";
        //var_dump($query);
        $statement = $this->connection->prepare($query);

        return $statement->execute($arr);

    }

    function getLatestStockId()
    {
        //$query = 'SELECT max(id) as latest_stock_id from stocks';

        $query = 'SELECT AUTO_INCREMENT as latest_stock_id
    -> FROM information_schema.TABLES
    -> WHERE TABLE_SCHEMA = "stock_db"
    -> AND TABLE_NAME = "stocks"';

        $arr = $this->queryModel($query);
        if (empty($arr))
            return 0;
        return $arr[0]['latest_stock_id'];

    }

    function store($arr)
    {
        return $this->insert($arr);
    }

    function getStockNames(): array
    {
        $query = "SELECT stock_name FROM stocks GROUP BY stock_name";
        $arr = $this->queryModel($query);

        return $arr;
    }

    function findStockByName($name)
    {
        $query = 'SELECT * FROM stocks WHERE stock_name=?';
        return $this->queryModel($query, [$name]);
    }


}