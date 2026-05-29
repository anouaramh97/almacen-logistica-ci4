<?php

// Configuracion: define ajustes usados por la aplicacion.

use CodeIgniter\Router\RouteCollection;


/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->post('language/update', 'LocaleController::update');

$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login', 'Auth\AuthController::login');
    $routes->post('login', 'Auth\AuthController::attemptLogin');
    $routes->get('register', 'Auth\AuthController::register');
    $routes->post('register', 'Auth\AuthController::attemptRegister');
    $routes->get('forgot-password', 'Auth\AuthController::forgotPassword');
    $routes->post('forgot-password', 'Auth\AuthController::sendResetLink');
    $routes->get('reset-password/(:segment)', 'Auth\AuthController::resetPassword/$1');
    $routes->post('reset-password', 'Auth\AuthController::updatePassword');
});

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('profile', 'ProfileController::edit');
    $routes->post('profile', 'ProfileController::update');
    $routes->post('profile/delete', 'ProfileController::delete');
    $routes->get('messages', 'MessageCenterController::index');
    $routes->get('messages/new', 'MessageCenterController::create');
    $routes->get('messages/(:num)', 'MessageCenterController::show/$1');
    $routes->post('messages', 'MessageCenterController::store');
    $routes->post('messages/reply/(:num)', 'MessageCenterController::reply/$1');
    $routes->post('logout', 'Auth\AuthController::logout');

    $routes->group('admin', ['filter' => 'role:administrador'], static function ($routes) {
        $routes->get('dashboard', 'Admin\DashboardController::index');
        $routes->get('categories', 'Admin\CategoryController::index');
        $routes->get('categories/edit/(:num)', 'Admin\CategoryController::edit/$1');
        $routes->post('categories', 'Admin\CategoryController::store');
        $routes->post('categories/update/(:num)', 'Admin\CategoryController::update/$1');
        $routes->post('categories/delete/(:num)', 'Admin\CategoryController::delete/$1');
        $routes->get('warehouses', 'Admin\WarehouseController::index');
        $routes->get('warehouses/edit/(:num)', 'Admin\WarehouseController::edit/$1');
        $routes->post('warehouses', 'Admin\WarehouseController::store');
        $routes->post('warehouses/update/(:num)', 'Admin\WarehouseController::update/$1');
        $routes->post('warehouses/delete/(:num)', 'Admin\WarehouseController::delete/$1');
        $routes->get('products', 'Admin\ProductController::index');
        $routes->get('products/create', 'Admin\ProductController::create');
        $routes->post('products', 'Admin\ProductController::store');
        $routes->get('products/(:num)', 'Admin\ProductController::show/$1');
        $routes->get('products/edit/(:num)', 'Admin\ProductController::edit/$1');
        $routes->post('products/update/(:num)', 'Admin\ProductController::update/$1');
        $routes->post('products/(:num)/gallery/delete/(:num)', 'Admin\ProductController::deleteGalleryImage/$1/$2');
        $routes->post('products/delete/(:num)', 'Admin\ProductController::delete/$1');
        $routes->get('stocks', 'Admin\StockController::index');
        $routes->get('stocks/edit/(:num)', 'Admin\StockController::edit/$1');
        $routes->post('stocks/update/(:num)', 'Admin\StockController::update/$1');
        $routes->get('users', 'Admin\UserController::index');
        $routes->get('users/create', 'Admin\UserController::create');
        $routes->post('users', 'Admin\UserController::store');
        $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
        $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
        $routes->post('users/activate/(:num)', 'Admin\UserController::activate/$1');
        $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
        $routes->get('orders', 'Admin\OrderController::index');
        $routes->get('orders/(:num)', 'Admin\OrderController::show/$1');
        $routes->get('orders/edit/(:num)', 'Admin\OrderController::edit/$1');
        $routes->post('orders/update/(:num)', 'Admin\OrderController::update/$1');
        $routes->post('orders/delete/(:num)', 'Admin\OrderController::delete/$1');
        $routes->post('orders/(:num)/invoice', 'Admin\InvoiceController::store/$1');
        $routes->get('routes', 'Admin\RouteController::index');
        $routes->get('routes/create', 'Admin\RouteController::create');
        $routes->post('routes', 'Admin\RouteController::store');
        $routes->get('routes/(:num)', 'Admin\RouteController::show/$1');
        $routes->post('routes/status/(:num)', 'Admin\RouteController::updateStatus/$1');
        $routes->post('routes/delete/(:num)', 'Admin\RouteController::delete/$1');
        $routes->get('invoices', 'Admin\InvoiceController::index');
        $routes->get('invoices/(:num)', 'Admin\InvoiceController::show/$1');
        $routes->get('invoices/(:num)/pdf', 'Admin\InvoiceController::pdf/$1');
    });

    $routes->group('client', ['filter' => 'role:cliente'], static function ($routes) {
        $routes->get('dashboard', 'Client\OrderController::index');
        $routes->get('products/(:num)', 'Client\ProductController::show/$1');
        $routes->get('orders', 'Client\OrderController::index');
        $routes->get('orders/create', 'Client\OrderController::create');
        $routes->post('orders', 'Client\OrderController::store');
        $routes->get('orders/edit/(:num)', 'Client\OrderController::edit/$1');
        $routes->post('orders/update/(:num)', 'Client\OrderController::update/$1');
        $routes->get('orders/(:num)', 'Client\OrderController::show/$1');
        $routes->get('deliveries', 'Client\DeliveryController::index');
        $routes->get('deliveries/(:num)', 'Client\DeliveryController::show/$1');
        $routes->get('invoices/(:num)', 'Client\InvoiceController::show/$1');
        $routes->get('invoices/(:num)/pdf', 'Client\InvoiceController::pdf/$1');
    });

    $routes->group('driver', ['filter' => 'role:conductor'], static function ($routes) {
        $routes->get('dashboard', 'Driver\DashboardController::index');
        $routes->get('routes', 'Driver\RouteController::index');
        $routes->get('routes/(:num)', 'Driver\RouteController::show/$1');
        $routes->get('deliveries/(:num)', 'Driver\DeliveryController::show/$1');
        $routes->post('deliveries/(:num)', 'Driver\DeliveryController::update/$1');
    });

    $routes->group('logistics', ['filter' => 'role:logistica'], static function ($routes) {
        $routes->get('dashboard', 'Logistics\DashboardController::index');
        $routes->get('routes', 'Logistics\RouteController::index');
        $routes->get('routes/create', 'Logistics\RouteController::create');
        $routes->post('routes', 'Logistics\RouteController::store');
        $routes->get('routes/(:num)', 'Logistics\RouteController::show/$1');
        $routes->get('orders', 'Logistics\OrderController::index');
        $routes->get('orders/(:num)', 'Logistics\OrderController::show/$1');
    });
});
