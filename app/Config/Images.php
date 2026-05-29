<?php

// Configuracion: define ajustes usados por la aplicacion.

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Images\Handlers\GDHandler;
use CodeIgniter\Images\Handlers\ImageMagickHandler;

class Images extends BaseConfig
{
    
    /**
     * Default handler used if no other handler is specified.
     */

    public string $defaultHandler = 'gd';

    
    public string $libraryPath = '/usr/local/bin/convert';

    
    /**
     * The available handler classes.
     *
     * @var array<string, string>
     */

    public array $handlers = [
        'gd'      => GDHandler::class,
        'imagick' => ImageMagickHandler::class,
    ];
}
