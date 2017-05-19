<?php

function b() {}
function a() {}

class Foo
{
    public function _a() {}
    public function a() {}
    public function b() {}
    public function c() {}
}

function d() {}
function c() {}

abstract class Bar
{
    public function b() {}
    public function a() {}
}

function f() {}
function e() {}

interface FooInterface
{
    public function d() {}
    public function a() {}
    public function c() {}
    public function b() {}
}

function h() {}
function g() {}

trait FooTrait
{
    public function a() {}
    public function _a() {}
    public function b() {}
}

?>
