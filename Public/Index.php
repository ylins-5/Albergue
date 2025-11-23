<?php

require_once __DIR__ . '/../app/Core/Router.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Controllers/RoomController.php';
require_once __DIR__ . '/../app/Controllers/BedController.php';
require_once __DIR__ . '/../app/Controllers/TagController.php';
require_once __DIR__ . '/../app/Controllers/TagQuartoController.php';
require_once __DIR__ . '/../app/Controllers/TagCamaController.php';
require_once __DIR__ . '/../app/Controllers/ReservaController.php';

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
$ReservaController = new ReservaController();

$router->get('/hello', function () {
    echo json_encode(['message' => 'API funcionando!']);
});

$router->get('/usuarios', function () use ($userController){
    $userController->getAll();
});

$router->get('/usuarios/{id}', function ($id) use ($userController){
    $userController->getById($id);
});

$router->post('/usuarios', function () use ($userController){
    $userController->create();
});

$router->put('/usuarios/{id}', function ($id) use ($userController){
    $userController->update($id);
});

$router->delete('/usuarios/{id}', function ($id) use ($userController){
    $userController->delete($id);
});

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

$router->get('/reservas/disponiveis', function () use ($reservaController) {
    $reservaController->availableBeds();
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
