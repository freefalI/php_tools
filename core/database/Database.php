<?php

class Database
{
    use Singleton;
    private static $dbh;

    public static function connect()
    {
        global $DB_CONN_CONFIG;
        extract($DB_CONN_CONFIG);
        try {
            // exception generation mode
            return self::$dbh = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (PDOException $ex) {
           echo "Can`t connect to database!<br>" . $ex->getMessage() ."<br>";
        }
    }

    public static function query($query, $args, $debug_mode = false)
    {
        $sth = self::$dbh->prepare($query);
        // print_r($sth->queryString);
        if ($debug_mode) {
            echo "<br><pre>"; 
            $sth->debugDumpParams();
            echo "</pre><br>"; 
        } 
        $sth->execute($args);
        return  $sth->fetchAll(PDO::FETCH_ASSOC);
    }
}
