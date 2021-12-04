<?php

namespace nkoporec\Pmd\Tests;

use PHPUnit\Framework\TestCase;
use nkoporec\Pmd\Pmd;

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
        $this->pmd->send('test');
        $this->assertTrue(true);
    }
}
