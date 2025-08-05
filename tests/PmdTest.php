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
    public function it_can_send_std_class()
    {
        $class = new \stdClass();
        $class->class = "class";
        $class->item = "item";
        $result = $this->pmd->send($class);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_basic_object()
    {
        $class = new TestObject();
        $result = $this->pmd->send($class);

        $this->assertEquals("200", $result);
    }

    /** @test */
    public function it_can_send_custom_drupal_style_class()
    {
        $object = new SimulatedDrupalService();
        $result = $this->pmd->send($object);

        $this->assertEquals("200", $result);
    }
}

// A simple object for basic structure testing
class TestObject
{
    public $class = "class";

    public function test()
    {
        return "test";
    }
}

// A simulated "Drupal-style" service-like object
class SimulatedDrupalService
{
    public string $id = 'my_service_id';

    protected array $config = [
        'cache' => true,
        'mode' => 'prod',
    ];

    private string $secret = 'this_should_not_be_visible';

    public function getId(): string
    {
        return $this->id;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function internalLogic(): string
    {
        return 'internal';
    }

    private function verySecretLogic(): string
    {
        return 'shhh';
    }
}
