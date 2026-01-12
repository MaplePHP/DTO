<?php
use MaplePHP\DTO\Format\Clock;
use MaplePHP\DTO\Traverse;
use MaplePHP\Unitary\{Config\TestConfig, Expect, TestCase};

$config = TestConfig::make("Testing clock iso")->withName("clock-iso");

group($config, function(TestCase $case) {

    Clock::setDefaultTimezone('Europe/Stockholm');

    $clockObj = Traverse::value([
        "summer" => "2025-06-03T12:30:00Z",
        "winter" => "2025-01-03T12:30:00Z",
        "localA" => "2025-06-03T12:30:00",
        "localB" => "2025-06-03T12:30:00+02:00",
    ]);

    $case->expect($clockObj->summer->clock()->format('y/m/d \k\l. H:i:s'))
        ->isEqualTo("25/06/03 kl. 14:30:00")
        ->assert("Z-timestamp should be parsed as UTC and converted to Europe/Stockholm (summer time).");

    $case->expect($clockObj->winter->clock()->format('y/m/d \k\l. H:i:s'))
        ->isEqualTo("25/01/03 kl. 13:30:00")
        ->assert("Z-timestamp should be parsed as UTC and converted to Europe/Stockholm (winter time).");

    $case->expect($clockObj->localA->clock()->format('y/m/d \k\l. H:i:s'))
        ->isEqualTo("25/06/03 kl. 12:30:00")
        ->assert("Offset-less ISO 8601 datetime should be treated as local time in Europe/Stockholm.");

    $case->expect($clockObj->localB->clock()->format('y/m/d \k\l. H:i:s'))
        ->isEqualTo("25/06/03 kl. 12:30:00")
        ->assert("Datetime with +02:00 offset should align with local Europe/Stockholm time without shifting wall time.");

});
