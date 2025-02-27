<?php

namespace Models;

use Database\Connector;

class Produto extends Connector
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setInsertProduct($params)
    {
        $this->conn->beginTransaction();
        try{
            $query = "INSERT INTO produto (:COLUMNS) VALUES (:VAL);";
    
            $columns = implode(",", array_keys($params));
            $values = implode(",", $this->formatValueParams(array_values($params)));
    
            $query = str_replace(":COLUMNS", $columns, $query);
            $query = str_replace(":VAL", $values, $query);
    
            $this->conn->exec($query);

            $lastId = $this->conn->lastInsertId();
            $this->conn->commit();
            return $lastId;
        }catch(\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Não foi possível cadastrar o produto, entre em contato com o administrador!\n {$e->getMessage()}");
        }
    }

    public function getAllProducts($params = array(), $order = new \stdClass())
    {
        $query = 'SELECT prd_id, prd_descricao, prd_valor, prd_status, prd_data_inclusao, prd_data_ult_att FROM produto WHERE 1 :filters order by :ordenate :direction';

        $parameters = (isset($params) && count($params)) > 0 ? $this->bindParams($params) : "";

        $query = str_replace(":filters", $parameters, $query);
        $query = str_replace(':ordenate', !empty($order->column) ? $order->column : 'prd_id', $query);
        $query = str_replace(':direction', !empty($order->direction) ? $order->direction : "desc", $query);

        $stmt = $this->conn->query($query);
        $stmt->execute();
        $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $res;
    }

    public function setUpdateProduct($id, $params)
    {
        $this->conn->beginTransaction();
        try{
            $query = "UPDATE produto SET :params WHERE prd_id = ':prd_id'";
    
            $queryParams = "";

            foreach($params as $column => $value) {
                $queryParams .= ", $column = $value";
            }
    
            $queryParams = substr(str_replace("|sl", '"', $queryParams), 1);

            $query = str_replace(":params", $queryParams, $query);
            $query = str_replace(":prd_id", $id, $query);
    
            $this->conn->exec($query);

            $this->conn->commit();
            return true;
        }catch(\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception("Não foi possível atualizar o produto, entre em contato com o administrador!\n {$e->getMessage()}");
        }
    }
}