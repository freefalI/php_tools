<?php
// spl_autoload_register();
error_reporting(E_ALL); 
require("config.php");
require_once("core/Singleton.php");
require_once("core/database/pdoWrapper.php");
require_once("core/builder/SQL.php");
require_once("core/builder/QueryFormer.php");
Database::connect();



// print_r(dsql("select * from table1 where id = :hi or name = :nm",[":hi"=>2,":nm"=>"hello1"]));
// print_r(dsql("select * from table1 where id = ? or name = ?",[2,"hello1"]));
// print_r(dsql("select * from table1 where id = ? or name = ?",2,"hello1"));



$arr = SQL::table('weather')->
    select()->
    //setValues("weather.date","weather.pressure","weather.temperature")->
    //join(['temperature_table'=>'weather.date=temperature_table.date','another_table'=>'weather.pressure=another_table.pressure'])->
    where("weather.pressure >= 650 and weather.temperature = hello")->
    //orderBy('weather.pressure desc')->
    //limit(2)->
    execute();
/*
$arr = SQL::table('weather')->update()->
    setValues(['temperature' => 1111])->
    where("pressure = 746")->
    execute();

$arr = SQL::table('weather')->insert()->
    setValues([
        'day' => '2023-03-02',
        'temperature' => 111,
        'pressure' => 746,
        'humidity' => 73,
        'precipitation' => 'Сильний сніг'])->
    execute();

$arr = SQL::table('weather')->delete()->
    where("temperature = 111")->
    execute();
*/