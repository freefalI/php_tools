<?php

class Database
{
    private static $dbh;
    private static $isConnected;

    public static function connect()
    {
        if (self::$isConnected) {
            return;
        }
        $port = 3306;
        global $DB_CONN_CONFIG;
        extract($DB_CONN_CONFIG);
        self::$dbh = new PDO("mysql:host=$host;dbname=$dbname;port=$port;charset=$charset", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES   => false
                ]);
        self::$isConnected = true;
    }

    public static function query($query, $args=[], $debugMode = false, $fetchMode = PDO::FETCH_ASSOC)
    {
        if (!self::$isConnected) {
            self::connect();
        }
        $query = preg_replace('/\s{2,}/', " ", trim($query));
        try {
            $sth = self::$dbh->prepare($query);
            $sth->execute($args);
            if ($debugMode) {
                self::debugQuery($query,$args);
            }
            $rawStatement = explode(' ', $query);
            $statement = strtolower($rawStatement[0]);
            if ($statement === 'select' || $statement === 'show') {
                return $sth->fetchAll($fetchMode);
            } elseif (in_array($statement, ['insert', 'update', 'delete'],true)) {
                return $sth->rowCount();
            } else {
                return null;
            }
        } catch (PDOException $ex) {
            echo "Can`t execute query!";
            self::debugQuery($query,$args);
            throw $ex;
        }
    }

    public static function lastInsertId()
    {
        return self::$dbh->lastInsertId();
    }

    private static function debugQuery($query, $args){
        $endl = PHP_EOL;
        echo $endl . str_repeat('*',100) . $endl;
        echo "QUERY:" . $endl . $query . $endl;
        if ($args) {
            echo "PARAMETERS:" . $endl;
            print_r($args);
            $final_query = self::debugReplacePlaceholdersWithValues($query, $args);
            echo $endl . "FINAL QUERY:" . $endl . $final_query . $endl;
            echo str_repeat('*',100) . $endl;
        }
    }
    
    private static function debugReplacePlaceholdersWithValues($string,$data) 
    {
        $indexed=$data==array_values($data);
        foreach($data as $k=>$v) {
            if(is_string($v)) $v="'$v'";
            if($indexed) $string=preg_replace('/\?/',$v,$string,1);
            else {
                if( $k[0]===":"){
                    $string=str_replace("$k",$v,$string);
                }
                else{
                    $string=str_replace("$k", $v, $string);
                }
            }
        }
        return $string;
    }
}
