
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

---

## Usage

The simplest way to work with the library is to start with the `Traverse` class, which provides powerful control over your data.

```php
use MaplePHP\DTO\Traverse;

$obj = Traverse::value([
    "firstname" => "<em>daniel</em>",
    "lastname" => "doe",
    "slug" => "Lorem ipsum åäö",
    "price" => "1999.99",
    "date" => "2023-08-21 14:35:12",
    "feed" => [
        "t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe 1"],
        "t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe 2"]
    ]
]);
```

### Accessing Nested Data
Each key in the array is accessible as an object property, and you can continue to drill down into nested arrays, maintaining consistency and safety.

```php
echo $obj->feed->t1->firstname;
// Output: <em>john 1</em>
```

### Iterating Through Arrays

```php
foreach($obj->feed->fetch() as $row) {
    echo $row->firstname;
}
// Output:
// <em>john 1</em>
// <em>jane 2</em>
```

---

## Built-in Data Handlers

MaplePHP DTO comes with powerful handlers for common data transformations. These handlers make it easy to manipulate strings, numbers, URIs, arrays, dates, and more.

### String Handling Example

You can chain methods for string manipulation:

```php
echo $obj->feed->t1->firstname->strStripTags()->strUcFirst();
// Equivalent to:
// echo $obj->feed->t1->firstname->str()->stripTags()->ucFirst();
// Output: John 1
```

You can also apply transformations when iterating over an array:

```php
foreach($obj->feed->fetch() as $row) {
    echo $row->firstname->strStripTags()->strUcFirst();
}
// Output:
// John 1
// Jane 2
```

---

## More Examples

Here are more examples of using the DTO library’s built-in handlers for different types of data:

### String Manipulation

```php
echo $obj->firstname->strStripTags()->strUcFirst();
// Output: Daniel
```

### Number Formatting

```php
echo $obj->price->numToFilesize();
// Output: 1.95 kb

echo $obj->price->numRound(2)->numCurrency("SEK", 2);
// Output: 1 999,99 kr
```

### Date Formatting

```php
echo $obj->date->clockFormat("y/m/d, H:i");
// Output: 23/08/21, 14:35
```

---

**Note:** This guide is a work in progress, with more content to be added soon.

---

## How It Works

The **MaplePHP DTO** library operates by wrapping data into objects that allow easy traversal and transformation using a chainable API. This structure allows for consistent and safe data handling, minimizing direct data manipulation risks.

Here’s how the key components of the library work:

### 1. **Traverse Class**

At the core of the library is the `Traverse` class. This class allows you to wrap an associative array (or any data structure) into an object, making each element of the array accessible as an object property.

- **Key-to-Property Mapping**: Each key in the array becomes an object property, and its value is transformed into a nested `Traverse` object if it's an array.
- **Lazy Loading**: The values are only accessed when needed, which allows you to traverse large data structures efficiently.

### 2. **Handlers for Data Types**

MaplePHP DTO uses specific handlers (such as `Str`, `Num`, `DateTime`, `Uri`, etc.) to manage different data types. These handlers provide methods to transform and validate the data.

- **String Handling**: The `Str` handler enables string-related operations, such as stripping tags, formatting case, and more.
- **Number Handling**: The `Num` handler allows numerical operations like rounding, formatting as currency, and converting to file sizes.
- **Date and Time Handling**: The `DateTime` handler provides methods for formatting and manipulating dates and times.

### 3. **Immutability**

When transformations are applied (e.g., `strUcFirst()` or `numRound()`), the library ensures immutability by returning a new `Traverse` instance with the modified data. This prevents accidental mutations of the original data.

### 4. **Fetch Method for Arrays**

The `fetch()` method simplifies working with arrays. Instead of manually looping through the array, you can use `fetch()` to iterate over the elements and apply transformations to each one.
