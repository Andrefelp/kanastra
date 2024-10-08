<?php

namespace Tests\Unit;

use App\Models\Boleto;
use App\Models\Cliente;
use Carbon\Carbon;
use Tests\TestCase;
use App\Services\BoletoService;
use \Illuminate\Validation\ValidationException;

class BoletoUnitTest extends TestCase
{

    protected $boletoService;

    public function setUp(): void
    {
        parent::setUp();

        $this->boletoService = new BoletoService();
    }

    public function testProcessaBoletoSemUuidDeveFalhar()
    {
        $data = [
            'uuid' => '',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => '10.00',
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo UUID é obrigatório.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoUuidDuplicadoDeveFalhar()
    {
        $cliente = Cliente::create([
           'nome' => 'John Doe',
           'documento' => $this->faker->cpf(false),
           'email' => $this->faker->email,
        ]);

        $boleto = Boleto::create([
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'cliente_id' => $cliente->id,
            'valor' => '10.00',
            'data_vencimento' => '2024-10-10',
            'created_at' => Carbon::now(),
        ]);

        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => '10.00',
            'data_vencimento' => '2024/10/10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo UUID informado já está cadastrado.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoUuidIncorretoDeveFalhar()
    {
        $data = [
            'uuid' => 'asdgdaf',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => '10.00',
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo UUID deve ter um formato válido.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoSemValorDeveFalhar()
    {
        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => '',
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo valor é obrigatório.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoSemValorNumericoDeveFalhar()
    {
        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => 'abc a',
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo valor deve ser numérico.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoValorZeradoDeveFalhar()
    {
        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => 0.00,
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O valor deve ser maior que 0.00.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoValorNegativoDeveFalhar()
    {
        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => -10.00,
            'data_vencimento' => '2024-10-10',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O valor deve ser maior que 0.00.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoSemVencimentoDeveFalhar()
    {
        $data = [
            'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
            'nome' => 'John Doe',
            'documento' => $this->faker->cpf(false),
            'email' => 'johndoe@kanastra.com.br',
            'valor' => 10.00,
            'data_vencimento' => '',
            '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O campo data de vencimento é obrigatório.');

        $this->boletoService->validate($data);
    }

    public function testProcessaBoletoComFormatoDataInvalidoDeveFalhar()
    {
        $data = [
                'uuid' => '1adb6ccf-ff16-467f-bea7-5f05d494280f',
                'nome' => 'John Doe',
                'documento' => $this->faker->cpf(false),
                'email' => 'johndoe@kanastra.com.br',
                'valor' => '10.00',
                'data_vencimento' => '2024/10/10',
                '1adb6ccf-ff16-467f-bea7-5f05d494280f'
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('O formato da data de vencimento deve ser YYYY-MM-DD.');

        $this->boletoService->validate($data);
    }
}
