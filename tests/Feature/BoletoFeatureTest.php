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

    public function testApiImportacaoBoletosComPlanilhaCorretaDeveRetornarSucesso()
    {
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\n John Doe,"
            . $this->faker->cpf(false) .
            ",johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";

        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postImportacaoBoleto(['file' => $file]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Processamento enfileirado com sucesso!'
        ]);
    }
}
