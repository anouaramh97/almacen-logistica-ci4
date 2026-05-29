<!-- Plantilla de correo: mensaje enviado al usuario desde el sistema. -->
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recuperar contrasena</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,sans-serif;color:#142033;">
    <div style="max-width:560px;margin:0 auto;padding:32px 18px;">
        <div style="background:#ffffff;border:1px solid #e4eaf2;border-radius:18px;padding:28px;">
            <h1 style="margin:0 0 12px;font-size:24px;color:#142033;">Recuperar contrasena</h1>
            <p style="margin:0 0 18px;line-height:1.6;color:#516173;">Hola <?= esc($name ?: 'usuario') ?>, hemos recibido una solicitud para cambiar la contrasena de tu cuenta.</p>
            <p style="margin:0 0 24px;line-height:1.6;color:#516173;">Pulsa el boton para crear una nueva contrasena. El enlace caduca en 60 minutos.</p>
            <p style="margin:0 0 24px;"><a href="<?= esc($resetUrl) ?>" style="display:inline-block;background:#0f62fe;color:#ffffff;text-decoration:none;border-radius:999px;padding:13px 20px;font-weight:bold;">Cambiar contrasena</a></p>
            <p style="margin:0;line-height:1.6;color:#6e7c90;font-size:13px;">Si no has pedido este cambio, puedes ignorar este correo.</p>
        </div>
    </div>
</body>
</html>
