<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO Number", callback: function () {
    $obj = Traverse::value([
        "price" => 1199.33,
        "neg" => -9999.99,
        "count" => 1,
        "numFormat" => "1 999.99",
    ]);

    $obj->num()::setDefaultLocale("sv_SE");

    $this->add($obj->price->numToCurrency("SEK")->strEndsWith("kr")->get(), [
        "equal" => true
    ], "toCurrency|setDefaultLocale|strEndsWith Failed");

    $this->add($obj->price->num()->getCurrencySymbol("SEK")->get(), [
        "equal" => "kr"
    ], "getCurrencySymbol Failed");

    $this->add($obj->price->numToCurrencyIso("SEK")->strStartsWith("SEK")->get(), [
        "equal" => true
    ], "toCurrencyIso Failed");

    $this->add($obj->price->num()->float()->get(), [
        "isFloat" => true
    ], "float Failed");

    $this->add($obj->price->num()->int()->get(), [
        "isInt" => true
    ], "int Failed");

    $this->add($obj->price->num()->floor()->int()->get(), [
        "equal" => 1199
    ], "floor Failed");

    $this->add($obj->price->num()->ceil()->int()->get(), [
        "equal" => 1200
    ], "ceil Failed");

    $this->add($obj->neg->num()->abs()->int()->get(), [
        "equal" => 9999
    ], "abs Failed");

    $this->add($obj->price->num()->numberFormat(2)->get(), [
        "equal" => "1,199.33"
    ], "numberFormat Failed");

    $this->add($obj->count->num()->leadingZero()->get(), [
        "equal" => "01"
    ], "leadingZero Failed");

    $this->add($obj->price->num()->clamp(1, 20)->int()->get(), [
        "equal" => 20
    ], "clamp Failed");

    $this->add($obj->price->num()->isEven()->get(), [
        "equal" => false
    ], "isEven Failed");

    $this->add($obj->price->num()->isOdd()->get(), [
        "equal" => true
    ], "isOdd Failed");

});