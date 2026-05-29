<?php

// Configuracion: define ajustes usados por la aplicacion.

namespace Config;


/**
 * This configuration controls how CodeIgniter behaves when running
 * in worker mode (with FrankenPHP).
 */

class WorkerMode
{
    
    public array $persistentServices = [
        'autoloader',
        'locator',
        'exceptions',
        'commands',
        'codeigniter',
        'superglobals',
        'routes',
        'cache',
    ];

    
    /**
     * Force Garbage Collection
     *
     * Whether to force garbage collection after each request.
     * Helps prevent memory leaks at a small performance cost.
     */

    public bool $forceGarbageCollection = true;
}
