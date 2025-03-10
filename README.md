
# MaplePHP - Data Transfer Object (DTO)

The MaplePHP DTO library simplifies working with structured data in PHP by wrapping it into objects. This allows easy traversal and transformation through a chainable interface, ensuring consistent, safe data handling and reducing the risk of direct data manipulation.

- **Encapsulation**: Encapsulates data within objects.
- **Immutability**: Ensures data integrity by preventing accidental modification.
- **Validation**: Facilitates data validation, ensuring the data conforms to specific types or formats.
- **Data Transformation**: Easily modify and format data using built-in methods.
- **Low Coupling**: Promotes separation of concerns, reducing dependencies between different parts of your code.
- **Improved Readability**: Makes code cleaner and easier to understand.
- **Simplified Testing**: DTOs are easier to test, as they contain only data and transformation logic.

---

**Note:** MaplePHP DTO also includes polyfill classes for Multibyte String and Iconv support.

## **1. Creating a DTO Object**

The simplest way to start using **MaplePHP DTO** is with the `Traverse` class:

```php
use MaplePHP\DTO\Traverse;

$obj = Traverse::value([
    "firstname" => "<em>daniel</em>",
    "lastname" => "doe",
    "price" => "1999.99",
    "date" => "2023-08-21 14:35:12",
    "feed" => [
        "t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe-1", 'salary' => 40000],
        "t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe-2", 'salary' => 20000]
    ]
]);
```

Now, `$obj` behaves like an object where you can access its properties directly.

---

## **2. Accessing Data**

### **Direct Property Access**

```php
echo $obj->firstname;
// Output: <em>daniel</em>
```

### **Safe Fallback for Missing Values**

```php
echo $obj->feed->t1->doNotExist->fallback('lorem')->strUcFirst();
// Output: Lorem
```

---

## **3. Working with Collections**

### **Iterating Over Arrays**

```php
foreach ($obj->feed->fetch() as $row) {
    echo $row->firstname->strStripTags()->strUcFirst();
}
// Output:
// John 1
// Jane 2
```

### **Filtering Data (`filter`)**

Filters an array based on a callback function.

```php
$filtered = $obj->feed->filter(fn($row) => $row->salary->get() > 30000);
echo $filtered->count();
// Output: 1
```

### **Finding Specific Values**

```php
echo $obj->shopList->search('cheese');
// Output: 3
```

```php
echo $obj->feed->pluck('lastname')->toArray()[1];
// Output: doe-2
```

---

## **4. Transforming Collections**

### **Mapping (`map`)**

Applies a function to each element.

```php
$mapped = $obj->shopList->map(fn($item) => strtoupper($item));
print_r($mapped->toArray());
```
**Output:**
```php
['SOAP', 'TOOTHBRUSH', 'MILK', 'CHEESE', 'POTATOES', 'BEEF', 'FISH']
```

### **Reducing (`reduce`)**

Combines values into a single result.

```php
$sum = $obj->feed->reduce(fn($carry, $item) => $carry + $item->salary->get(), 0);
echo $sum;
// Output: 60000
```

### **Sorting (`reverse`, `shuffle`)**

```php
echo $obj->shopList->reverse()->eq(0);
// Output: fish
```

```php
echo $obj->shopList->shuffle()->eq(0); // Random Output
```

### **Chunking and Slicing (`chunk`, `slice`, `splice`)**

```php
echo $obj->shopList->chunk(3)->count();
// Output: 3
```

```php
echo $obj->shopList->slice(1, 2)->count();
// Output: 2
```

```php
$spliced = $obj->shopList->splice(1, 2, ['replaced'])->toArray();
print_r($spliced);
```
**Output:**
```php
['soap', 'replaced', 'potatoes', 'beef', 'fish']
```

---

## **5. Modifying Collections**

### **Adding and Removing Items**

```php
echo $obj->shopList->push('barbie')->count();
// Output: 8
```

```php
echo $obj->shopList->pop($value)->count();
echo $value;
// Output: fish
```

```php
echo $obj->shopList->shift($value)->count();
echo $value;
// Output: soap
```

---

## **6. Advanced Traversal & Recursion**

### **Walking Through Nested Structures (`walk`, `walkRecursive`)**

```php
$value = "";
$obj->feed->walkRecursive(function ($val) use (&$value) {
    $value .= strip_tags(str_replace(" ", "", $val));
});
echo $value;
// Output: john1doe-1400001jane2doe-2200002
```

### **Flattening Data (`flatten`, `flattenWithKeys`)**

```php
$flatten = $obj->feed->flatten()->map(fn($row) => $row->strToUpper())->toArray();
```

---

## **7. String, Number, and Date Handling**

### **String Manipulations**

```php
echo $obj->firstname->strStripTags()->strUcFirst();
// Output: Daniel
```

### **Number Formatting**

```php
echo $obj->price->numToFilesize();
// Output: 1.95 kb
```

```php
echo $obj->price->numRound(2)->numCurrency("SEK", 2);
// Output: 1 999,99 kr
```

### **Date Handling**

```php
echo $obj->date->clockFormat("y/m/d, H:i");
// Output: 23/08/21, 14:35
```

```php
\MaplePHP\DTO\Format\Clock::setDefaultLanguage('sv_SE');
echo $obj->date->clockFormat('d M');
// Output: 21 augusti
```

---

## **8. Array Utility Methods**

### **Merging and Replacing Arrays**

```php
$merged = $obj->shopList->merge(['eggs', 'bread']);
print_r($merged->toArray());
```
**Output:**
```php
['soap', 'toothbrush', 'milk', 'cheese', 'potatoes', 'beef', 'fish', 'eggs', 'bread']
```

```php
$replaced = $obj->shopList->replaceRecursive([0 => 'soap_bar']);
print_r($replaced->toArray());
```
**Output:**
```php
['soap_bar', 'toothbrush', 'milk', 'cheese', 'potatoes', 'beef', 'fish']
```

### **Computing Differences (`diff`, `diffAssoc`, `diffKey`)**

```php
$diff = $obj->shopList->diff(['milk', 'cheese']);
print_r($diff->toArray());
```
**Output:**
```php
['soap', 'toothbrush', 'potatoes', 'beef', 'fish']
```

```php
$diffAssoc = $obj->shopList->diffAssoc(['soap', 'toothbrush']);
print_r($diffAssoc->toArray());
```
**Output:**
```php
['milk', 'cheese', 'potatoes', 'beef', 'fish']
```

### **Extracting Keys (`keys`, `pluck`)**

```php
print_r($obj->shopList->keys()->toArray());
```
**Output:**
```php
[0, 1, 2, 3, 4, 5, 6]
```

```php
print_r($obj->feed->pluck('lastname')->toArray());
```
**Output:**
```php
['doe-1', 'doe-2']
```
