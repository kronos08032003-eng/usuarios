<?php
require_once(dirname(__FILE__) . "/../vendor/autoload.php");
use Slim\Factory\AppFactory;
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$usuario = [
   ['id' => 1, 'nome' => 'Ana Silva', 'email' => 'ana@email.com', 'login' => 'ana', 'senha' => '123456'],
   ['id' => 2, 'nome' => 'Bruno Souza', 'email' => 'bruno@email.com', 'login' => 'bruno', 'senha' => '123456'],
   ['id' => 3, 'nome' => 'Carla Lima', 'email' => 'carla@email.com', 'login' => 'carla', 'senha' => '123456'],
   ['id' => 4, 'nome' => 'Diego Santos', 'email' => 'diego@email.com', 'login' => 'diego', 'senha' => '123456'],
   ['id' => 5, 'nome' => 'Maria Oliveira', 'email' => 'maria@email.com', 'login' => 'maria', 'senha' => '123456'],
];
// GET /status
$app->get('/status', function ($request, $response) {
   $response->getBody()->write(json_encode([
       'status' => 'API funcionando'
   ]));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(200);
});
// GET /usuarios
$app->get('/usuarios', function ($request, $response) use ($usuario) {
   $lista = [];
   foreach ($usuario as $u) {
       $u['senha'] = '********';
       $lista[] = $u;
   }
   $response->getBody()->write(json_encode($lista));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(200);
});
// GET /usuarios/{id}
$app->get('/usuarios/{id}', function ($request, $response, $args) use ($usuario) {
   $id = (int) $args['id'];
   $encontrado = null;
   foreach ($usuario as $u) {
       if ($u['id'] === $id) {
           $encontrado = $u;
           break;
       }
   }
   if ($encontrado !== null) {
       $encontrado['senha'] = '********';
       $response->getBody()->write(json_encode($encontrado));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(200);
   }
   $response->getBody()->write(json_encode([
       'erro' => 'Usuário não encontrado'
   ]));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(404);
});
// POST /usuarios
$app->post('/usuarios', function ($request, $response) use (&$usuario) {
   $body = $request->getParsedBody();
   if (
       empty($body['nome']) ||
       empty($body['email']) ||
       empty($body['login']) ||
       empty($body['senha'])
   ) {
       $response->getBody()->write(json_encode([
           'erro' => 'Campos obrigatórios ausentes'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(400);
   }
   $proximoId = $usuario ? max(array_column($usuario, 'id')) + 1 : 1;
   $novo = [
       'id' => $proximoId,
       'nome' => $body['nome'],
       'email' => $body['email'],
       'login' => $body['login'],
       'senha' => $body['senha']
   ];
   $usuario[] = $novo;
   $novo['senha'] = '********';
   $response->getBody()->write(json_encode($novo));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(201);
});
// PUT /usuarios/{id}
$app->put('/usuarios/{id}', function ($request, $response, $args) use (&$usuario) {
   $id = (int) $args['id'];
   $body = $request->getParsedBody();
   $indiceEncontrado = null;
   foreach ($usuario as $key => $u) {
       if ($u['id'] === $id) {
           $indiceEncontrado = $key;
           break;
       }
   }
   if ($indiceEncontrado === null) {
       $response->getBody()->write(json_encode([
           'erro' => 'Usuário não encontrado'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(404);
   }
   if (
       empty($body['nome']) ||
       empty($body['email']) ||
       empty($body['login'])
   ) {
       $response->getBody()->write(json_encode([
           'erro' => 'Campos obrigatórios ausentes'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(400);
   }
   $usuario[$indiceEncontrado]['nome'] = $body['nome'];
   $usuario[$indiceEncontrado]['email'] = $body['email'];
   $usuario[$indiceEncontrado]['login'] = $body['login'];
   $atualizado = $usuario[$indiceEncontrado];
   $atualizado['senha'] = '********';
   $response->getBody()->write(json_encode($atualizado));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(200);
});
// DELETE /usuarios/{id}
$app->delete('/usuarios/{id}', function ($request, $response, $args) use (&$usuario) {
   $id = (int) $args['id'];
   $indiceEncontrado = null;
   foreach ($usuario as $key => $u) {
       if ($u['id'] === $id) {
           $indiceEncontrado = $key;
           break;
       }
   }
   if ($indiceEncontrado === null) {
       $response->getBody()->write(json_encode([
           'erro' => 'Usuário não encontrado'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(404);
   }
   unset($usuario[$indiceEncontrado]);
   return $response->withStatus(204);
});
// POST /login
$app->post('/login', function ($request, $response) use ($usuario) {
   $body = $request->getParsedBody();
   if (empty($body['login']) || empty($body['senha'])) {
       $response->getBody()->write(json_encode([
           'erro' => 'Informe login e senha'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(400);
   }
   foreach ($usuario as $u) {
       if (
           $u['login'] === $body['login'] &&
           $u['senha'] === $body['senha']
       ) {
           $u['senha'] = '********';
           $response->getBody()->write(json_encode([
               'mensagem' => 'Login realizado com sucesso',
               'usuario' => $u
           ]));
           return $response
               ->withHeader('Content-Type', 'application/json')
               ->withStatus(200);
       }
   }
   $response->getBody()->write(json_encode([
       'erro' => 'Login ou senha inválidos'
   ]));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(401);
});
// PUT /usuarios/{id}/senha
$app->put('/usuarios/{id}/senha', function ($request, $response, $args) use (&$usuario) {
   $id = (int) $args['id'];
   $body = $request->getParsedBody();
   $indiceEncontrado = null;
   foreach ($usuario as $key => $u) {
       if ($u['id'] === $id) {
           $indiceEncontrado = $key;
           break;
       }
   }
   if ($indiceEncontrado === null) {
       $response->getBody()->write(json_encode([
           'erro' => 'Usuário não encontrado'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(404);
   }
   if (empty($body['senhaAtual']) || empty($body['novaSenha'])) {
       $response->getBody()->write(json_encode([
           'erro' => 'Informe a senha atual e a nova senha'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(400);
   }
   if ($usuario[$indiceEncontrado]['senha'] !== $body['senhaAtual']) {
       $response->getBody()->write(json_encode([
           'erro' => 'Senha atual incorreta'
       ]));
       return $response
           ->withHeader('Content-Type', 'application/json')
           ->withStatus(401);
   }
   $usuario[$indiceEncontrado]['senha'] = $body['novaSenha'];
   $response->getBody()->write(json_encode([
       'mensagem' => 'Senha alterada com sucesso'
   ]));
   return $response
       ->withHeader('Content-Type', 'application/json')
       ->withStatus(200);
});
return $app;
