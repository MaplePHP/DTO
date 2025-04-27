<?php

use MaplePHP\DTO\Format\Arr;
use MaplePHP\DTO\Format\Str;
use MaplePHP\DTO\Traverse;

$unit = new MaplePHP\Unitary\Unit();

// Begin by adding a test case
$unit->case("MaplePHP DTO String", callback: function () {
    $obj = Traverse::value([
        "id" => 4,
        "firstname" => "<em>daniel</em>",
        "lastname" => "Doe",
        "email" => "john.Doe@gmail.com",
        "slug" => " Lorem_ipsum  åäö_",
        "tagline" => "Lorem ipsum dolor",
        "price" => "1999.99",
        "status" => 1,
        "camel" => "camelCase",
        "site" => "https://www.example.com",
        "site2" => "https://guest:pass@example.com:443/path/to/page/?a=1&b=2#anchor",
        "json" => '{"name":"Alice","email":"alice@example.com","roles":["admin","editor"]}',
        "date" => "2023-08-21 14:35:12",
        "content" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse euismod turpis eget elit eleifend, non pulvinar enim dapibus.\n Nullam blandit vitae justo vitae viverra. Aliquam varius eu leo a euismod.",
        "exportReadable" => "hello \n\t",
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

    $this->add($obj->content->str()->excerpt(50, "---")->get(), [
        "length" => [53, 53],
        "endsWith" => ['---'],
    ], "excerpt: Failed");

    $this->add($obj->email->str()->position(".")->get(), [
        "equal" => 4,
    ], "position: Failed");

    $this->add($obj->email->str()->positionLast(".")->get(), [
        "equal" => 14,
    ], "position: Failed");

    $this->add($obj->email->str()->strlen()->get(), [
        "equal" => 18,
    ], "strlen: Failed");

    $this->add($obj->content->str()->substr(0, 5)->get(), [
        "equal" => "Lorem",
    ], "substr: Failed");

    $this->add($obj->email->str()->getContainAfter("@")->get(), [
        "equal" => "gmail.com",
    ], "getContainAfter required to be true");

    $this->add($obj->exportReadable->str()->exportReadableValue()->get(), [
        "equal" => Str::value("hello \n\t")->exportReadableValue()->get(),
    ], "exportReadableValue required to be true");

    $data = $obj->json->str()->jsonDecode()->get();
    $this->add($data->name ?? "", [
        "equal" => 'Alice',
    ], "jsonDecode: Failed");

    $this->add($obj->email->str()->contains("@gmail")->get(), [
        "equal" => true,
    ], "contains: Failed");

    $this->add($obj->email->str()->getContains("@gmail")->get(), [
        "equal" => "@gmail",
    ], "contains: Failed");

    $this->add($obj->email->str()->startsWith("john")->get(), [
        "equal" => true,
    ], "startsWith: Failed");

    $this->add($obj->email->str()->getStartsWith("john")->get(), [
        "equal" => "john",
    ], "getStartsWith: Failed");

    $this->add($obj->email->str()->endsWith(".com")->get(), [
        "equal" => true,
    ], "endsWith: Failed");

    $this->add($obj->email->str()->getEndsWith(".com")->get(), [
        "equal" => ".com",
    ], "getEndsWith: Failed");

    $this->add($obj->content->str()->nl2br()->contains('<br />')->get(), [
        "equal" => true,
    ], "nl2br: Failed");

    $this->add($obj->site->str()->addTrailingSlash()->get(), [
        "equal" => "https://www.example.com/",
    ], "addTrailingSlash: Failed");


    $this->add($obj->site2->str()->getUrlPath()->trimTrailingSlash()->get(), [
        "equal" => "/path/to/page",
    ], "trimTrailingSlash: Failed");

    $this->add($obj->firstname->str()->encode()->get(), [
        "equal" => "&lt;em&gt;daniel&lt;/em&gt;",
    ], "encode|specialChars: Failed");

    $this->add($obj->firstname->str()->encode()->decode()->get(), [
        "equal" => "<em>daniel</em>",
    ], "decode: Failed");

    $this->add($obj->firstname->str()->sanitizeIdentifiers()->get(), [
        "equal" => "emdanielem",
    ], "sanitizeIdentifiers: Failed");

    $this->add($obj->content->str()->clearBreaks()->nl2br()->contains('<br />')->get(), [
        "equal" => false,
    ], "clearBreaks: Failed");

    $this->add($obj->slug->str()->normalizeSpaces()->strlen()->get(), [
        "equal" => 16,
    ], "normalizeSpaces: Failed");

    $this->add($obj->firstname->str()->entityEncode()->get(), [
        "equal" => "&lt;em&gt;daniel&lt;/em&gt;",
    ], "entityEncode: Failed");

    $this->add($obj->slug->str()->trim()->strlen()->get(), [
        "equal" => 17,
    ], "trim: Failed");

    $this->add($obj->slug->str()->ltrim()->strlen()->get(), [
        "equal" => 17,
    ], "ltrim: Failed");

    $this->add($obj->site2->str()->getUrlPath()->rtrim("/")->strlen()->get(), [
        "equal" => 13,
    ], "rtrim: Failed");

    $this->add($obj->tagline->str()->ucWords()->get(), [
        "equal" => "Lorem Ipsum Dolor",
    ], "ucWords: Failed");

    $this->add($obj->firstname->str()->pad(19, "Test")->strlen()->get(), [
        "equal" => 19,
    ], "pad: Failed");

    $this->add($obj->id->str()->leadingZero()->get(), [
        "equal" => "04",
    ], "leadingZero: Failed");

    $this->add($obj->tagline->str()->replaceSpaces()->get(), [
        "equal" => "Lorem-ipsum-dolor",
    ], "replaceSpaces: Failed");

    $this->add($obj->slug->str()->normalizeSeparators()->trim()->get(), [
        "equal" => "Lorem ipsum åäö",
    ], "normalizeSeparators: Failed");

    $this->add($obj->email->str()->formatEmail()->get(), [
        "equal" => "john.doe@gmail.com",
    ], "formatEmail: Failed");

    $this->add($obj->slug->str()->slug()->get(), [
        "equal" => "lorem-ipsum-aao",
    ], "slug: Failed");

    $this->add($obj->slug->str()->normalizeAccents()->trim()->get(), [
        "equal" => "Lorem_ipsum  aao_",
    ], "normalizeAccents: Failed");

    $this->add($obj->slug->str()->urlEncode()->get(), [
        "equal" => "+Lorem_ipsum++%C3%A5%C3%A4%C3%B6_",
    ], "urlEncode: Failed");

    $this->add($obj->slug->str()->urlEncode()->urldecode()->trim()->get(), [
        "equal" => "Lorem_ipsum  åäö_",
    ], "urldecode: Failed");

    $this->add($obj->slug->str()->rawUrlEncode()->get(), [
        "equal" => "%20Lorem_ipsum%20%20%C3%A5%C3%A4%C3%B6_",
    ], "rawUrlEncode: Failed");

    $this->add($obj->slug->str()->rawUrlEncode()->rawUrldecode()->trim()->get(), [
        "equal" => "Lorem_ipsum  åäö_",
    ], "rawUrldecode: Failed");

    $this->add($obj->site->str()->replace("https://", "dto://")->get(), [
        "equal" => "dto://www.example.com",
    ], "replace: Failed");

    $this->add($obj->slug->str()->rawUrlEncode()->normalizeUrlEncoding()->get(), [
        "equal" => "%20Lorem_ipsum%20%20%C3%A5%C3%A4%C3%B6_",
    ], "normalizeUrlEncoding: Failed");

    $this->add($obj->site2->str()->getUrlPath()->get(), [
        "equal" => "/path/to/page/",
    ], "getUrlPath: Failed");

    $this->add($obj->site2->str()->getUrlUser()->get(), [
        "equal" => "guest",
    ], "getUrlUser: Failed");

    $this->add($obj->site2->str()->getUrlPassword()->get(), [
        "equal" => "pass",
    ], "getUrlPassword: Failed");

    $this->add($obj->site2->str()->getUrlHost()->get(), [
        "equal" => "example.com",
    ], "getUrlHost: Failed");

    $this->add($obj->site2->strGetUrlPort()->toInt(), [
        "equal" => 443,
    ], "strGetUrlPort: Failed");

    $this->add($obj->site2->str()->getUrlQuery()->get(), [
        "equal" => "a=1&b=2",
    ], "getUrlQuery: Failed");

    $this->add($obj->site2->str()->getUrlFragment()->get(), [
        "equal" => "anchor",
    ], "getUrlFragment: Failed");

    $this->add($obj->site2->strGetUrlParts(['user', 'host'])->implode("@")->get(), [
        "equal" => "guest@example.com",
    ], "strGetUrlParts: Failed");

    $this->add($obj->site2->str()->getUrlPath()->getDirname()->get(), [
        "equal" => "/path/to",
    ], "getDirname: Failed");

    $this->add($obj->firstname->str()->escape()->get(), [
        "equal" => "&lt;em&gt;daniel&lt;/em&gt;",
    ], "getDirname: Failed");

    $this->add($obj->camel->strExplodeCamelCase()->count(), [
        "equal" => 2,
    ], "strExplodeCamelCase: Failed");

    $this->add($obj->camel->str()->camelCaseToArr() instanceof Arr, [
        "equal" => true,
    ], "camelCaseToArr: Failed");



});