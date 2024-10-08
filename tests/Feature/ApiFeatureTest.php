<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Auth\AuthenticationException;

class ApiFeatureTest extends TestCase
{
    protected $urls;

    public function setUp(): void
    {
        parent::setUp();

        $this->urls = [
            '/api/importacao-boletos-csv',
        ];
    }

    public function testApiSemAutorizacaoDeveRetornarUnauthenticatedAccess()
    {
        foreach ($this->urls as $url) {
            $response = $this->postJson($url, [], ['Accept' => 'application/json']);

            $this->assertInstanceOf(AuthenticationException::class, $response->exception);
            $response->assertStatus(401);
            $response->assertJson(['message' => 'Unauthenticated.']);
        }
    }

    public function testApiComAutorizacaoIncorretaDeveRetornarUnauthenticatedAccess()
    {
        foreach ($this->urls as $url) {
            $response = $this->postJson($url, [], ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->faker->sha256]);

            $this->assertInstanceOf(AuthenticationException::class, $response->exception);
            $response->assertStatus(401);
            $response->assertJson(['message' => 'Unauthenticated.']);
        }
    }
}
