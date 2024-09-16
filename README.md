


# MaplePHP - DTO
Using a DTO library in PHP provides benefits such as encapsulating data, enforcing immutability, validating data, facilitating data transformation, maintaining API compatibility, reducing coupling, improving code readability, and simplifying testing.





## Usage
The easiest way is to always start from the **Traverse** class and this will give you the most control.

```php
use MaplePHP\DTO\Traverse;

$obj = Traverse::value(["firstname" => "<em>daniel</em>", "lastname" => "doe", "slug" => "Lorem ipsum åäö", "price" => "1999.99", "date" => "2023-08-21 14:35:12", "feed" => [
		"t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe 1"],
		"t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe 2"]
	]
]);
```

#### Traversing the data
```php
echo $obj->feed->t1->firstname;
// <em>john 1</em>
```

#### Traversing the feed
```php

foreach($obj->feed->fetch() as $row) {
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
echo $obj->feed->t1->firstname->strStripTags()->strUcFirst()
// Same as
// echo $obj->feed->t1->firstname->str()->stripTags()->ucFirst()
// Result: John 1
foreach($obj->feed()->fetch()->get() as $row) {
	echo $row->firstname->strStripTags()->strUcFirst()."<br>";
}
// John 1
// Jane 2
```

## Examples
```php
echo $obj->firstname->strStripTags()->strUcFirst()."<br>";
// Daniel

echo $obj->price->numToFilesize()."<br>";
// 1.95 kb

echo $obj->price->numRound(2)->numCurrency("SEK", 2)."<br>";
// 1 999,99 kr

echo $obj->date("DateTime")->format("y/m/d, \k\l. H:i")."<br>";
// 23/08/21, kl. 14:35
```

## How it works

### Traverse
When you pass array and object data to the Traverse object it will make it possible for you to easily traverse the array/object. you can then use one of the Handlers to modify the data when you have traversed to the right position.
```php
use MaplePHP\DTO\Traverse;
$obj = Traverse::value([MIXED_DATA]);
$obj->arrayKey1()->arrayKey2("HANDLER")->modifyFunction1->modifyFunction2();
```

### Format handlers
You can also just access the handlers directly to modify data quickly. 
```php
use MaplePHP\DTO\Format;

Format\Str::value([STRING]);
Format\Num::value([NUMBER]);
Format\Arr::value([ARRAY]);
Format\DateTime::value([STRING]);
```
#### Example
```php
echo Format\Str::value("lorem")->ucfirst();
// Lorem
```

