# framework

config.php:

$DB_CONN_CONFIG = [
    "host" => "localhost",
    //"port"=>3306,
    "dbname" => "",
    "username" => "",
    "password" => "",
    "charset" => "UTF8"
];

$SQL_DEBUG_MODE = false;

#Database class test

Database::connect();
//How to work with placeholders:
$a = Database::query('insert into goods (name,price) values ( :nm, :pr)',[':nm'=>'shoes',':pr'=>50],true);print_r($a);
$a = Database::query('insert into goods (name,price) values ( :nm, :pr)',['nm'=>'shoes','pr'=>50],true);print_r($a);
$a = Database::query('insert into goods (name,price) values ( ?, ?)',['shoes',50],true);print_r($a);

$a = Database::query('update goods set price = 100 where id = :id',[':id'=>2],true);print_r($a);
$a = Database::query('select * from goods where id = :id',[':id'=>2],true);print_r($a);
$a = Database::query('delete from goods where id = :id',[':id'=>2],true);print_r($a);


#SQL class test

$q = SQL::table('goods')->insert(['id'=>20,'name'=>'jeans','price'=>200]);print_r($q);
$q = SQL::table('goods')->insertGetId(['name'=>"adidas",'price'=>120]);print_r( $q);

$q = SQL::table('goods')->where('name = adidas')->update(['price'=>200]);print_r($q);

$q = SQL::table('goods')->where('id > 5')->delete();print_r($q);

$q = SQL::table('goods')->where('price > 30 and price < 300')->get();print_r( $q);
$q = SQL::table('goods')->where('price > 30 and price < 300')->first();print_r( $q);
$q = SQL::table('goods')->where('price > 30 and price < 300')->select('name','price');print_r( $q);

$q = SQL::table('goods')->join('sells','goods.id = sells.id')->get();print_r( $q);

$q = SQL::table('goods')->
    // join('sells','goods.id = sells.id')->
    where("price > 30 and price < 300")->
    orderBy('price','desc')->
    orderBy('name')->
    offset(3)-> 
    limit(2)-> 
    select('name','price');
print_r($q);


#Model class test

class Product extends Model
{
    protected $table ='goods';
}

$q = Product::find(55);
$q = Product::find([53,54,55]);
$q = Product::all();
$q = Product::where("id = 55")->get();

$q = Product::find(55);
$q->price = 200;
$q->save();

$q->delete();

Product::destroy(51);

$q = Product::create(['price'=>500,'name'=>'nike']);
print_r($q);
