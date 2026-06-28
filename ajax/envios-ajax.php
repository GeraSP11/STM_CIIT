<?php
/**
 * ajax.php
 *
 * Punto de entrada único para todas las peticiones AJAX del módulo de
 * Programación de Envíos. El JS del frontend siempre llama a este
 * archivo con un parámetro "action".
 */

declare(strict_types=1);

require_once '../backend/controllers/envios-controller.php';

$accion = $_GET['action'] ?? $_POST['action'] ?? '';

$controlador = new EnviosController();
$controlador->manejarSolicitud((string) $accion);
