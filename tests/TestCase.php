<?php

namespace Tests;

use Faker\Factory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected $faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create('pt_BR');
        DB::connection()->beginTransaction();
    }

    public function tearDown(): void
    {
        DB::connection()->rollBack();
        DB::connection()->disconnect();

        parent::tearDown();
    }
}
