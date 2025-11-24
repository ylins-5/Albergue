<?php

// =======================================================================
// 1. CONFIGURAÇÃO DE CORS E SESSÃO (Adicionado)
// =======================================================================

// Permite acesso de qualquer origem (necessário para credentials funcionarem)
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // Cache por 1 dia
}

// Controla as requisições OPTIONS (Preflight) antes de iniciar o App
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

    exit(0);
}

// Inicia a sessão PHP para salvar o login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =======================================================================
// 2. INICIALIZAÇÃO DA APP
// =======================================================================

require_once __DIR__ . '/../App/Core/Router.php';
require_once __DIR__ . '/../App/Core/Database.php';
require_once __DIR__ . '/../App/Controllers/UserController.php';
require_once __DIR__ . '/../App/Controllers/RoomController.php';
require_once __DIR__ . '/../App/Controllers/BedController.php';
require_once __DIR__ . '/../App/Controllers/TagController.php';
require_once __DIR__ . '/../App/Controllers/TagQuartoController.php';
require_once __DIR__ . '/../App/Controllers/TagCamaController.php';
require_once __DIR__ . '/../App/Controllers/ReservaController.php';

use App\Core\Router;
use App\Core\Database;
use App\Controllers\UserController;
use App\Controllers\RoomController;
use App\Controllers\BedController;
use App\Controllers\TagController;
use App\Controllers\TagCamaController;
use App\Controllers\TagQuartoController;
use App\Controllers\ReservaController;

$router = new Router();

$userController = new UserController();
$roomController = new RoomController();
$bedController = new BedController();
$tagController = new TagController();
$tagQuartoController = new TagQuartoController();
$tagCamaController = new TagCamaController();
$reservaController = new ReservaController();

// =======================================================================
// 3. DEFINIÇÃO DE ROTAS
// =======================================================================

$router->get('/hello', function () {
    echo json_encode(['message' => 'API funcionando!']);
});

// --- Rotas de Usuário ---
$router->get('/usuarios', function () use ($userController){
    $userController->getAll();
});

$router->get('/usuarios/{id}', function ($id) use ($userController){
    $userController->getById($id);
});

$router->post('/usuarios', function () use ($userController){ // Cadastro
    $userController->create();
});

$router->put('/usuarios/{id}', function ($id) use ($userController){
    $userController->update($id);
});

$router->delete('/usuarios/{id}', function ($id) use ($userController){
    $userController->delete($id);
});

// --- Rotas de Login e Sessão (ESSENCIAIS PARA O FRONTEND) ---

$router->post('/login', function() use ($userController) {
    // Certifique-se que no seu UserController->login() você está fazendo:
    // $_SESSION['user_id'] = $usuario['id'];
    $userController->login();
});

// Rota adicionada para o JavaScript verificarSessao()
$router->get('/session', function() {
    header('Content-Type: application/json');
    if (isset($_SESSION['user_id']) || isset($_SESSION['user'])) {
        // Retorna os dados da sessão se existirem
        $user = isset($_SESSION['user']) ? $_SESSION['user'] : ['id' => $_SESSION['user_id']];
        echo json_encode(['authenticated' => true, 'user' => $user]);
    } else {
        echo json_encode(['authenticated' => false]);
    }
});

// Rota adicionada para o Logout
$router->post('/logout', function() {
    session_destroy();
    echo json_encode(['message' => 'Logout realizado']);
});

// --- Rotas de Quartos ---
$router->get('/quartos', function () use ($roomController){
    $roomController->getAll();
});

$router->get('/quartos/{id}', function ($id) use ($roomController){
    $roomController->getById($id);
});

$router->post('/quartos', function () use ($roomController){
    $roomController->create();
});

$router->put('/quartos/{id}', function ($id) use ($roomController){
    $roomController->update($id);
});

$router->delete('/quartos/{id}', function ($id) use ($roomController){
    $roomController->delete($id);
});

