<?php

// Configuracion: define ajustes usados por la aplicacion.

namespace Config;

use CodeIgniter\Config\BaseConfig;


/**
 * Enable/disable backward compatibility breaking features.
 */

class Feature extends BaseConfig
{
    
    public bool $autoRoutesImproved = true;

    
    /**
     * Use filter execution order in 4.4 or before.
     */

    public bool $oldFilterOrder = false;

    
    public bool $limitZeroAsAll = true;

    
    /**
     * Use strict location negotiation.
     *
     * By default, the locale is selected based on a loose comparison of the language code (ISO 639-1)
     * Enabling strict comparison will also consider the region code (ISO 3166-1 alpha-2).
     */

    public bool $strictLocaleNegotiation = false;
}
