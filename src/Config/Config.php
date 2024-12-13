<?php
namespace Config;

use Dotenv\Dotenv;

// controlar corretamente session após configurar autenticação do usuário
session_start();

// Load environments
$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . "/../../");
$dotenv->load();
