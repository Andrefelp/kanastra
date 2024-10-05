<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::connection()->beginTransaction();
    }

    public function tearDown(): void
    {
        DB::connection()->rollBack();
        DB::connection()->disconnect();

        parent::tearDown();
    }
}
