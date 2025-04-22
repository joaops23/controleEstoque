<?php
namespace Config;
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// controlar corretamente session após configurar autenticação do usuário
session_start();

// Load environments
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . "/../../");
$dotenv->load();

// ini_set('display_errors', 1);
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// ini_set('error_log', './php_error.log');