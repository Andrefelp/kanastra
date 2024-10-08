<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BoletoFeatureTest extends TestCase
{
    protected $user;
    protected $token;
    protected $url;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('API Token')->plainTextToken;

        $this->url = '/api/importacao-boletos-csv';
    }

    protected function postImportacaoBoleto(array $data)
    {
        return $this->postJson(
            $this->url,
            $data,
            ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->token]
        );
    }

    public function testApiImportacaoBoletosSemArquivoAnexadoDeveRetornarErroArquivoObrigatorio()
    {
        $data = [];
        $response = $this->postImportacaoBoleto($data);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'É obrigatório inserir um arquivo.',
            'errors' => [
                'file' => [
                    'É obrigatório inserir um arquivo.',
                ],
            ],
        ]);
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function testApiImportacaoBoletosSemExtensaoCsvDeveRetornarErroFormato()
    {
        $file = UploadedFile::fake()->create('boletos.txt', 100, 'text');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O arquivo deve ser do formato CSV.',
            'errors' => [
                'file' => [
                    'O arquivo deve ser do formato CSV.',
                ],
            ],
        ]);
        $this->assertInstanceOf(ValidationException::class, $response->exception);
    }

    public function testApiImportacaoBoletosComApenasUmaLinhaDeveRetornarErroTamanhoArquivo()
    {
        $csvContent = "unica linha";
        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);

        $response->assertJson([
            'message' => 'O arquivo só possui uma linha.'
        ]);
    }

    public function testApiImportacaoBoletosComCpfIncorretoValoresIguaisDeveRetornarErroCpfInvalido()
    {
        $csvContent = 'name,governmentId,email,debtAmount,debtDueDate,debtId\n
        John Doe,11111111111,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f';

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O CPF é inválido.'
        ]);
    }

    public function testApiImportacaoBoletosComCpfIncorretoValorAleatorioDeveRetornarErroCpfInvalido()
    {
        $csvContent = 'name,governmentId,email,debtAmount,debtDueDate,debtId\n
        John Doe,12345678910,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f';

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O CPF é inválido.'
        ]);
    }

    public function testApiImportacaoBoletosComCpfCnpjIncorretoNaoPossuiOnzeOuQuatorzeCaracteresDeveRetornarErroCpfCnpjInvalido()
    {
        $csvContent = 'name,governmentId,email,debtAmount,debtDueDate,debtId\n
        John Doe,1234,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f';

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Não foi informado um CNPJ/CPF válido.'
        ]);
    }

    public function testApiImportacaoBoletosComCnpjIncorretoDeveRetornarErroCpfInvalido()
    {
        $csvContent = 'name,governmentId,email,debtAmount,debtDueDate,debtId\n
        John Doe,12345678910111,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f';

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O CNPJ é inválido.'
        ]);
    }

    public function testApiImportacaoBoletosSemUuidIncorretoDeveRetornarErroUuidObrigatorio()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,1000000.00,2022-10-12,";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O campo UUID é obrigatório.'
        ]);
    }

    public function testApiImportacaoBoletosComUuidIncorretoDeveRetornarErroUuidValido()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,1000000.00,2022-10-12,valor_uuid_incorreto";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O campo UUID deve ter um formato válido.'
        ]);
    }

    public function testApiImportacaoBoletosSemValorDeveRetornarErroValorObrigatorio()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O campo valor é obrigatório.'
        ]);
    }

    public function testApiImportacaoBoletosComValorNegativoDeveRetornarErroValorMaiorQueZero()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,-1.0,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O valor deve ser maior que 0.00.'
        ]);
    }

    public function testApiImportacaoBoletosComValorZeradoDeveRetornarErroValorMaiorQueZero()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,0.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O valor deve ser maior que 0.00.'
        ]);
    }

    public function testApiImportacaoBoletosComValorNaoNumericoDeveRetornarErroValorNumerico()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,abc,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O campo valor deve ser numérico.'
        ]);
    }

    public function testApiImportacaoBoletosSemDataVencimentoDeveRetornarErroVencimentoObrigatorio()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,10.00,,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O campo data de vencimento é obrigatório.'
        ]);
    }

    public function testApiImportacaoBoletosComFormatoDataDeVencimentoInvalidoDeveRetornarErroFormatoInvalido()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,10.00,2024/10/10,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'O formato da data de vencimento deve ser YYYY-MM-DD.'
        ]);
    }

    public function testApiImportacaoBoletosComPlanilhaCorretaDeveRetornarSucesso()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sucesso!'
        ]);
    }
}
