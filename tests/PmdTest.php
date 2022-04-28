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
    public function it_can_send_int()
    {
        $int = 12;
        $result = $this->pmd->send($int);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_float()
    {
        $float = 12.12;
        $result = $this->pmd->send($float);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_string()
    {
        $string = "test string";
        $result = $this->pmd->send($string);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_array()
    {
        $array = [
            "string",
            "string2",
        ];
        $result = $this->pmd->send($array);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_multi_dim_array()
    {
        $array = [
            "string",
            [
                "string2",
            ],
        ];
        $result = $this->pmd->send($array);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_class()
    {
        $class = new \stdClass();
        $class->class = "class";
        $class->item = "item";
        $result = $this->pmd->send($class);

        $this->assertEquals("200", $result);
    }
}
