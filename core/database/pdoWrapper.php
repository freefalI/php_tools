<?php
require_once "Database.php";
/**
 * wrapper over PDO 
 * 
 * sql() or dsql() [for debug]  instead of DB::query()
 * 
 * examples of using 
 * sql("select * from table1 where id = :id or name = :nm",[":id"=>2,":nm"=>"hello"])); 
 * dsql("select * from table1 where id = ? or name = ?", [2,"hello"]));
 * dsql("select * from table1 where id = ? or name = ?", 2, "hello"));
 */

function sqlQuery($query, $debug_mode, $args )
{
    if (!empty($args) and is_array($args[0]) and count($args) == 1)
        return Database::query($query, $args[0], $debug_mode);
    return Database::query($query, $args, $debug_mode);
}

function sql($query, ...$args)
{
    return sqlQuery($query, false, $args);
}

function dsql($query, ...$args)
{
    print_r($args);
    return sqlQuery($query, true, $args);
}

function pdo_debugStrParams($string,$data) 
{
    $indexed=$data==array_values($data);
    foreach($data as $k=>$v) {
        if(is_string($v)) $v="'$v'";
        if($indexed) $string=preg_replace('/\?/',$v,$string,1);
        else $string=str_replace(":$k",$v,$string);
    }
    return $string;
}