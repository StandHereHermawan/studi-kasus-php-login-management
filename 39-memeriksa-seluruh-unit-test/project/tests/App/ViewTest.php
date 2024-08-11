<?php

namespace AriefKarditya\LocalDomainPhp\App;

use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @test
     */
    public function render()
    {
        View::render('Home/index', "PHP Login Management");

        TestCase::expectOutputRegex('[PHP Login Management]');
        TestCase::expectOutputRegex('[html]');
        TestCase::expectOutputRegex('[body]');
        TestCase::expectOutputRegex('[Login Management]');
        TestCase::expectOutputRegex('[Login]');
        TestCase::expectOutputRegex('[Register]');
    }
}
