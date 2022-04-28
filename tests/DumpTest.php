<?php

namespace nkoporec\Pmd\Tests;

use nkoporec\Pmd\Pmd;
use PHPUnit\Framework\TestCase;

class PmdTest extends TestCase
{
    /** @var \nkoporec\Pmd\Pmd */
    protected $pmd;

    public function setUp(): void
    {
        parent::setUp();
        $this->pmd = new Pmd();
    }

    /** @test */
    public function it_can_send_string()
    {
        $a = [
            "test",
            "test2",
            "test3",
        ];
        $this->pmd->send($a);
        $this->assertTrue(true);
    }
}