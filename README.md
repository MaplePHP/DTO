

# PHPFuse - DTO
Using a DTO library in PHP provides benefits such as encapsulating data, enforcing immutability, validating data, facilitating data transformation, maintaining API compatibility, reducing coupling, improving code readability, and simplifying testing.





## Usage
The easiest way is to always start from the **Traverse** class and this will give you the most control.

```php
use PHPFuse\DTO\Traverse;

$obj = Traverse::value(["firstname" => "<em>daniel</em>", "lastname" => "doe", "slug" => "Lorem ipsum åäö", "price" => "1999.99", "date" => "2023-08-21 14:35:12", "feed" => [
		"t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe 1"],
		"t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe 2"]
	]
]);
```

#### Traversing the data
```php
echo $obj->feed()->t1()->firstname();
// <em>john 1</em>
```

#### Traversing the feed
```php

foreach($obj->feed()->fetch()->get() as $row) {
	echo $row->firstname."<br>";
}
// <em>john 1</em>
// <em>jane 2</em>
```

### Handlers 
You can access some Handler to make your life easier:
**Str, Uri, Num, DateTime, Arr, ...see Format dir for more**

#### Traversing and modify string
```php
echo $obj->feed()->t1()->firstname("Str")->stripTags()->ucfirst()
// John 1

foreach($obj->feed()->fetch()->get() as $row) {
	echo $row->firstname("Str")->stripTags()->ucfirst()."<br>";
}
// John 1
// Jane 2
```

## Examples
```php
echo $obj->firstname("Str")->stripTags()->ucfirst()."<br>";
// Daniel

echo $obj->slug("Uri")->formatSlug()."<br>";
// lorem-ipsum-aao

echo $obj->price("Num")->toFilesize()."<br>";
// 1.95 kb

echo $obj->price("Num")->round(2)->currency("SEK", 2)."<br>";
// 1 999,99 kr

echo $obj->date("DateTime")->format("y/m/d, \k\l. H:i")."<br>";
// 23/08/21, kl. 14:35
```

## How it works

### Traverse
You can pass array and object data that you later can traverse. You can alose modify using the Format Handlers. 
```php
use PHPFuse\DTO\Traverse;
Traverse::value([MIXED_DATA]);
```

### Format handlers
Start with initiate  the PHPFuse Cache class and pass on a Handler to it. 
```php
use PHPFuse\DTO\Traverse;
use PHPFuse\DTO\Format;

// If you want only to direct
Format\Str::value([STRING]);
Format\Uri::value([STRING]);
Format\Num::value([NUMBER]);
Format\DateTime::value([STRING]);
Format\Arr::value([ARRAY]);
```
