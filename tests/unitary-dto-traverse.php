<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO test", callback: function () {
    $obj = Traverse::value([
        "firstname" => "<em>daniel</em>",
        "lastname" => "doe",
        "email" => "john.doe@gmail.com",
        "slug" => "Lorem ipsum åäö",
        "price" => "1999.99",
        "date" => "2023-08-21 14:35:12",
        "feed" => [
            "t1" => ["firstname" => "<em>john 1</em>", "lastname" => "doe-1", 'salary' => 40000, "status" => 1, ['test' => 1]],
            "t2" => ["firstname" => "<em>jane 2</em>", "lastname" => "doe-2", 'salary' => 20000, "status" => 0, ['test' => 2]],
        ],
        "meta" => [
            "title" => 'Lorem ipsum dolor',
            "description" => '',
            "published" => '2023-08-23 12:35:12',
            "slug" => 'lorem-ipsum-dolor',
        ],
        'shopList' => ['soap', 'toothbrush', 'milk', 'cheese', 'potatoes', 'beef', 'fish'],
        'randList' => ['lorem', 'ipsum', 'dolor', 'sit', 'lorem', 'amet', 'sum'],
    ]);


    $this->add($obj
        ->add('qwddqwq', ['Hello'])
        ->merge(['World'])
        ->implode("-")
        ->strToLower()->get(), [
        'equal' => 'hello-world',
    ],
        "Add returned wrong string value");


    $this->add($obj->shopList->keys()->eq(1), [
        'equal' => 1,
    ], "Keys returned wrong key value");


    $this->add($obj->shopList->next()->next()->key()->get(), [
        'equal' => 2,
    ], "Key returned wrong key value");

    $this->add($obj->meta->select(['title', 'description'])->count(), [
        'equal' => 2,
    ], "Replace returned wrong value");

    $this->add($obj->shopList->searchMatch(['soap', 'cheese'])->count(), [
        'equal' => 2,
    ], "Replace returned wrong value");

    $this->add($obj->shopList->searchFilter(['soap', 'cheese'])->count(), [
        'equal' => 5,
    ], "Replace returned wrong value");

    $this->add($obj->shopList->search('cheese'), [
        'equal' => 3,
    ], "Replace returned wrong value");


    $this->add($obj->meta->reverse(true)->first() . $obj->meta->reverse(true)->eq('slug'), [
        'equal' => 'lorem-ipsum-dolorlorem-ipsum-dolor',
    ], "Replace returned wrong value");

    $this->add($obj->shopList->reverse()->eq(0), [
        'equal' => 'fish',
    ], "Replace returned wrong value");

    $this->add($obj->shopList->replaceRecursive(['t1' => ['firstname' => 'JANE']])->eq('t1.firstname'), [
        'equal' => 'JANE',
    ], "Replace returned wrong value");

    $this->add($obj->shopList->replace([0 => 'test'], [0 => 'test2'])->eq(0), [
        'equal' => 'test2',
    ], "Replace returned wrong value");

    $value = "";
    $obj->feed->walkRecursive(function ($val, $key) use (&$value) {
        $value .= strip_tags(str_replace(" ", "", $val));
    });

    $this->add($value, [
        'equal' => 'john1doe-14000011jane2doe-22000002',
    ], "Walk Recursive returned wrong value");

    $this->add($obj->shopList->walk(function ($value, $key) {
        if ($key !== 2 && $value !== "milk") {
            $this->unset($key);
        }
    })->count(), [
        'equal' => 1,
    ], "Walk returned wrong count length");

    $this->add($obj->feed->t1->rand(4)->count(), [
        'equal' => 4,
    ], "Rand returned wrong count length");

    $val = $obj->shopList->shuffle()->eq(0) . $obj->shopList->shuffle()->eq(1) . $obj->shopList->shuffle()->eq(2);
    $this->add($val, [
        'notEqual' => 'soaptoothbrushmilk',
    ], "Shuffle did not return a shuffled array, try revalidate one more time");

    // Highly dynamic as you can create new objects on the spot
    $this->add($obj->newShopList->fill(0, 10, 'empty')->count(), [
        'equal' => 10,
    ], "Push returned wrong count length");

    $this->add($obj->newShopList->range(1, 10)->count(), [
        'equal' => 10,
    ], "Push returned wrong count length");

    $this->add($obj->shopList->pad(10, 'empty')->count(), [
        'equal' => 10,
    ], "Push returned wrong count length");

    $this->add($obj->shopList->push('barbie')->count(), [
        'equal' => 8,
    ], "Push returned wrong count length");

    $this->add($obj->shopList->unshift('robot')->count(), [
        'equal' => 8,
    ], "Unshift returned wrong count length");

    $length = $obj->shopList->pop($value)->count();
    $this->add($value, [
        'equal' => 'fish',
    ], "Pop returned wrong value");

    $this->add($length, [
        'equal' => 6,
    ], "Pop returned wrong count length");

    $length = $obj->shopList->shift($value)->count();
    $this->add($value, [
        'equal' => 'soap',
    ], "Shift returned wrong value");

    $this->add($length, [
        'equal' => 6,
    ], "Shift returned wrong count length");

    $this->add(($obj->feed->pluck('lastname')->toArray()[1] ?? 0), [
        'equal' => 'doe-2',
    ], "Pluck returned wrong value");

    $this->add($obj->feed->t1->unset(['firstname', 'lastname'], ['firstname', 'lastname'])->count(), [
        'equal' => 3,
    ], "Unset returned wrong value");

    $arr = $obj->shopList->flip()->toArray();
    $this->add(isset($arr['toothbrush']), [
        'equal' => true,
    ], "Flip returned wrong value");

    ob_start();
    $arr = $obj->feed->each(function ($row, $key) {
        $fullName = $row->firstname->strStripTags()->strToUpper() . " " . $row->lastname;
        echo $fullName . "\n";
        return $fullName;
    });
    $get = ob_get_clean();

    $this->add($arr['t1'] ?? null, [
        'isString' => 1,
        'length' => 10,
    ], 'Each fail from array');

    $this->add($get ?? null, [
        'isString' => 1,
        'length' => 10,
    ], 'Each fail from output buffer');

    $this->add($obj->randList->duplicates()->first(), [
        'equal' => 'lorem'
    ], 'Duplicates returned wrong value');

    $this->add($obj->randList->unique()->count(), [
        'equal' => 6
    ], 'Unique returned wrong value');

    ob_start();
    $obj->feed->dump();
    $get = ob_get_clean();

    $this->add($get, [
        'isString' => 1,
        'length' => 10
    ], 'Diff returned wrong value');

    $this->add($obj->shopList->diff(['soap', 'toothbrush', 'cheese'])->count(), [
        'equal' => 4
    ], 'Diff returned wrong value');

    $this->add($obj->shopList->diffAssoc(['soap', 'toothbrush', 'cheese'])->count(), [
        'equal' => 5
    ], 'Diff Assoc returned wrong value');

    $this->add($obj->feed->t1->diffKey(['lastname' => "Lotte", 'salary' => '20'])->count(), [
        'equal' => 3
    ], 'Diff Key returned wrong value');


    $this->add($obj->email->valid('email'), [
        'isBool' => true,
        'equal' => true
    ], 'Valid returned wrong value');

    $this->add($obj->shopList->first(), [
        'equal' => 'soap'
    ], 'First returned wrong value');

    $this->add($obj->shopList->last(), [
        'equal' => 'fish'
    ], 'Last returned wrong value');

    $this->add($obj->shopList->splice(1, 2, splicedResults: $result)->count(), [
        'equal' => 5
    ], 'Splice returned wrong value');

    $this->add($result->count(), [
        'equal' => 2
    ], 'Spliced Results returned wrong value');

    $this->add($obj->shopList->slice(1, 2)->count(), [
        'equal' => 2
    ], 'Splice returned wrong value');

    $this->add($obj->shopList->prepend(['prepend'])->first(), [
        'equal' => 'prepend'
    ], 'Prepend returned wrong value');

    $this->add($obj->shopList->append(['append'])->last(), [
        'equal' => 'append'
    ], 'Append returned wrong value');

    $flatten = $obj->feed->flatten()->map(function ($row) {
        return $row->strToUpper();
    })->toArray();

    $this->add($flatten, function ($arr) {
        $isStr = true;
        foreach ($arr as $row) {
            if (!is_string($row)) {
                $isStr = false;
                break;
            }
        }
        return $isStr && count($arr) === 10;
    }, "Array flatten failed");


    $map = $obj->feed->map(function ($row) {
        return $row->lastname;
    });

    $this->add($map->t1->lastname->get(), function ($value) {
        return $this->equal("doe-1");
    }, "Map returned wrong value");

    $filter = $obj->feed->filter(function ($row) {
        return ($row->status->get() === 1);
    });

    $this->add($filter->count(), function ($value) {
        return $this->equal(1);
    }, "Filter returned wrong value");


    $reduce = $obj->feed->reduce(function ($carry, $item) {
        return ($carry += $item->salary->get());
    });

    $this->add($reduce->get(), function ($value) {
        return $this->equal(60000);
    }, "Reduse returned wrong value");


    $this->add($obj->shopList->chunk(3)->count(), function ($value) {
        return $this->equal(3);
    }, "Chunk returned wrong value");

    // Then add tests to your case:
    // Test 1: Access the validation instance inside the add closure
    $this->add((string)$obj->feed->t1->firstname, function ($value) {
        return $this->equal('<em>john 1</em>');
    }, "Value should equal to '<em>john 1</em>'");

    $this->add((string)$obj->feed->t1->firstname->strStripTags(), function ($value) {
        return $this->equal('john 1');
    }, "Value should equal to 'john 1'");


    $this->add($obj->feed->fetch(), function ($value) {
        return ($this->isArray() && count($value) === 2);
    }, "Expect fetch to return an array");


    $count = 0;
    foreach ($obj->feed->fetch() as $row) {
        $this->add($row->lastname->get(), function () use (&$count) {
            $count++;
            return $this->equal('doe-' . $count);
        });
    }

    $this->add($obj->feed->t1->firstname->strStripTags()->strUcFirst()->get(), [
        "equal" => $obj->feed->t1->firstname->str()->stripTags()->ucFirst()->get()
    ], "Values does not match");

    $this->add($obj->feed->t1->firstname->strStripTags()->strUcFirst()->get(), [
        "equal" => $obj->feed->t1->firstname->str()->stripTags()->ucFirst()->get()
    ], "Values does not match");

    $val = Arr::value([100 => 'a', 200 => 'b', 201 => 'c', 202 => 'd', 404 => 'e', 403 => 'f'])
        ->unset(200, 201, 202)
        ->arrayKeys() // polyfill class used in Arr
        ->count();

    $this->add($val, [
        "equal" => 3
    ], "Values does not match");


    \MaplePHP\DTO\Format\Clock::setDefaultLanguage('sv_SE');
    $this->add($obj->date->clockFormat('FM', 'is_IS')->get(), [
        "equal" => 'ágústágú.'
    ], "Month translation to is_IS failed");

    $this->add($obj->date->clockFormat('FM')->get(), [
        "equal" => 'augustiaug'
    ], "Month translation to sv_SE failed");

    $this->add($obj->date->clockFormat('lD')->get(), [
        "equal" => 'måndagmån'
    ], "Weekday translation to sv_SE failed");

});