<?php
/**
 * SQL builder
 */

class SQL
{
    public $table;
    public $where;
    public $orderBy;
    public $limit;
    public $select;
    public $insert;
    public $update;
    public $delete;
    public $join;
    public $params;
    

    public static function table($table)
    {
        return new SQL($table);
    }

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function limit($numOfRecords)
    {
        $this->limit = $numOfRecords;
        return $this;
    }

    public function where($condition)
    {
        $this->where = $condition;
        return $this;
    }

    public function orderBy($fields)
    {
        $this->orderBy = $fields;
        return $this;
    }

    public function select()
    {
        $this->select = true;
        return $this;
    }

    public function insert()
    {
        $this->insert = true;
        return $this;
    }

    public function update()
    {
        $this->update = true;
        return $this;
    }

    public function delete()
    {
        $this->delete = true;
        return $this;
    }

    public function join($params)
    {
        $this->join = $params;
        return $this;
    }

    public function setValues(...$params)
    {
        $this->params = $params;
        return $this;
    }

    public function execute()
    {
        try {
            [$query, $args] = QueryFormer::buildQuery($this);
            global $SQL_DEBUG_MODE;
            if ($SQL_DEBUG_MODE) {
                return dsql($query, $args);
            }
            return sql($query, $args);
        } catch (PDOException $ex) {
            echo "Can`t execute query!<br>" . $ex->getMessage() ."<br>";
            echo "QUERY:<br>" . $query . "PARAMETERS:<br>";
            print_r($args);
            $final_query = pdo_debugStrParams($query, $args);
            echo "<br>FINAL QUERY:<br>" . $final_query;
        }
    }

    
    // public function getObjecAsArray(){
    //     $objAsArray = [];
    //     foreach ($this as $key => $value) {
    //         $objAsArray[$key] = $value;
    //     }
    //     return $objAsArray;
    // }
    // /**
    //  * Get the value of table
    //  */ 
    // public function getTable()
    // {
    //     return $this->table;
    // }


    // /**
    //  * Get the value of where
    //  */ 
    // public function getWhere()
    // {
    //     return $this->where;
    // }

    // /**
    //  * Get the value of orderBy
    //  */ 
    // public function getOrderBy()
    // {
    //     return $this->orderBy;
    // }

    // /**
    //  * Get the value of limit
    //  */ 
    // public function getLimit()
    // {
    //     return $this->limit;
    // }

    // /**
    //  * Get the value of join
    //  */ 
    // public function getJoin()
    // {
    //     return $this->join;
    // }

    // /**
    //  * Get the value of select
    //  */ 
    // public function getSelect()
    // {
    //     return $this->select;
    // }

    // /**
    //  * Get the value of insert
    //  */ 
    // public function getInsert()
    // {
    //     return $this->insert;
    // }

    // /**
    //  * Get the value of update
    //  */ 
    // public function getUpdate()
    // {
    //     return $this->update;
    // }

    // /**
    //  * Get the value of delete
    //  */ 
    // public function getDelete()
    // {
    //     return $this->delete;
    // }

    // /**
    //  * Get the value of params
    //  */ 
    // public function getParams()
    // {
    //     return $this->params;
    // }
}

