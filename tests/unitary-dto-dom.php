<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO Dom", callback: function () {
    $obj = Traverse::value([
        "title" => "Lorem ipsum dolor",
        "content" => "lorem ipsum dolor sit amet, consectetur adipiscing elit.",
    ]);

    $this->add($obj->title->domTag("h1")->strStrlen()->get(), [
        "equal" => 28
    ], "domTag: Failed");

    $this->add($obj->title->domTag("h1#title")->strStrlen()->get(), [
        "equal" => 39
    ], "domTag id: Failed");

    $this->add($obj->title->domTag("h1.title")->strStrlen()->get(), [
        "equal" => 42
    ], "domTag add class: Failed");

    $this->add($obj->title->domTag("h1.title")->domTag("header")->strClearBreaks()->strStrlen()->get(), [
        "equal" => 59
    ], "domTag chain: Failed");

    $this->add($obj->title->domBuild(function($dom) {
        return $dom->tag("h1")->class("title");
    })->strStrlen()->get(), [
        "equal" => 42
    ], "domBuild: Failed");

});