<!-- Vista: muestra la pantalla con los datos recibidos del controlador. -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura <?= esc($invoice['invoice_number']) ?></title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 28px;
        }
        .header {
            border-bottom: 2px solid #0f62fe;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 26px;
        }
        .muted {
            color: #6b7280;
        }
        .info-block {
            width: 100%;
            margin-bottom: 18px;
            border: 1px solid #d1d5db;
            border-collapse: collapse;
        }
        .info-block td {
            padding: 10px 12px;
            vertical-align: top;
        }
        .info-block .label {
            width: 140px;
            font-weight: bold;
            background: #f8fafc;
        }
        table.meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 22px;
        }
        table.items th,
        table.items td {
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
        }
        table.items th {
            background: #eff6ff;
        }
        table.totals {
            width: 320px;
            margin-left: auto;
            border-collapse: collapse;
        }
        table.totals td {
            border: 1px solid #d1d5db;
            padding: 8px 10px;
        }
        table.totals tr:last-child td {
            background: #0f62fe;
            color: #fff;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factura <?= esc($invoice['invoice_number']) ?></h1>
        <div class="muted">Documento generado desde Logistica Pro.</div>
    </div>

    <table class="info-block">
        <tr>
            <td class="label">Cliente</td>
            <td><?= esc($invoice['customer_name']) ?></td>
        </tr>
        <tr>
            <td class="label">Correo</td>
            <td><?= esc($invoice['customer_email']) ?></td>
        </tr>
        <tr>
            <td class="label">Direccion</td>
            <td><?= esc($invoice['delivery_address']) ?></td>
        </tr>
    </table>

    <table class="info-block">
        <tr>
            <td class="label">Numero</td>
            <td><?= esc($invoice['invoice_number']) ?></td>
        </tr>
        <tr>
            <td class="label">Fecha</td>
            <td><?= esc($invoice['issue_date']) ?></td>
        </tr>
        <tr>
            <td class="label">Pedido</td>
            <td>#<?= esc($invoice['order_id']) ?></td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio unitario</th>
                <th>IVA</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= esc($item['product_name']) ?></td>
                    <td><?= esc($item['quantity']) ?></td>
                    <td><?= number_format((float) $item['unit_price'], 2) ?> EUR</td>
                    <td><?= number_format((float) $item['tax_rate'], 2) ?>%</td>
                    <td><?= number_format((float) $item['subtotal'], 2) ?> EUR</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal</td>
            <td><?= number_format((float) $invoice['subtotal'], 2) ?> EUR</td>
        </tr>
        <tr>
            <td>IVA</td>
            <td><?= number_format((float) $invoice['tax'], 2) ?> EUR</td>
        </tr>
        <tr>
            <td>Total</td>
            <td><?= number_format((float) $invoice['total'], 2) ?> EUR</td>
        </tr>
    </table>

    <div class="footer">
        Documento generado automaticamente desde Logistica Pro.
    </div>
</body>
</html>
