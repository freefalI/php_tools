<?php
/**
 * SQL builder
 * 
 * 
 * TODO: limit in delete,update
 */

class SQL
{
    private $table;
    private $selectFields = [];
    private $join=[];
    private $where;
    private $orderBy=[];
    private $limit;
    private $offset;
    
    private $query;
    private $params = [];
    
    public static function table($table)
    {
        return new SQL($table);
    }

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function insert(array $params)
    {
        $this->params = $params;
        return $this->execute('insert');
    }
    
    public function insertGetId(array $params)
    {
        $this->params = $params;
        $affectedRows = $this->execute('insert');
        return $affectedRows ? Database::lastInsertId() : null;
    }
    
    public function update(array $params)
    {
        $this->params = $params;
        return $this->execute('update');
    }
    
    public function delete()
    {
        return $this->execute('delete');
    }

    public function select(...$fields)
    {
        $this->selectFields = $fields;
        return $this->execute('select');
    }
    public function get()
    {
        return $this->execute('select');
    }
    public function first()
    {
        $this->limit = 1;
        return @$this->execute('select')[0];
    }
    public function limit($numOfRecords)
    {
        $this->limit = $numOfRecords;
        return $this;
    }
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }
    public function where($condition)
    {
        $this->where = $condition;
        return $this;
    }
    public function orderBy($field, $mode = 'ASC')
    {
        $this->orderBy[$field] = strtoupper($mode);
        return $this;
    }
    public function join($table, $condition)
    {
        $this->join[$table] = $condition;
        return $this;
    }



    private function execute($queryType)
    {
        $method = 'build' . ucfirst($queryType);
        $this->$method();
        global $SQL_DEBUG_MODE;
        return Database::query($this->query, $this->params, $SQL_DEBUG_MODE);
    }

    private function buildSelect()
    {
        $this->query = "SELECT ";
        if ($this->selectFields) {
            $this->query.= "`" .  implode("`,`", $this->selectFields) . "`";
        } else {
            $this->query .= '*';
        }
        $this->query .= " FROM `{$this->table}`";

        if ($this->join) {
            foreach ($this->join as $table =>$cond) {
                $this->query.= " JOIN `$table` ON $cond";
            }
        }
        $this->buildWhere();

        if ($this->orderBy) {
            $this->query .= " ORDER BY";
            foreach ($this->orderBy as $field =>$mode) {
                $this->query .= " `$field` $mode,";
            }
            $this->query = rtrim($this->query, ',');
        }
        if (isset($this->limit)) {
            $this->query .= " LIMIT ?";
            $this->params[] = $this->limit;
        }
        if (isset($this->offset)) {
            $this->query .= " OFFSET ?";
            $this->params[] = $this->offset;
        }
    }

    private function buildInsert()
    {
        if (!isset($this->params)) {
            throw new Exception("Error: You must pass parameters!");
        }
        $values = implode(",", array_fill(0, count($this->params), "?"));
        $this->query = "INSERT INTO `{$this->table}` (`" .
            implode("`,`", array_keys($this->params)) .
            "`) VALUES ($values)";
        $this->params = array_values($this->params);
    }

    private function buildUpdate()
    {
        if (!isset($this->params)) {
            throw new Exception("Error: You must pass parameters!");
        }
        $values =[];
        foreach ($this->params as $key => $value) {
            $values[] = "`$key` = ?";
        }
        $this->query = "UPDATE `{$this->table}` SET " . implode(",", $values);
        $this->params = array_values($this->params);
        $this->buildWhere();
    }

    private function buildDelete()
    {
        $this->query = "DELETE FROM `{$this->table}`";
        $this->buildWhere();
    }

    private function buildWhere()
    {
        if (isset($this->where)) {
            $this->query .= ' WHERE ';
            $arr = array_map(
                'trim',
                preg_split(
                        '/((?:and|or)?\s?[a-zA-Z.]+\s?(?:[><]=?|!?=))/',
                        $this->where,
                        -1,
                        PREG_SPLIT_DELIM_CAPTURE| PREG_SPLIT_NO_EMPTY
                )
            );
            foreach ($arr as $key => $value) {
                if ($key % 2) {
                    $this->query .= " ? ";
                    $this->params[] = $value;
                } else {
                    $this->query .= $value;
                }
            }
        }
    }

    // public function getObjecAsArray(){
    //     $objAsArray = [];
    //     foreach ($this as $key => $value) {
    //         $objAsArray[$key] = $value;
    //     }
    //     return $objAsArray;
    // }
}