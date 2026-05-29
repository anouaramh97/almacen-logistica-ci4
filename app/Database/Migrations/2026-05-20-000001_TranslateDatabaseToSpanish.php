<?php

// Migracion: crea o modifica la estructura de la base de datos.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Define cambios estructurales de base de datos que se pueden aplicar o revertir.
 */
class TranslateDatabaseToSpanish extends Migration
{
    /**
     * Aplica los cambios de esta migracion en la base de datos.
     */
    public function up()
    {
        $this->dropCompatibilityViews();

        $this->renameTablesToSpanish();
        $this->renameColumnsToSpanish();
        $this->translateStoredValuesToSpanish();
        $this->createCompatibilityViews();
    }

    /**
     * Revierte los cambios aplicados por esta migracion.
     */
    public function down()
    {
        $this->dropCompatibilityViews();
        $this->translateStoredValuesToEnglish();
        $this->renameColumnsToEnglish();
        $this->renameTablesToEnglish();
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function dropCompatibilityViews(): void
    {
        foreach (['messages', 'conversations', 'invoices', 'deliveries', 'routes', 'order_items', 'orders', 'stocks', 'warehouses', 'product_images', 'products', 'categories', 'users', 'roles'] as $view) {
            $this->db->query("DROP VIEW IF EXISTS {$view}");
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function renameTablesToSpanish(): void
    {
        $tables = [
            'roles' => 'roles_sistema',
            'users' => 'usuarios',
            'categories' => 'categorias',
            'products' => 'productos',
            'product_images' => 'imagenes_producto',
            'warehouses' => 'almacenes',
            'stocks' => 'existencias',
            'orders' => 'pedidos',
            'order_items' => 'detalles_pedido',
            'routes' => 'rutas',
            'deliveries' => 'entregas',
            'invoices' => 'facturas',
            'conversations' => 'conversaciones',
            'messages' => 'mensajes',
        ];

        foreach ($tables as $english => $spanish) {
            if ($this->db->tableExists($english) && ! $this->db->tableExists($spanish)) {
                $this->db->query("RENAME TABLE {$english} TO {$spanish}");
            }
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function renameTablesToEnglish(): void
    {
        $tables = [
            'mensajes' => 'messages',
            'conversaciones' => 'conversations',
            'facturas' => 'invoices',
            'entregas' => 'deliveries',
            'rutas' => 'routes',
            'detalles_pedido' => 'order_items',
            'pedidos' => 'orders',
            'existencias' => 'stocks',
            'almacenes' => 'warehouses',
            'imagenes_producto' => 'product_images',
            'productos' => 'products',
            'categorias' => 'categories',
            'usuarios' => 'users',
            'roles_sistema' => 'roles',
        ];

        foreach ($tables as $spanish => $english) {
            if ($this->db->tableExists($spanish) && ! $this->db->tableExists($english)) {
                $this->db->query("RENAME TABLE {$spanish} TO {$english}");
            }
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function renameColumnsToSpanish(): void
    {
        $this->changeColumns('usuarios', [
            'role_id' => 'rol_id BIGINT UNSIGNED NOT NULL',
            'name' => 'nombre VARCHAR(100) NOT NULL',
            'email' => 'correo VARCHAR(150) NOT NULL',
            'password' => 'contrasena VARCHAR(255) NOT NULL',
            'avatar_path' => 'ruta_avatar VARCHAR(255) NULL',
            'phone' => 'telefono VARCHAR(20) NULL',
            'address' => 'direccion VARCHAR(255) NULL',
            'city' => 'ciudad VARCHAR(100) NULL',
            'postal_code' => 'codigo_postal VARCHAR(20) NULL',
            'status' => "estado ENUM('active','inactive') NOT NULL DEFAULT 'active'",
            'email_verified_at' => 'correo_verificado_en DATETIME NULL',
            'remember_token' => 'token_recordar VARCHAR(100) NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('roles_sistema', [
            'name' => 'nombre VARCHAR(50) NOT NULL',
            'description' => 'descripcion VARCHAR(255) NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('categorias', [
            'name' => 'nombre VARCHAR(100) NOT NULL',
            'description' => 'descripcion VARCHAR(255) NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('productos', [
            'category_id' => 'categoria_id BIGINT UNSIGNED NOT NULL',
            'name' => 'nombre VARCHAR(150) NOT NULL',
            'description' => 'descripcion TEXT NULL',
            'image_path' => 'ruta_imagen VARCHAR(255) NULL',
            'price' => 'precio DECIMAL(10,2) NOT NULL',
            'tax_rate' => 'iva DECIMAL(5,2) NOT NULL DEFAULT 21.00',
            'weight' => 'peso DECIMAL(8,2) NULL',
            'status' => "estado ENUM('active','inactive') NOT NULL DEFAULT 'active'",
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('imagenes_producto', [
            'product_id' => 'producto_id BIGINT UNSIGNED NOT NULL',
            'path' => 'ruta VARCHAR(255) NOT NULL',
            'sort_order' => 'orden INT NOT NULL DEFAULT 0',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('almacenes', [
            'name' => 'nombre VARCHAR(100) NOT NULL',
            'address' => 'direccion VARCHAR(255) NOT NULL',
            'city' => 'ciudad VARCHAR(100) NOT NULL',
            'postal_code' => 'codigo_postal VARCHAR(20) NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('existencias', [
            'product_id' => 'producto_id BIGINT UNSIGNED NOT NULL',
            'warehouse_id' => 'almacen_id BIGINT UNSIGNED NOT NULL',
            'quantity' => 'cantidad INT NOT NULL DEFAULT 0',
            'minimum_quantity' => 'cantidad_minima INT NOT NULL DEFAULT 0',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('pedidos', [
            'customer_id' => 'cliente_id BIGINT UNSIGNED NOT NULL',
            'order_date' => 'fecha_pedido DATETIME NOT NULL',
            'status' => "estado ENUM('pending','confirmed','preparing','on_route','delivered','cancelled') NOT NULL DEFAULT 'pending'",
            'delivery_address' => 'direccion_entrega VARCHAR(255) NOT NULL',
            'notes' => 'notas TEXT NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('detalles_pedido', [
            'order_id' => 'pedido_id BIGINT UNSIGNED NOT NULL',
            'product_id' => 'producto_id BIGINT UNSIGNED NOT NULL',
            'quantity' => 'cantidad INT NOT NULL',
            'unit_price' => 'precio_unitario DECIMAL(10,2) NOT NULL',
            'tax_rate' => 'iva DECIMAL(5,2) NOT NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('rutas', [
            'driver_id' => 'conductor_id BIGINT UNSIGNED NOT NULL',
            'route_code' => 'codigo_ruta VARCHAR(50) NOT NULL',
            'departure_date' => 'fecha_salida DATETIME NOT NULL',
            'estimated_arrival' => 'llegada_estimada DATETIME NULL',
            'status' => "estado ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned'",
            'origin' => 'origen VARCHAR(150) NOT NULL',
            'destination' => 'destino VARCHAR(150) NOT NULL',
            'notes' => 'notas TEXT NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('entregas', [
            'order_id' => 'pedido_id BIGINT UNSIGNED NOT NULL',
            'route_id' => 'ruta_id BIGINT UNSIGNED NOT NULL',
            'estimated_delivery_at' => 'entrega_estimada_en DATETIME NULL',
            'delivered_at' => 'entregado_en DATETIME NULL',
            'status' => "estado ENUM('pending','in_transit','delivered','failed') NOT NULL DEFAULT 'pending'",
            'recipient_name' => 'nombre_receptor VARCHAR(100) NULL',
            'proof_image' => 'imagen_prueba VARCHAR(255) NULL',
            'observations' => 'observaciones TEXT NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('facturas', [
            'order_id' => 'pedido_id BIGINT UNSIGNED NOT NULL',
            'invoice_number' => 'numero_factura VARCHAR(50) NOT NULL',
            'issue_date' => 'fecha_emision DATE NOT NULL',
            'tax' => 'impuesto DECIMAL(10,2) NOT NULL',
            'status' => "estado ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending'",
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('conversaciones', [
            'subject' => 'asunto VARCHAR(150) NOT NULL',
            'order_id' => 'pedido_id BIGINT UNSIGNED NULL',
            'created_by' => 'creado_por BIGINT UNSIGNED NOT NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);

        $this->changeColumns('mensajes', [
            'conversation_id' => 'conversacion_id BIGINT UNSIGNED NOT NULL',
            'sender_id' => 'emisor_id BIGINT UNSIGNED NOT NULL',
            'receiver_id' => 'receptor_id BIGINT UNSIGNED NOT NULL',
            'message' => 'mensaje TEXT NOT NULL',
            'read_at' => 'leido_en DATETIME NULL',
            'created_at' => 'creado_en DATETIME NULL',
            'updated_at' => 'actualizado_en DATETIME NULL',
        ]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function renameColumnsToEnglish(): void
    {
        $this->changeColumns('usuarios', [
            'rol_id' => 'role_id BIGINT UNSIGNED NOT NULL',
            'nombre' => 'name VARCHAR(100) NOT NULL',
            'correo' => 'email VARCHAR(150) NOT NULL',
            'contrasena' => 'password VARCHAR(255) NOT NULL',
            'ruta_avatar' => 'avatar_path VARCHAR(255) NULL',
            'telefono' => 'phone VARCHAR(20) NULL',
            'direccion' => 'address VARCHAR(255) NULL',
            'ciudad' => 'city VARCHAR(100) NULL',
            'codigo_postal' => 'postal_code VARCHAR(20) NULL',
            'estado' => "status ENUM('active','inactive') NOT NULL DEFAULT 'active'",
            'correo_verificado_en' => 'email_verified_at DATETIME NULL',
            'token_recordar' => 'remember_token VARCHAR(100) NULL',
            'creado_en' => 'created_at DATETIME NULL',
            'actualizado_en' => 'updated_at DATETIME NULL',
        ]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function translateStoredValuesToSpanish(): void
    {
        $this->db->query("UPDATE roles_sistema SET nombre = 'administrador' WHERE nombre = 'admin'");

        foreach (['usuarios', 'productos'] as $table) {
            $this->db->query("ALTER TABLE {$table} MODIFY estado ENUM('active','inactive','activo','inactivo') NOT NULL DEFAULT 'active'");
            $this->db->query("UPDATE {$table} SET estado = 'activo' WHERE estado = 'active'");
            $this->db->query("UPDATE {$table} SET estado = 'inactivo' WHERE estado = 'inactive'");
            $this->db->query("ALTER TABLE {$table} MODIFY estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");
        }

        $this->translateStatusValues('pedidos', [
            'pending' => 'pendiente',
            'confirmed' => 'confirmado',
            'preparing' => 'preparando',
            'on_route' => 'en_ruta',
            'delivered' => 'entregado',
            'cancelled' => 'cancelado',
        ], "ENUM('pendiente','confirmado','preparando','en_ruta','entregado','cancelado') NOT NULL DEFAULT 'pendiente'");

        $this->translateStatusValues('rutas', [
            'planned' => 'planificada',
            'in_progress' => 'en_progreso',
            'completed' => 'completada',
            'cancelled' => 'cancelada',
        ], "ENUM('planificada','en_progreso','completada','cancelada') NOT NULL DEFAULT 'planificada'");

        $this->translateStatusValues('entregas', [
            'pending' => 'pendiente',
            'in_transit' => 'en_transito',
            'delivered' => 'entregada',
            'failed' => 'fallida',
        ], "ENUM('pendiente','en_transito','entregada','fallida') NOT NULL DEFAULT 'pendiente'");

        $this->translateStatusValues('facturas', [
            'pending' => 'pendiente',
            'paid' => 'pagada',
            'cancelled' => 'cancelada',
        ], "ENUM('pendiente','pagada','cancelada') NOT NULL DEFAULT 'pendiente'");
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function translateStoredValuesToEnglish(): void
    {
        $this->db->query("UPDATE roles_sistema SET nombre = 'admin' WHERE nombre = 'administrador'");
    }

    /**
     * Crea registros relacionados manteniendo juntas las operaciones necesarias.
     */
    private function createCompatibilityViews(): void
    {
        $views = [
            'roles' => 'SELECT id, nombre AS name, descripcion AS description, creado_en AS created_at, actualizado_en AS updated_at FROM roles_sistema',
            'users' => 'SELECT id, rol_id AS role_id, nombre AS name, correo AS email, contrasena AS password, ruta_avatar AS avatar_path, telefono AS phone, direccion AS address, ciudad AS city, codigo_postal AS postal_code, estado AS status, correo_verificado_en AS email_verified_at, token_recordar AS remember_token, creado_en AS created_at, actualizado_en AS updated_at FROM usuarios',
            'categories' => 'SELECT id, nombre AS name, descripcion AS description, creado_en AS created_at, actualizado_en AS updated_at FROM categorias',
            'products' => 'SELECT id, categoria_id AS category_id, nombre AS name, sku, descripcion AS description, ruta_imagen AS image_path, precio AS price, iva AS tax_rate, peso AS weight, estado AS status, creado_en AS created_at, actualizado_en AS updated_at FROM productos',
            'product_images' => 'SELECT id, producto_id AS product_id, ruta AS path, orden AS sort_order, creado_en AS created_at, actualizado_en AS updated_at FROM imagenes_producto',
            'warehouses' => 'SELECT id, nombre AS name, direccion AS address, ciudad AS city, codigo_postal AS postal_code, creado_en AS created_at, actualizado_en AS updated_at FROM almacenes',
            'stocks' => 'SELECT id, producto_id AS product_id, almacen_id AS warehouse_id, cantidad AS quantity, cantidad_minima AS minimum_quantity, creado_en AS created_at, actualizado_en AS updated_at FROM existencias',
            'orders' => 'SELECT id, cliente_id AS customer_id, fecha_pedido AS order_date, estado AS status, total, direccion_entrega AS delivery_address, notas AS notes, creado_en AS created_at, actualizado_en AS updated_at FROM pedidos',
            'order_items' => 'SELECT id, pedido_id AS order_id, producto_id AS product_id, cantidad AS quantity, precio_unitario AS unit_price, iva AS tax_rate, subtotal, creado_en AS created_at, actualizado_en AS updated_at FROM detalles_pedido',
            'routes' => 'SELECT id, conductor_id AS driver_id, codigo_ruta AS route_code, fecha_salida AS departure_date, llegada_estimada AS estimated_arrival, estado AS status, origen AS origin, destino AS destination, notas AS notes, creado_en AS created_at, actualizado_en AS updated_at FROM rutas',
            'deliveries' => 'SELECT id, pedido_id AS order_id, ruta_id AS route_id, entrega_estimada_en AS estimated_delivery_at, entregado_en AS delivered_at, estado AS status, nombre_receptor AS recipient_name, imagen_prueba AS proof_image, observaciones AS observations, creado_en AS created_at, actualizado_en AS updated_at FROM entregas',
            'invoices' => 'SELECT id, pedido_id AS order_id, numero_factura AS invoice_number, fecha_emision AS issue_date, subtotal, impuesto AS tax, total, estado AS status, creado_en AS created_at, actualizado_en AS updated_at FROM facturas',
            'conversations' => 'SELECT id, asunto AS subject, pedido_id AS order_id, creado_por AS created_by, creado_en AS created_at, actualizado_en AS updated_at FROM conversaciones',
            'messages' => 'SELECT id, conversacion_id AS conversation_id, emisor_id AS sender_id, receptor_id AS receiver_id, mensaje AS message, leido_en AS read_at, creado_en AS created_at, actualizado_en AS updated_at FROM mensajes',
        ];

        foreach ($views as $name => $select) {
            $this->db->query("CREATE VIEW {$name} AS {$select}");
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function changeColumns(string $table, array $columns): void
    {
        foreach ($columns as $from => $definition) {
            if ($this->db->fieldExists($from, $table)) {
                $this->db->query("ALTER TABLE {$table} CHANGE {$from} {$definition}");
            }
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function translateStatusValues(string $table, array $map, string $enumDefinition): void
    {
        $allValues = array_merge(array_keys($map), array_values($map));
        $enumValues = implode(',', array_map(static fn (string $value): string => "'{$value}'", $allValues));
        $this->db->query("ALTER TABLE {$table} MODIFY estado ENUM({$enumValues}) NOT NULL");

        foreach ($map as $from => $to) {
            $this->db->query("UPDATE {$table} SET estado = '{$to}' WHERE estado = '{$from}'");
        }

        $this->db->query("ALTER TABLE {$table} MODIFY estado {$enumDefinition}");
    }
}
