<?php

// Helper: agrupa funciones reutilizables de apoyo para la aplicacion.

use App\Models\UserModel;

/**
 * Helper current user: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        $user = session('auth_user');
        return is_array($user) ? $user : null;
    }
}

/**
 * Helper is logged in: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return current_user() !== null;
    }
}

/**
 * Helper refresh auth user: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('refresh_auth_user')) {
    function refresh_auth_user(): ?array
    {
        $user = current_user();
        if (! $user) {
            return null;
        }

        $model = new UserModel();
        $fresh = $model->select('users.*, roles.name as role_name, roles.description as role_description')
            ->join('roles', 'roles.id = users.role_id')
            ->find($user['id']);

        if (! $fresh) {
            session()->remove('auth_user');
            return null;
        }

        session()->set('auth_user', $fresh);
        return $fresh;
    }
}

/**
 * Helper has role: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('has_role')) {
    function has_role(string $role): bool
    {
        return (current_user()['role_name'] ?? null) === $role;
    }
}

/**
 * Helper avatar url: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('avatar_url')) {
    function avatar_url(?string $path, string $name = 'Usuario'): string
    {
        $resolvedPath = resolve_project_media_path($path);

        if ($resolvedPath !== null) {
            return preg_match('#^(https?:)?//#i', $resolvedPath) || str_starts_with($resolvedPath, 'data:image/')
                ? $resolvedPath
                : base_url($resolvedPath);
        }

        $initials = '';
        foreach (preg_split('/\s+/', trim($name)) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160"><rect width="100%" height="100%" rx="32" fill="#0f62fe"/><text x="50%" y="56%" dominant-baseline="middle" text-anchor="middle" font-family="Segoe UI, Arial" font-size="58" font-weight="700" fill="#ffffff">' . htmlspecialchars(substr($initials ?: 'LP', 0, 2), ENT_QUOTES) . '</text></svg>');
    }
}

/**
 * Helper product image url: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('product_image_url')) {
    function product_image_url(?string $path, string $name = 'Producto'): string
    {
        $resolvedPath = resolve_project_media_path($path);

        if ($resolvedPath !== null) {
            return preg_match('#^(https?:)?//#i', $resolvedPath) || str_starts_with($resolvedPath, 'data:image/')
                ? $resolvedPath
                : base_url($resolvedPath);
        }

        return 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="320" height="240"><rect width="100%" height="100%" rx="28" fill="#e8f1ff"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Segoe UI, Arial" font-size="26" font-weight="700" fill="#0f62fe">' . htmlspecialchars($name, ENT_QUOTES) . '</text></svg>');
    }
}

/**
 * Helper resolve project media path: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('resolve_project_media_path')) {
    function resolve_project_media_path(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);

        if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:image/')) {
            return $path;
        }

        $relativePath = ltrim(str_replace('\\', '/', $path), '/');

        if (is_file(FCPATH . $relativePath)) {
            return $relativePath;
        }

        $publicMarker = '/public/';
        $normalized = str_replace('\\', '/', $path);
        $publicPosition = strpos($normalized, $publicMarker);

        if ($publicPosition !== false) {
            $candidate = ltrim(substr($normalized, $publicPosition + strlen($publicMarker)), '/');

            if ($candidate !== '' && is_file(FCPATH . $candidate)) {
                return $candidate;
            }
        }

        $basename = basename($relativePath);

        if ($basename === '' || $basename === '.' || $basename === '..') {
            return null;
        }

        foreach ([
            'media/avatars/' . $basename,
            'media/products/' . $basename,
            'uploads/avatars/' . $basename,
            'uploads/products/' . $basename,
        ] as $candidate) {
            if (is_file(FCPATH . $candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}

/**
 * Helper project media relative dir: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('project_media_relative_dir')) {
    function project_media_relative_dir(string $type): string
    {
        return match ($type) {
            'avatar' => 'media/avatars',
            'product' => 'media/products',
            default => 'media/misc',
        };
    }
}

/**
 * Helper project media absolute dir: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('project_media_absolute_dir')) {
    function project_media_absolute_dir(string $type): string
    {
        return FCPATH . project_media_relative_dir($type);
    }
}

/**
 * Helper ensure project media directory: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('ensure_project_media_directory')) {
    function ensure_project_media_directory(string $type): string
    {
        $directory = project_media_absolute_dir($type);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return $directory;
    }
}

/**
 * Helper store project media: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('store_project_media')) {
    function store_project_media($file, string $type, ?string $oldPath = null): ?string
    {
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $oldPath;
        }

        $directory = ensure_project_media_directory($type);
        $filename = $file->getRandomName();
        $file->move($directory, $filename);

        $newPath = project_media_relative_dir($type) . '/' . $filename;

        if ($oldPath && $oldPath !== $newPath) {
            $oldAbsolutePath = FCPATH . ltrim($oldPath, '/');
            if (is_file($oldAbsolutePath)) {
                unlink($oldAbsolutePath);
            }
        }

        return $newPath;
    }
}

/**
 * Helper delete project media: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('delete_project_media')) {
    function delete_project_media(?string $path): void
    {
        $resolvedPath = resolve_project_media_path($path);

        if ($resolvedPath === null || str_starts_with($resolvedPath, 'http') || str_starts_with($resolvedPath, 'data:image/')) {
            return;
        }

        $absolutePath = FCPATH . ltrim($resolvedPath, '/');

        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}

/**
 * Helper status label: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('status_label')) {
    function status_label(?string $status): string
    {
        if ($status === null || $status === '') {
            return '';
        }

        $translated = lang('App.' . $status);

        return is_string($translated) && $translated !== 'App.' . $status
            ? $translated
            : ucfirst(str_replace('_', ' ', $status));
    }
}

/**
 * Helper format order datetime: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('format_order_datetime')) {
    function format_order_datetime(?string $dateTime): string
    {
        if ($dateTime === null || trim($dateTime) === '') {
            return '';
        }

        try {
            return (new DateTimeImmutable($dateTime))->format('d/m/Y H:i');
        } catch (Throwable) {
            return $dateTime;
        }
    }
}

/**
 * Helper role label: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('role_label')) {
    function role_label(?string $role): string
    {
        if ($role === null || $role === '') {
            return '';
        }

        $translated = lang('App.role_' . $role);

        return is_string($translated) && $translated !== 'App.role_' . $role
            ? $translated
            : ucfirst(str_replace('_', ' ', $role));
    }
}

/**
 * Helper order status class: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('order_status_class')) {
    function order_status_class(?string $status): string
    {
        return match ($status) {
            'pendiente' => 'is-warning',
            'confirmado' => 'is-success-soft',
            'preparando' => 'is-info',
            'en_ruta' => 'is-primary',
            'entregado' => 'is-success-dark',
            'cancelado' => 'is-danger',
            default => 'is-primary',
        };
    }
}

/**
 * Helper delivery status class: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('delivery_status_class')) {
    function delivery_status_class(?string $status): string
    {
        if ($status === 'pendiente') {
            return 'is-warning';
        }

        if ($status === 'en_transito') {
            return 'is-primary';
        }

        if ($status === 'entregada' || $status === 'entregado') {
            return 'is-success-dark';
        }

        if ($status === 'fallida') {
            return 'is-danger';
        }

        return 'is-primary';
    }
}

/**
 * Helper route status class: funcion auxiliar disponible en vistas y controladores.
 */
if (! function_exists('route_status_class')) {
    function route_status_class(?string $status): string
    {
        if ($status === 'planificada') {
            return 'is-warning';
        }

        if ($status === 'en_progreso') {
            return 'is-primary';
        }

        if ($status === 'completada') {
            return 'is-success-dark';
        }

        if ($status === 'cancelada' || $status === 'cancelado') {
            return 'is-danger';
        }

        return 'is-primary';
    }
}
