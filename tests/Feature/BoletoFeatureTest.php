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

    public function testApiImportacaoBoletosSemArquivoAnexadoDeveRetornarErroArquivoObrigatorio()
    {
        $response = $this->postJson(
            $this->url,
            [],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ]);

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

        $response = $this->postJson(
            $this->url,
            [
                'file' => $file,
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ]);

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

    public function testApiImportacaoBoletosComExtensaoCsvDeveRetornarSucesso()
    {
        $csvContent = "conteudoInternoCSV";
        $file = UploadedFile::fake()->createWithContent('boletos.csv', $csvContent, 'text/csv');

        $response = $this->postJson(
            $this->url,
            [
                'file' => $file,
            ],
            [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sucesso!'
        ]);
    }
}
