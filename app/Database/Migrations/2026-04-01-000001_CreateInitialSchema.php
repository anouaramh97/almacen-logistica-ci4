<?php

// Migracion: crea o modifica la estructura de la base de datos.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Define cambios estructurales de base de datos que se pueden aplicar o revertir.
 */
class CreateInitialSchema extends Migration
{
    /**
     * Aplica los cambios de esta migracion en la base de datos.
     */
    public function up()
    {
        $tablesSql = [
            "CREATE TABLE roles (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL UNIQUE, description VARCHAR(255) NULL, created_at DATETIME NULL, updated_at DATETIME NULL)",
            "CREATE TABLE users (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, role_id BIGINT UNSIGNED NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, avatar_path VARCHAR(255) NULL, phone VARCHAR(20) NULL, address VARCHAR(255) NULL, city VARCHAR(100) NULL, postal_code VARCHAR(20) NULL, status ENUM('active','inactive') NOT NULL DEFAULT 'active', email_verified_at DATETIME NULL, remember_token VARCHAR(100) NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id))",
            "CREATE TABLE categories (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE, description VARCHAR(255) NULL, created_at DATETIME NULL, updated_at DATETIME NULL)",
            "CREATE TABLE products (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, category_id BIGINT UNSIGNED NOT NULL, name VARCHAR(150) NOT NULL, sku VARCHAR(100) NOT NULL UNIQUE, description TEXT NULL, image_path VARCHAR(255) NULL, price DECIMAL(10,2) NOT NULL, tax_rate DECIMAL(5,2) NOT NULL DEFAULT 21.00, weight DECIMAL(8,2) NULL, status ENUM('active','inactive') NOT NULL DEFAULT 'active', created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id))",
            "CREATE TABLE product_images (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id BIGINT UNSIGNED NOT NULL, path VARCHAR(255) NOT NULL, sort_order INT NOT NULL DEFAULT 0, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE)",
            "CREATE TABLE warehouses (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, postal_code VARCHAR(20) NULL, created_at DATETIME NULL, updated_at DATETIME NULL)",
            "CREATE TABLE stocks (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, product_id BIGINT UNSIGNED NOT NULL, warehouse_id BIGINT UNSIGNED NOT NULL, quantity INT NOT NULL DEFAULT 0, minimum_quantity INT NOT NULL DEFAULT 0, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT uq_stock_product_warehouse UNIQUE (product_id, warehouse_id), CONSTRAINT fk_stocks_product FOREIGN KEY (product_id) REFERENCES products(id), CONSTRAINT fk_stocks_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id))",
            "CREATE TABLE orders (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, customer_id BIGINT UNSIGNED NOT NULL, order_date DATETIME NOT NULL, status ENUM('pending','confirmed','preparing','on_route','delivered','cancelled') NOT NULL DEFAULT 'pending', total DECIMAL(10,2) NOT NULL DEFAULT 0.00, delivery_address VARCHAR(255) NOT NULL, notes TEXT NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES users(id))",
            "CREATE TABLE order_items (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL, product_id BIGINT UNSIGNED NOT NULL, quantity INT NOT NULL, unit_price DECIMAL(10,2) NOT NULL, tax_rate DECIMAL(5,2) NOT NULL, subtotal DECIMAL(10,2) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE, CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id))",
            "CREATE TABLE routes (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, driver_id BIGINT UNSIGNED NOT NULL, route_code VARCHAR(50) NOT NULL UNIQUE, departure_date DATETIME NOT NULL, estimated_arrival DATETIME NULL, status ENUM('planned','in_progress','completed','cancelled') NOT NULL DEFAULT 'planned', origin VARCHAR(150) NOT NULL, destination VARCHAR(150) NOT NULL, notes TEXT NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_routes_driver FOREIGN KEY (driver_id) REFERENCES users(id))",
            "CREATE TABLE deliveries (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL UNIQUE, route_id BIGINT UNSIGNED NOT NULL, estimated_delivery_at DATETIME NULL, departed_at DATETIME NULL, delivered_at DATETIME NULL, status ENUM('pending','in_transit','delivered','failed') NOT NULL DEFAULT 'pending', recipient_name VARCHAR(100) NULL, proof_image VARCHAR(255) NULL, observations TEXT NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_deliveries_order FOREIGN KEY (order_id) REFERENCES orders(id), CONSTRAINT fk_deliveries_route FOREIGN KEY (route_id) REFERENCES routes(id))",
            "CREATE TABLE invoices (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL UNIQUE, invoice_number VARCHAR(50) NOT NULL UNIQUE, issue_date DATE NOT NULL, subtotal DECIMAL(10,2) NOT NULL, tax DECIMAL(10,2) NOT NULL, total DECIMAL(10,2) NOT NULL, status ENUM('pending','paid','cancelled') NOT NULL DEFAULT 'pending', created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES orders(id))",
            "CREATE TABLE conversations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, subject VARCHAR(150) NOT NULL, order_id BIGINT UNSIGNED NULL, created_by BIGINT UNSIGNED NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_conversations_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL, CONSTRAINT fk_conversations_user FOREIGN KEY (created_by) REFERENCES users(id))",
            "CREATE TABLE messages (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, conversation_id BIGINT UNSIGNED NOT NULL, sender_id BIGINT UNSIGNED NOT NULL, receiver_id BIGINT UNSIGNED NOT NULL, message TEXT NOT NULL, read_at DATETIME NULL, created_at DATETIME NULL, updated_at DATETIME NULL, CONSTRAINT fk_messages_conversation FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE, CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id), CONSTRAINT fk_messages_receiver FOREIGN KEY (receiver_id) REFERENCES users(id))",
        ];

        foreach ($tablesSql as $sql) {
            $this->db->query($sql);
        }
    }

    /**
     * Revierte los cambios aplicados por esta migracion.
     */
    public function down()
    {
        foreach (['messages', 'conversations', 'invoices', 'deliveries', 'routes', 'order_items', 'orders', 'stocks', 'warehouses', 'product_images', 'products', 'categories', 'users', 'roles'] as $table) {
            $this->db->query("DROP TABLE IF EXISTS {$table}");
        }
    }
}
