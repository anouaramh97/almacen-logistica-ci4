<?php

// Seeder: carga datos iniciales para probar o arrancar la aplicacion.

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Carga datos iniciales necesarios para arrancar el proyecto.
 */
class InitialSeeder extends Seeder
{
    /**
     * Ejecuta la carga de datos definida para esta semilla.
     */
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('roles')->insertBatch([
            ['name' => 'administrador', 'description' => 'Administrador del sistema', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'logistica', 'description' => 'Empresa de logística', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'conductor', 'description' => 'Conductor encargado de entregas', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cliente', 'description' => 'Cliente que realiza pedidos', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $roleMap = [];
        foreach ($this->db->table('roles')->get()->getResultArray() as $role) {
            $roleMap[$role['name']] = $role['id'];
        }

        $this->db->table('users')->insertBatch([
            ['role_id' => $roleMap['administrador'], 'name' => 'Administrador', 'email' => 'admin@almacen.com', 'password' => password_hash('Admin12345', PASSWORD_BCRYPT), 'phone' => '600000000', 'address' => 'Calle Principal 1', 'city' => 'Madrid', 'postal_code' => '28001', 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['role_id' => $roleMap['logistica'], 'name' => 'Empresa Logistica', 'email' => 'logistica@almacen.com', 'password' => password_hash('Logistica123', PASSWORD_BCRYPT), 'phone' => '611111111', 'address' => 'Avenida del Transporte 8', 'city' => 'Madrid', 'postal_code' => '28020', 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['role_id' => $roleMap['conductor'], 'name' => 'Conductor Demo', 'email' => 'conductor@almacen.com', 'password' => password_hash('Conductor123', PASSWORD_BCRYPT), 'phone' => '622222222', 'address' => 'Calle Ruta 12', 'city' => 'Madrid', 'postal_code' => '28030', 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['role_id' => $roleMap['cliente'], 'name' => 'Cliente Demo', 'email' => 'cliente@almacen.com', 'password' => password_hash('Cliente123', PASSWORD_BCRYPT), 'phone' => '633333333', 'address' => 'Calle Comercio 5', 'city' => 'Getafe', 'postal_code' => '28901', 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $userMap = [];
        foreach ($this->db->table('users')->get()->getResultArray() as $user) {
            $userMap[$user['email']] = $user['id'];
        }

        $this->db->table('categories')->insertBatch([
            ['name' => 'Electrónica', 'description' => 'Componentes y dispositivos', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Oficina', 'description' => 'Material para oficina', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Logística', 'description' => 'Equipamiento logístico', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $categoryMap = [];
        foreach ($this->db->table('categories')->get()->getResultArray() as $category) {
            $categoryMap[$category['name']] = $category['id'];
        }

        $this->db->table('warehouses')->insertBatch([
            ['name' => 'Central Madrid', 'address' => 'Calle Almacén 10', 'city' => 'Madrid', 'postal_code' => '28010', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Nave Sur', 'address' => 'Polígono Ruta 22', 'city' => 'Getafe', 'postal_code' => '28906', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $warehouseMap = [];
        foreach ($this->db->table('warehouses')->get()->getResultArray() as $warehouse) {
            $warehouseMap[$warehouse['name']] = $warehouse['id'];
        }

        $this->db->table('products')->insertBatch([
            ['category_id' => $categoryMap['Electrónica'], 'name' => 'Escáner industrial', 'sku' => 'ESC-100', 'description' => 'Escáner para almacén', 'price' => 299.00, 'tax_rate' => 21.00, 'weight' => 2.40, 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => $categoryMap['Oficina'], 'name' => 'Etiquetas térmicas', 'sku' => 'ETQ-200', 'description' => 'Pack de etiquetas', 'price' => 39.90, 'tax_rate' => 21.00, 'weight' => 0.50, 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
            ['category_id' => $categoryMap['Logística'], 'name' => 'Carro de picking', 'sku' => 'CAR-300', 'description' => 'Carro para preparación', 'price' => 189.50, 'tax_rate' => 21.00, 'weight' => 14.00, 'status' => 'activo', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $productMap = [];
        foreach ($this->db->table('products')->get()->getResultArray() as $product) {
            $productMap[$product['sku']] = $product['id'];
        }

        $this->db->table('stocks')->insertBatch([
            ['product_id' => $productMap['ESC-100'], 'warehouse_id' => $warehouseMap['Central Madrid'], 'quantity' => 6, 'minimum_quantity' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['product_id' => $productMap['ETQ-200'], 'warehouse_id' => $warehouseMap['Central Madrid'], 'quantity' => 45, 'minimum_quantity' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['product_id' => $productMap['CAR-300'], 'warehouse_id' => $warehouseMap['Nave Sur'], 'quantity' => 2, 'minimum_quantity' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->db->table('orders')->insertBatch([
            ['customer_id' => $userMap['cliente@almacen.com'], 'order_date' => $now, 'status' => 'pendiente', 'total' => 378.90, 'delivery_address' => 'Calle Comercio 5, Getafe', 'notes' => 'Entrega preferente', 'created_at' => $now, 'updated_at' => $now],
            ['customer_id' => $userMap['cliente@almacen.com'], 'order_date' => $now, 'status' => 'confirmado', 'total' => 189.50, 'delivery_address' => 'Calle Comercio 5, Getafe', 'notes' => 'Sin observaciones', 'created_at' => $now, 'updated_at' => $now],
        ]);
        $orders = $this->db->table('orders')->orderBy('id')->get()->getResultArray();
        $firstOrder = $orders[0]['id'] ?? null; $secondOrder = $orders[1]['id'] ?? null;

        if ($firstOrder) {
            $this->db->table('order_items')->insertBatch([
                ['order_id' => $firstOrder, 'product_id' => $productMap['ESC-100'], 'quantity' => 1, 'unit_price' => 299.00, 'tax_rate' => 21.00, 'subtotal' => 299.00, 'created_at' => $now, 'updated_at' => $now],
                ['order_id' => $firstOrder, 'product_id' => $productMap['ETQ-200'], 'quantity' => 2, 'unit_price' => 39.95, 'tax_rate' => 21.00, 'subtotal' => 79.90, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }
        if ($secondOrder) {
            $this->db->table('order_items')->insert(['order_id' => $secondOrder, 'product_id' => $productMap['CAR-300'], 'quantity' => 1, 'unit_price' => 189.50, 'tax_rate' => 21.00, 'subtotal' => 189.50, 'created_at' => $now, 'updated_at' => $now]);
        }

        $this->db->table('routes')->insert(['driver_id' => $userMap['conductor@almacen.com'], 'route_code' => 'RUTA-001', 'departure_date' => date('Y-m-d H:i:s', strtotime('+1 day')), 'estimated_arrival' => date('Y-m-d H:i:s', strtotime('+1 day +4 hours')), 'status' => 'planificada', 'origin' => 'Central Madrid', 'destination' => 'Getafe', 'notes' => 'Ruta demo inicial', 'created_at' => $now, 'updated_at' => $now]);
        $routeId = $this->db->insertID();

        if ($secondOrder) {
            $this->db->table('deliveries')->insert(['order_id' => $secondOrder, 'route_id' => $routeId, 'status' => 'pendiente', 'created_at' => $now, 'updated_at' => $now]);
            $this->db->table('invoices')->insert(['order_id' => $secondOrder, 'invoice_number' => 'FAC-000002', 'issue_date' => date('Y-m-d'), 'subtotal' => 156.61, 'tax' => 32.89, 'total' => 189.50, 'status' => 'pendiente', 'created_at' => $now, 'updated_at' => $now]);
        }

        $this->db->table('conversations')->insert(['subject' => 'Consulta sobre pedido', 'order_id' => $firstOrder, 'created_by' => $userMap['cliente@almacen.com'], 'created_at' => $now, 'updated_at' => $now]);
        $conversationId = $this->db->insertID();
        $this->db->table('messages')->insertBatch([
            ['conversation_id' => $conversationId, 'sender_id' => $userMap['cliente@almacen.com'], 'receiver_id' => $userMap['admin@almacen.com'], 'message' => 'Necesito confirmar la fecha estimada de entrega.', 'created_at' => $now, 'updated_at' => $now],
            ['conversation_id' => $conversationId, 'sender_id' => $userMap['admin@almacen.com'], 'receiver_id' => $userMap['cliente@almacen.com'], 'message' => 'Lo revisamos y te avisamos en cuanto se asigne la ruta.', 'created_at' => date('Y-m-d H:i:s', strtotime('+10 minutes')), 'updated_at' => date('Y-m-d H:i:s', strtotime('+10 minutes'))],
        ]);
    }
}