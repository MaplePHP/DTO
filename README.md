# Darn Tidy Object (DTO)

DTO stands for **Darn Tidy Object**, a playful twist on the traditional Data Transfer Object. But this isn’t your average DTO. It’s a fully-loaded toolkit for **traversing, transforming, and tidying up structured data** in PHP with style, power, and simplicity.

_It also makes your life easier by ensuring every piece of data is returned in the correct type-helping. Whether you expect an int, string, bool, or even a callable, DTO gives you strict, reliable access to your data with minimal effort._

---

## 📦 Installation

```bash
composer require maplephp/dto
```

## 📘 Documentation

* [Why DTO?](https://maplephp.github.io/DTO/docs/intro#why-dto)
* [Traverse Collection](https://maplephp.github.io/DTO/docs/traverse)
* [Format string](https://maplephp.github.io/DTO/docs/format-string)
* [Format Number](https://maplephp.github.io/DTO/docs/format-number)
* [Format Clock](https://maplephp.github.io/DTO/docs/format-clock)
* [Format Dom](https://maplephp.github.io/DTO/docs/format-dom)

---

## How It Works

DTO wraps your data arrays into a powerful, fluent object structure. Instead of cluttered array access, your code becomes expressive and self-documenting.

### Before DTO

```php
$name = isset($data['user']['profile']['name'])
    ? ucfirst(strip_tags($data['user']['profile']['name']))
    : 'Guest';
```

### With DTO

```php
$name = $obj->user->profile->name
    ->strStripTags()
    ->strUcFirst()
    ->fallback('Guest')
    ->get();
```

Much tidier, right?

---

## ✨ Core Features

### Smart Data Traversal

Access deeply nested data without ever worrying about undefined keys.

```php
echo $obj->article->tagline->strToUpper();  
// Result: 'HELLO WORLD'

echo $obj->article->content->strExcerpt()->strUcFirst();  
// Result: 'Lorem ipsum dolor sit amet...'
```

---

### Correct Type Handling (with ease)

No more clunky `is_numeric` checks or `intval` casts. DTO makes it simple to extract values in the exact type you expect:

```php
$orderId = $dto->order->id->toInt();
// Result: 1234 (int)
```

Handle flexible types cleanly with fallbacks:

```php
$callback = $dto->settings->onReady->acceptType(['callable', 'null']);
if (is_callable($callback)) {
    $callback(); // Result: Runs a startup hook or closure
}
```

---

### Built-In Data Transformation

Transform values directly using built-in helpers like:

#### Strings (`str`)

```php
echo $obj->title->strSlug();  
// Result: 'my-awesome-title'
```

#### Numbers (`num`)

```php
echo $obj->filesize->numToFilesize();  
// Result: '1.95 kb'

echo $obj->price->numRound(2)->numToCurrency("USD");  
// Result: $1,999.99
```

#### Dates (`clock`)

```php
echo $obj->created_at->clockFormat('d M, Y', 'sv_SE');  
// Result: '21 augusti 2025'

echo $obj->created_at->clockIsToday();  
// Result: true
```

#### HTML DOM Builder (`dom`)

```php
echo $obj->heading->domTag("h1.title");  
// Result: <h1 class="title">My Heading</h1>
```

Or nest elements with ease:

```php
echo $obj->title->domTag("h1.title")->domTag("header");  
// Result: <header><h1 class="title">Hello</h1></header>
```

---

### Built-In Collection Support

Work with arrays of objects just as cleanly:

```php
foreach ($obj->users->fetch() as $user) {
    echo $user->firstName->strUcFirst();
}
```

---

### Modify Data on the Fly

Change values directly without verbose conditionals:

```php
$updated = $obj->shoppingList->replace([0 => 'Shampoo']);
print_r($updated->toArray());
```

---

Now go forth, write cleaner code, and let DTO handle the messy parts.

---
