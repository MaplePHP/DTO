<?php
// Place Codes/snippets at top of test file

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$prefix = "PHPFuse";
$dir = dirname(__FILE__)."/../";

//require_once("{$dir}../_vendors/composer/vendor/autoload.php");

spl_autoload_register(function($class) use($dir, $prefix) {
    $classFilePath = NULL;
    $class = str_replace("\\", "/", $class);
    $exp = explode("/", $class);
    $sh1 = array_shift($exp);
    $path = implode("/", $exp).".php";
    if($sh1 !== $prefix) $path = "{$sh1}/{$path}";

    $filePath = $dir."../".$path;
    require_once($filePath);    
});


use PHPFuse\DTO\Traverse;
use PHPFuse\DTO\Format;

$obj = Traverse::value(["firstname" => "<em>daniel</em>", "lastname" => "ronkainen", "slug" => "Lorem ipsum åäö", "price" => "1999.99", "date" => "2023-08-21 14:35:12", "feed" => [
		"t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe 1"],
		"t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe 2"]
	]
]);


echo Format\Str::value("lorem")->ucfirst();

echo $obj->feed()->t1()->firstname("Str")->stripTags()->ucfirst()."<br>";


echo "<br><strong>Str:</strong><br>";
echo $obj->firstname("Str")->stripTags()->ucfirst()."<br>";

echo "<br><strong>Uri:</strong><br>";
echo $obj->slug("Uri")->formatSlug()."<br>";

echo "<br><strong>Num:</strong><br>";
echo $obj->price("Num")->toFilesize()."<br>";
echo $obj->price("Num")->round(2)->currency("SEK", 2)."<br>";

echo "<br><strong>DateTime:</strong><br>";
echo $obj->date("DateTime")->format("y/m/d, \k\l. H:i")."<br>";


echo "<br><strong>Feed:</strong><br>";
foreach($obj->feed()->fetch()->get() as $row) {
	echo $row->firstname("Str")->stripTags()->ucfirst()."<br>";
}


