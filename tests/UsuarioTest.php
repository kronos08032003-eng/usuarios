<?php

use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;

class UsuarioTest extends TestCase
{
    private function getApp()
    {
        // Garante o isolamento do escopo e o retorno correto da instância $app
        return (static function() {
            return require __DIR__ . '/../src/app.php';
        })();
    }

    // 1. TESTE DO GET /status
    public function test_deve_retornar_status_da_api(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/status')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        $this->assertEquals('API funcionando', $corpo['status']);
    }

    // 2. TESTE DO GET /usuarios (Listar todos)
    public function test_deve_listar_todos_os_usuarios(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/usuarios')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertIsArray($corpo);
        $this->assertEquals('Ana Silva', $corpo[0]['nome']);
        $this->assertEquals('********', $corpo[0]['senha']); // Garante que a senha veio mascarada
    }

    // 3. TESTE DO GET /usuarios/{id} (Buscar por ID)
    public function test_deve_buscar_usuario_por_id(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('GET', '/usuarios/1')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals(1, $corpo['id']);
        $this->assertEquals('Ana Silva', $corpo['nome']);
        $this->assertEquals('********', $corpo['senha']);
    }

    // 4. TESTE DO POST /usuarios (Criar usuário)
    public function test_deve_criar_novo_usuario(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/usuarios')
            ->withHeader('Content-Type', 'application/json');

        $dadosNovoUsuario = [
            'nome' => 'Carlos Rocha',
            'email' => 'carlos@email.com',
            'login' => 'carlos',
            'senha' => 'abc123'
        ];
        
        $request->getBody()->write(json_encode($dadosNovoUsuario));
        $request->getBody()->rewind();
        $request = $request->withParsedBody($dadosNovoUsuario);

        $response = $app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Carlos Rocha', $corpo['nome']);
        $this->assertEquals('********', $corpo['senha']); // Retorno deve vir mascarado
        $this->assertEquals(6, $corpo['id']); // Próximo ID automático deve ser 6
    }

    // 5. TESTE DO PUT /usuarios/{id} (Atualizar dados)
    public function test_deve_atualizar_dados_do_usuario(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/usuarios/2')
            ->withHeader('Content-Type', 'application/json');

        $dadosAtualizados = [
            'nome' => 'Bruno Souza Editado',
            'email' => 'bruno_novo@email.com',
            'login' => 'bruno.souza'
        ];

        $request->getBody()->write(json_encode($dadosAtualizados));
        $request->getBody()->rewind();
        $request = $request->withParsedBody($dadosAtualizados);

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Bruno Souza Editado', $corpo['nome']);
        $this->assertEquals('bruno_novo@email.com', $corpo['email']);
    }

    // 6. TESTE DO DELETE /usuarios/{id} (Deletar usuário)
    public function test_deve_deletar_usuario(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('DELETE', '/usuarios/3')
            ->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    // 7. TESTE DO POST /login (Autenticação com sucesso)
    public function test_deve_realizar_login_com_sucesso(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('POST', '/login')
            ->withHeader('Content-Type', 'application/json');

        $credenciais = [
            'login' => 'ana',
            'senha' => '123456'
        ];

        $request->getBody()->write(json_encode($credenciais));
        $request->getBody()->rewind();
        $request = $request->withParsedBody($credenciais);

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Login realizado com sucesso', $corpo['mensagem']);
        $this->assertEquals('Ana Silva', $corpo['usuario']['nome']);
    }

    // 8. TESTE DO PUT /usuarios/{id}/senha (Alterar senha com sucesso)
    public function test_deve_alterar_senha_do_usuario(): void
    {
        $app = $this->getApp();
        $request = (new ServerRequestFactory())->createServerRequest('PUT', '/usuarios/4/senha')
            ->withHeader('Content-Type', 'application/json');

        $dadosSenha = [
            'senhaAtual' => '123456',
            'novaSenha' => 'senha_segura'
        ];

        $request->getBody()->write(json_encode($dadosSenha));
        $request->getBody()->rewind();
        $request = $request->withParsedBody($dadosSenha);

        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $corpo = json_decode((string) $response->getBody(), true);
        
        $this->assertEquals('Senha alterada com sucesso', $corpo['mensagem']);
    }
}
