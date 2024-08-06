<?php

namespace AriefKarditya\LocalDomainPhp;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class RegexTest extends TestCase
{
    /**
     * @test
     */
    public function RegexTry()
    {
        $path = "/products/12345/categories/abcde";

        $pattern = "#^/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)$#";

        $result = preg_match($pattern, $path, $variables);

        Assert::assertEquals(1, $result);

        var_dump($variables);

        array_shift($variables); # Menghapus isi array dengan index 0.
        var_dump($variables);
    }
}
