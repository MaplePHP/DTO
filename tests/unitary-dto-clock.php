<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO Clock", callback: function () {
    $obj = Traverse::value([
        "date" => "2023-05-21 14:35:12",
        "birth" => "1988-08-21 14:35:12",
    ]);

    $obj->date->clock()::setDefaultTimezone("Europe/Stockholm");

    $this->add($obj->date->clock()->format("Y/m/d"), [
        "equal" => '2023/05/21'
    ], "format: Failed");

    $this->add($obj->date->clock()->format("M", "sv_SE"), [
        "equal" => 'maj'
    ], "format SV_se: Failed");

    $this->add($obj->date->clock()->setLocale("fr_FR")->format("M"), [
        "equal" => 'mai'
    ], "setLocale fr_FR: Failed");

    $this->add($obj->date->clock()->setLocale("fr_FR")->format("M"), [
        "equal" => 'mai'
    ], "setLocale fr_FR: Failed");

    $this->add($obj->date->clock()->dateTime(), [
        "equal" => '2023-05-21 16:35:12'
    ], "dateTime: Failed");

    $this->add($obj->date->clock()->date(), [
        "equal" => '2023-05-21'
    ], "date: Failed");

    $this->add($obj->date->clock()->time(), [
        "equal" => '16:35'
    ], "time: Failed");

    $this->add($obj->date->clock()->timestamp(), [
        "equal" => '1684679712'
    ], "timestamp: Failed");

    $this->add($obj->date->clock()->year(), [
        "equal" => '2023'
    ], "year: Failed");

    $this->add($obj->date->clock()->year(true), [
        "equal" => '23'
    ], "year (shorthand): Failed");

    $this->add($obj->date->clock()->month(), [
        "equal" => '05'
    ], "month: Failed");

    $this->add($obj->date->clock()->monthName(), [
        "equal" => 'May'
    ], "monthName: Failed");

    $this->add($obj->birth->clock()->shortMonthName(), [
        "equal" => 'Aug'
    ], "shortMonthName: Failed");

    $this->add($obj->date->clock()->day(), [
        "equal" => '21'
    ], "day: Failed");

    $this->add($obj->date->clock()->dayOfWeek(), [
        "equal" => 7
    ], "dayOfWeek: Failed");

    $this->add($obj->date->clock()->weekday(), [
        "equal" => "Sunday"
    ], "weekday: Failed");

    $this->add($obj->date->clock()->shortWeekday(), [
        "equal" => "Sun"
    ], "shortWeekday: Failed");

    $this->add($obj->date->clock()->seconds(), [
        "equal" => '12'
    ], "seconds: Failed");

    $this->add($obj->date->clock()->isLeapYear(), [
        "equal" => false
    ], "isLeapYear: Failed");

    $this->add($obj->date->clock()->weekNumber(), [
        "equal" => 20
    ], "weekNumber: Failed");

    $this->add($obj->date->clock()->iso(), [
        "equal" => "2023-05-21T16:35:12+02:00"
    ], "iso: Failed");

    $this->add($obj->date->clock()->rfc(), [
        "equal" => "Sun, 21 May 2023 16:35:12 +0200"
    ], "rfc: Failed");

    $this->add($obj->date->clock()->time12Hour(), [
        "equal" => "04:35 PM"
    ], "time12Hour: Failed");

    $this->add($obj->date->clock()->diffInDays(), [
        "lessThan" => 0
    ], "diffInDays: Failed");

    $this->add($obj->date->clock()->isToday(), [
        "equal" => false
    ], "isToday: Failed");

    $this->add($obj->date->clock()->timezone(), [
        "equal" => "Europe/Stockholm"
    ], "timezone|setDefaultTimezone: Failed");

    $this->add($obj->date->clock()->setTimezone("UTC")->timezone(), [
        "equal" => "UTC"
    ], "setTimezone: Failed");


    // ALWAYS CALL AT THE END
    $obj->date->clock()::setDefaultLocale("fi_FI");
    $this->add($obj->date->clock()->format("M"), [
        "equal" => 'toukokuu'
    ], "setDefaultLocale fi_FI: Failed");

});