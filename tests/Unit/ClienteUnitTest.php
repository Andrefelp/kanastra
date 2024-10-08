<?php

namespace Tests\Unit;

use App\Services\ClienteService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ClienteUnitTest extends TestCase
{
    protected $clienteService;

    public function setUp(): void
    {
        parent::setUp();

        $this->clienteService = new ClienteService();
    }

    public function testProcessaClienteSemNomeDeveFalhar()
    {
        $data = [
            'nome' => '',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo nome é obrigatório.');

        $this->clienteService->validateFinal($data);
    }

    public function testProcessaClienteSemNomeValidoDeveFalhar()
    {
        $data = [
            'nome' => 1.4,
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo deve ser do tipo String');

        $this->clienteService->validateFinal($data);
    }

    public function testProcessaClienteNomeMaiorQuePermitidoDeveFalhar()
    {
        $data = [
            'nome' => str_repeat('a', 300),
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo deve ter no máximo 255 caracteres.');

        $this->clienteService->validateFinal($data);
    }

    public function testProcessaClienteSemEmailDeveFalhar() {
        $data = [
            'nome' => 'John',
            'documento' => $this->faker->cpf(false),
            'email' => '',
        ];

        $email = $this->clienteService->validateEmail($data['email']);
        $this->assertNull($email);
    }

    public function testProcessaClienteEmailInvalidoDeveFalhar() {
        $data = [
            'nome' => 'John',
            'documento' => $this->faker->cpf(false),
            'email' => 'a',
        ];

        $email = $this->clienteService->validateEmail($data['email']);
        $this->assertNull($email);

    }

}
