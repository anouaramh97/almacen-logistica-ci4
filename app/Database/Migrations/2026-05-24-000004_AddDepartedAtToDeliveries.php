<?php

// Migracion: crea o modifica la estructura de la base de datos.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Define cambios estructurales de base de datos que se pueden aplicar o revertir.
 */
class AddDepartedAtToDeliveries extends Migration
{
    /**
     * Aplica los cambios de esta migracion en la base de datos.
     */
    public function up()
    {
        if ($this->db->tableExists('entregas')) {
            if (! $this->db->fieldExists('salida_en', 'entregas')) {
                $this->db->query('ALTER TABLE entregas ADD salida_en DATETIME NULL AFTER entrega_estimada_en');
            }

            $this->refreshSpanishCompatibilityView();
            return;
        }

        if ($this->db->tableExists('deliveries') && ! $this->db->fieldExists('departed_at', 'deliveries')) {
            $this->db->query('ALTER TABLE deliveries ADD departed_at DATETIME NULL AFTER estimated_delivery_at');
        }
    }

    /**
     * Revierte los cambios aplicados por esta migracion.
     */
    public function down()
    {
        if ($this->db->tableExists('entregas') && $this->db->fieldExists('salida_en', 'entregas')) {
            $this->db->query('DROP VIEW IF EXISTS deliveries');
            $this->db->query('ALTER TABLE entregas DROP COLUMN salida_en');
            $this->refreshSpanishCompatibilityViewWithoutDepartedAt();
            return;
        }

        if ($this->db->tableExists('deliveries') && $this->db->fieldExists('departed_at', 'deliveries')) {
            $this->db->query('ALTER TABLE deliveries DROP COLUMN departed_at');
        }
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function refreshSpanishCompatibilityView(): void
    {
        $this->db->query('DROP VIEW IF EXISTS deliveries');
        $this->db->query('CREATE VIEW deliveries AS SELECT id, pedido_id AS order_id, ruta_id AS route_id, entrega_estimada_en AS estimated_delivery_at, salida_en AS departed_at, entregado_en AS delivered_at, estado AS status, nombre_receptor AS recipient_name, imagen_prueba AS proof_image, observaciones AS observations, creado_en AS created_at, actualizado_en AS updated_at FROM entregas');
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function refreshSpanishCompatibilityViewWithoutDepartedAt(): void
    {
        $this->db->query('DROP VIEW IF EXISTS deliveries');
        $this->db->query('CREATE VIEW deliveries AS SELECT id, pedido_id AS order_id, ruta_id AS route_id, entrega_estimada_en AS estimated_delivery_at, entregado_en AS delivered_at, estado AS status, nombre_receptor AS recipient_name, imagen_prueba AS proof_image, observaciones AS observations, creado_en AS created_at, actualizado_en AS updated_at FROM entregas');
    }
}
