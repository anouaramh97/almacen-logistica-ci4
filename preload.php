<?php

/**
 * Este archivo forma parte del marco de trabajo CodeIgniter 4.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * Para la informacion completa de copyright y licencia, consulta
 * el archivo LICENSE distribuido con este codigo fuente.
 */

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * Archivo de ejemplo para precarga
 *---------------------------------------------------------------
 * Consulta https://www.php.net/manual/es/opcache.preloading.php
 *
 * Como usarlo:
 *   0. Copia este archivo en la carpeta raiz del proyecto.
 *   1. Configura la propiedad $paths de la clase preload.
 *   2. Configura opcache.preload en php.ini.
 *     php.ini:
 *     opcache.preload=/path/to/preload.php
 */

// Carga el archivo de configuracion de rutas.
require __DIR__ . '/app/Config/Paths.php';

// Ruta al controlador frontal.
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

class preload
{
    /**
     * @var array Rutas que se van a precargar.
     */
    private array $paths = [
        [
            'include' => __DIR__ . '/vendor/codeigniter4/framework/system', // Cambia esta ruta si usas instalacion manual.
            'exclude' => [
                // No son necesarios si no los usas.
                '/system/Database/OCI8/',
                '/system/Database/Postgre/',
                '/system/Database/SQLite3/',
                '/system/Database/SQLSRV/',
                // No son necesarios para aplicaciones web.
                '/system/Database/Seeder.php',
                '/system/Test/',
                '/system/CLI/',
                '/system/Commands/',
                '/system/Publisher/',
                '/system/ComposerScripts.php',
                // No son archivos de clases ni funciones.
                '/system/Config/Routes.php',
                '/system/Language/',
                '/system/bootstrap.php',
                '/system/util_bootstrap.php',
                '/system/rewrite.php',
                '/Views/',
                // Pueden producir errores.
                '/system/ThirdParty/',
            ],
        ],
    ];

    public function __construct()
    {
        $this->loadAutoloader();
    }

    private function loadAutoloader(): void
    {
        $paths = new Paths();
        require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

        Boot::preload($paths);
    }

    /**
     * Carga archivos PHP.
     */
    public function load(): void
    {
        foreach ($this->paths as $path) {
            $directory = new RecursiveDirectoryIterator($path['include']);
            $fullTree  = new RecursiveIteratorIterator($directory);
            $phpFiles  = new RegexIterator(
                $fullTree,
                '/.+((?<!Test)+\.php$)/i',
                RecursiveRegexIterator::GET_MATCH,
            );

            foreach ($phpFiles as $key => $file) {
                foreach ($path['exclude'] as $exclude) {
                    if (str_contains($file[0], $exclude)) {
                        continue 2;
                    }
                }

                require_once $file[0];
                // Descomenta solo para depurar y ver que archivos se incluyen.
                // No lo uses en produccion: los scripts de precarga no deben generar salida.
                // echo 'Cargado: ' . $file[0] . "\n";
            }
        }
    }
}

(new preload())->load();
