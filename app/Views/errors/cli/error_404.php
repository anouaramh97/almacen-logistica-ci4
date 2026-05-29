<?php

// Vista de error: salida controlada para excepciones, errores HTTP o CLI.

use CodeIgniter\CLI\CLI;

CLI::error('ERROR: ' . $code);
CLI::write($message);
CLI::newLine();
