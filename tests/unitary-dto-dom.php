<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;


// Begin by adding a test case
group("MaplePHP DTO Dom", function (\MaplePHP\Unitary\TestCase $case) {
    $obj = Traverse::value([
        "title" => "Lorem ipsum dolor",
        "content" => "lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    ]);

    $case->add($obj->title->domTag("h1")->strStrlen()->get(), [
        "equal" => 28
    ], "domTag: Failed");

    $case->add($obj->title->domTag("h1#title")->strStrlen()->get(), [
        "equal" => 39
    ], "domTag id: Failed");

    $case->add($obj->title->domTag("h1.title")->strStrlen()->get(), [
        "equal" => 42
    ], "domTag add class: Failed");

    $case->add($obj->title->domTag("h1.title")->domTag("header")->strClearBreaks()->strStrlen()->get(), [
        "equal" => 59
    ], "domTag chain: Failed");

    $case->add($obj->title->domBuild(function($dom) {
        return $dom->tag("h1")->class("title");
    })->strStrlen()->get(), [
        "equal" => 42
    ], "domBuild: Failed");

});