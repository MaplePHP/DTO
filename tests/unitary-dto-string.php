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
        "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse euismod turpis eget elit eleifend, non pulvinar enim dapibus. Nullam blandit vitae justo vitae viverra. Aliquam varius eu leo a euismod.",
    ]);

    $this->add($obj->firstname->str()->stripTags()->ucFirst()->get(), [
        "equal" => 'Daniel'
    ], "stripTags|ucFirst: Failed");

    $this->add($obj->lastname->str()->toUpper()->get(), [
        "equal" => 'DOE'
    ], "toUpper: Failed");

    $this->add($obj->lastname->str()->toLower()->get(), [
        "equal" => 'doe'
    ], "toLower: Failed");

    $this->add($obj->content->str()->excerpt(50, "...")->get(), [
        "equal" => 'doe'
    ], "toLower: Failed");



});