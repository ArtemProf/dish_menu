<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseSetup;

    public Generator $faker;

    public string $token;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->faker = Factory::create();
    }

    public static function getTestData(string $filename): string
    {
        return file_get_contents(__DIR__ . '/Data/' . $filename);
    }

    public static function getHttpResponseFromTestData(string $filename): PromiseInterface
    {
        return Http::response(self::getTestData($filename));
    }
}