$router->get('/quartos/{id}/camas', function ($id) use ($bedController){
    $bedController->getByRoomId($id);
});

// --- Rotas de Camas ---
$router->get('/camas', function () use ($bedController){
    $bedController->getAll();
});

$router->get('/camas/{id}', function ($id) use ($bedController){
    $bedController->getById($id);
});

$router->post('/camas', function () use ($bedController){
    $bedController->create();
});

$router->put('/camas/{id}', function ($id) use ($bedController){
    $bedController->update($id);
});

$router->delete('/camas/{id}', function ($id) use ($bedController){
    $bedController->delete($id);
});

// --- Rotas de Tags ---
$router->get('/tags', function () use ($tagController){
    $tagController->index();
});

$router->get('/tags/{id}', function ($id) use ($tagController){
    $tagController->show($id);
});

$router->post('/tags', function () use ($tagController){
    $tagController->store();
});

$router->put('/tags/{id}', function ($id) use ($tagController){
    $tagController->update($id);
});

$router->delete('/tags/{id}', function ($id) use ($tagController){
    $tagController->delete($id);
});

$router->get('/tags/tipo/{tipo}', function ($tipo) use ($tagController) {
    $tagController->findByType($tipo);
});

// --- Rotas Tag-Quartos ---
$router->get('/tag-quartos', function () use ($tagQuartoController) {
    $tagQuartoController->getAll();
});

$router->get('/tag-quartos/{tag_id}/{quarto_id}', function ($tag_id, $quarto_id) use ($tagQuartoController) {
    $tagQuartoController->getById($tag_id, $quarto_id);
});

$router->get('/tag-quartos/tag/{tag_id}', function ($tag_id) use ($tagQuartoController) {
    $tagQuartoController->getByTag($tag_id);
});

$router->get('/tag-quartos/quarto/{quarto_id}', function ($quarto_id) use ($tagQuartoController) {
    $tagQuartoController->getByQuarto($quarto_id);
});

$router->post('/tag-quartos', function () use ($tagQuartoController) {
    $tagQuartoController->create();
});

$router->delete('/tag-quartos/{tag_id}/{quarto_id}', function ($tag_id, $quarto_id) use ($tagQuartoController) {
    $tagQuartoController->delete($tag_id, $quarto_id);
});

// --- Rotas Tag-Camas ---
$router->get('/tag-camas', function () use ($tagCamaController) {
    $tagCamaController->getAll();
});

$router->get('/tag-camas/tag/{tag_id}', function ($tag_id) use ($tagCamaController) {
    $tagCamaController->getByTag($tag_id);
});

$router->get('/tag-camas/cama/{cama_id}', function ($cama_id) use ($tagCamaController) {
    $tagCamaController->getByCama($cama_id);
});

$router->get('/tag-camas/{tag_id}/{cama_id}', function ($tag_id, $cama_id) use ($tagCamaController) {
    $tagCamaController->getById($tag_id, $cama_id);
});

$router->post('/tag-camas', function () use ($tagCamaController) {
    $tagCamaController->create();
});

$router->delete('/tag-camas/{tag_id}/{cama_id}', function ($tag_id, $cama_id) use ($tagCamaController) {
    $tagCamaController->delete($tag_id, $cama_id);
});

// --- Rotas de Reservas ---
$router->get('/reservas/disponiveis', function () use ($reservaController) {
    $reservaController->availableBeds();
});

$router->get('/reservas', function () use ($reservaController) {
    $reservaController->index();
});

$router->get('/reservas/{id}', function ($id) use ($reservaController) {
    $reservaController->show($id);
});

$router->post('/reservas', function () use ($reservaController) {
    $reservaController->store();
});

$router->put('/reservas/{id}', function ($id) use ($reservaController) {
    $reservaController->update($id);
});

$router->delete('/reservas/{id}', function ($id) use ($reservaController) {
    $reservaController->destroy($id);
});

// =======================================================================
// 4. DISPATCH
// =======================================================================

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);