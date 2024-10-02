<?php
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP Request URI path test", function() {

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

    // Then add tests to your case:
    // Test 1: Access the validation instance inside the add closure
    $this->add((string)$obj->feed->t1->firstname, function($value) {
        return $this->equal('<em>john 1</em>');
    }, "Value should equal to '<em>john 1</em>'");

    $this->add((string)$obj->feed->t1->firstname->strStripTags(), function($value) {
        return $this->equal('john 1');
    }, "Value should equal to 'john 1'");

    $this->add($obj->feed->fetch(), function($value) {
        return ($this->isArray() && count($value) === 2);
    }, "Expect fetch to return an array");


    $count = 0;
    foreach($obj->feed->fetch() as $row) {
        $this->add($row->lastname->get(), function() use (&$count) {
            $count++;
            return $this->equal('doe ' . $count);
        });
    }

    $this->add($obj->feed->t1->firstname->strStripTags()->strUcFirst()->get(), [
        "equal" => $obj->feed->t1->firstname->str()->stripTags()->ucFirst()->get()
    ], "Values does not match");



    echo MaplePHP\DTO\Format\Str::value("lorem")->ucfirst();

});