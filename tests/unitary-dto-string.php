<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO test", callback: function () {
    $obj = Traverse::value([
        "firstname" => "<em>daniel</em>",
        "lastname" => "Doe",
        "email" => "john.doe@gmail.com",
        "slug" => "Lorem ipsum åäö",
        "price" => "1999.99",
        "date" => "2023-08-21 14:35:12",
    ]);

    $this->add($obj->firstname->str()->stripTags()->ucFirst()->get(), [
        "equal" => 'Daniel'
    ], "Month translation to is_IS failed");

    $this->add($obj->firstname->str()->stripTags()->toUpper()->get(), [
        "equal" => 'DANIEL'
    ], "Failed toUpper");

    $this->add($obj->lastname->str()->toLower()->get(), [
        "equal" => 'doe'
    ], "Failed toLower");
});