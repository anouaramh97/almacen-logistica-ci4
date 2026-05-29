<?php

// Migracion: crea o modifica la estructura de la base de datos.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Define cambios estructurales de base de datos que se pueden aplicar o revertir.
 */
class RepairSpanishStatusValues extends Migration
{
    /**
     * Aplica los cambios de esta migracion en la base de datos.
     */
    public function up()
    {
        if ($this->db->tableExists('usuarios')) {
            $this->db->query("ALTER TABLE usuarios MODIFY estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");
            $this->db->query("UPDATE usuarios SET estado = 'activo' WHERE estado = '' OR estado IS NULL");
        }

        if ($this->db->tableExists('productos')) {
            $this->db->query("ALTER TABLE productos MODIFY estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");
            $this->db->query("UPDATE productos SET estado = 'activo' WHERE estado = '' OR estado IS NULL");
        }

        if ($this->db->tableExists('pedidos')) {
            $this->db->query("ALTER TABLE pedidos MODIFY estado ENUM('pendiente','confirmado','preparando','en_ruta','entregado','cancelado') NOT NULL DEFAULT 'pendiente'");
            $this->db->query("UPDATE pedidos SET estado = 'pendiente' WHERE estado = '' OR estado IS NULL");
        }

        if ($this->db->tableExists('rutas')) {
            $this->db->query("ALTER TABLE rutas MODIFY estado ENUM('planificada','en_progreso','completada','cancelada') NOT NULL DEFAULT 'planificada'");
            $this->db->query("UPDATE rutas SET estado = 'planificada' WHERE estado = '' OR estado IS NULL");
        }

        if ($this->db->tableExists('entregas')) {
            $this->db->query("ALTER TABLE entregas MODIFY estado ENUM('pendiente','en_transito','entregada','fallida') NOT NULL DEFAULT 'pendiente'");
            $this->db->query("UPDATE entregas SET estado = 'pendiente' WHERE estado = '' OR estado IS NULL");
        }

        if ($this->db->tableExists('facturas')) {
            $this->db->query("ALTER TABLE facturas MODIFY estado ENUM('pendiente','pagada','cancelada') NOT NULL DEFAULT 'pendiente'");
            $this->db->query("UPDATE facturas SET estado = 'pendiente' WHERE estado = '' OR estado IS NULL");
        }
    }

    /**
     * Revierte los cambios aplicados por esta migracion.
     */
    public function down()
    {
        
    }
}
