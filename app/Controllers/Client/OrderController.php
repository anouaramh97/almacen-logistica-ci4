<?php

// Controlador: gestiona peticiones, valida datos y prepara la respuesta.

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\StockModel;

/**
 * Coordina las pantallas y acciones del modulo de cliente.
 */
class OrderController extends BaseController
{
    /**
     * Lista los registros principales y prepara los datos para la vista.
     */
    public function index(): string
    {
        $userId = current_user()['id'];
        $orders = model(OrderModel::class)->listForCustomerWithInvoice($userId);

        return $this->render('client/orders/index', ['orders' => $orders]);
    }

    /**
     * Prepara el formulario de alta con los datos auxiliares necesarios.
     */
    public function create(): string
    {
        return $this->renderOrderForm();
    }

    /**
     * Valida la entrada y guarda un nuevo registro.
     */
    public function store()
    {
        if (! $this->validate($this->deliveryAddressRules())) {
            return redirect()->back()->withInput()->with('error', 'Indica la direccion, ciudad o poblacion y codigo postal.');
        }

        $orderModel = model(OrderModel::class);
        [$items, $total, $error] = $this->buildOrderItems();

        if ($error !== null) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        if ($items === []) {
            return redirect()->back()->withInput()->with('error', 'Añade al menos un producto con cantidad válida.');
        }

        $now = date('Y-m-d H:i:s');
        $orderData = [
            'customer_id' => current_user()['id'],
            'order_date' => $now,
            'status' => 'pendiente',
            'total' => $total,
            'delivery_address' => $this->composeDeliveryAddress(),
            'notes' => $this->request->getPost('notes'),
            'created_at' => $now,
            'updated_at' => $now,
        ];

        foreach ($items as $item) {
            $item['created_at'] = $now;
            $item['updated_at'] = $now;
        }

        $orderModel->createCustomerOrder($orderData, $items);

        return redirect()->to(site_url('client/orders'))->with('success', 'Pedido creado correctamente.');
    }

    /**
     * Carga un registro existente para mostrarlo en el formulario de edicion.
     */
    public function edit($id): string
    {
        $order = model(OrderModel::class)->findForCustomer((int) $id, (int) current_user()['id']);

        if (! $order) {
            return redirect()->to(site_url('client/orders'))->with('error', 'Pedido no encontrado.');
        }

        if (($order['status'] ?? null) !== 'pendiente') {
            return redirect()->to(site_url('client/orders/' . $id))->with('error', 'Solo puedes modificar pedidos pendientes de confirmacion.');
        }

        $items = model(OrderItemModel::class)->itemsForOrder((int) $id);
        $quantitiesByProduct = [];
        foreach ($items as $item) {
            $quantitiesByProduct[(int) $item['product_id']] = (int) $item['quantity'];
        }

        return $this->renderOrderForm([
            'mode' => 'edit',
            'order' => $order,
            'quantitiesByProduct' => $quantitiesByProduct,
        ]);
    }

    /**
     * Valida la entrada y actualiza un registro existente.
     */
    public function update($id)
    {
        $orderModel = model(OrderModel::class);
        $order = $orderModel->findForCustomer((int) $id, (int) current_user()['id']);

        if (! $order) {
            return redirect()->to(site_url('client/orders'))->with('error', 'Pedido no encontrado.');
        }

        if (($order['status'] ?? null) !== 'pendiente') {
            return redirect()->to(site_url('client/orders/' . $id))->with('error', 'Solo puedes modificar pedidos pendientes de confirmacion.');
        }

        if (! $this->validate($this->deliveryAddressRules())) {
            return redirect()->back()->withInput()->with('error', 'Indica la direccion, ciudad o poblacion y codigo postal.');
        }

        [$items, $total, $error] = $this->buildOrderItems();

        if ($error !== null) {
            return redirect()->back()->withInput()->with('error', $error);
        }

        if ($items === []) {
            return redirect()->back()->withInput()->with('error', 'Añade al menos un producto con cantidad válida.');
        }

        $now = date('Y-m-d H:i:s');
        foreach ($items as $index => $item) {
            $items[$index]['created_at'] = $now;
            $items[$index]['updated_at'] = $now;
        }

        $result = $orderModel->updateCustomerPendingOrder((int) $id, (int) current_user()['id'], [
            'total' => $total,
            'delivery_address' => $this->composeDeliveryAddress(),
            'notes' => $this->request->getPost('notes'),
            'updated_at' => $now,
        ], $items);

        if (! $result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to(site_url('client/orders/' . $id))->with('success', $result['message']);
    }

    /**
     * Muestra el detalle de un registro concreto.
     */
    public function show($id): string
    {
        $order = model(OrderModel::class)->findForCustomer((int) $id, (int) current_user()['id']);
        $items = model(OrderItemModel::class)->itemsForOrder((int) $id, true);
        $invoice = model(InvoiceModel::class)->where('order_id', $id)->first();

        return $this->render('client/orders/show', ['order' => $order, 'items' => $items, 'invoice' => $invoice]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function renderOrderForm(array $data = []): string
    {
        $productModel = model(ProductModel::class);
        $products = $productModel->listActive();
        $productIds = array_column($products, 'id');
        $galleryByProduct = model(ProductImageModel::class)->groupedByProductIds($productIds);
        $deliveryFields = $this->resolveDeliveryFields($data['order']['delivery_address'] ?? null);

        return $this->render('client/orders/create', $data + [
            'products' => $products,
            'galleryByProduct' => $galleryByProduct,
            'deliveryFields' => $deliveryFields,
        ]);
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function deliveryAddressRules(): array
    {
        return [
            'delivery_street' => 'required',
            'delivery_city' => 'required',
            'delivery_postal_code' => 'required',
        ];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function composeDeliveryAddress(): string
    {
        $parts = [
            trim((string) $this->request->getPost('delivery_street')),
            trim((string) $this->request->getPost('delivery_city')),
            trim((string) $this->request->getPost('delivery_postal_code')),
        ];

        return implode(', ', array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function resolveDeliveryFields(?string $deliveryAddress = null): array
    {
        $street = old('delivery_street');
        $city = old('delivery_city');
        $postalCode = old('delivery_postal_code');

        if ($street !== null || $city !== null || $postalCode !== null) {
            return [
                'street' => trim((string) $street),
                'city' => trim((string) $city),
                'postal_code' => trim((string) $postalCode),
            ];
        }

        if ($deliveryAddress !== null && trim($deliveryAddress) !== '') {
            return $this->splitDeliveryAddress($deliveryAddress);
        }

        $user = current_user();

        return [
            'street' => trim((string) ($user['address'] ?? '')),
            'city' => trim((string) ($user['city'] ?? '')),
            'postal_code' => trim((string) ($user['postal_code'] ?? '')),
        ];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function splitDeliveryAddress(string $deliveryAddress): array
    {
        $parts = array_values(array_filter(array_map('trim', explode(',', $deliveryAddress)), static fn (string $part): bool => $part !== ''));

        if ($parts === []) {
            return ['street' => '', 'city' => '', 'postal_code' => ''];
        }

        $street = $parts[0] ?? '';
        $city = $parts[1] ?? '';
        $postalCode = $parts[2] ?? '';

        if (count($parts) === 2 && preg_match('/^[0-9A-Za-z -]{4,10}$/', $parts[1])) {
            $city = '';
            $postalCode = $parts[1];
        }

        if (count($parts) > 3) {
            $street = implode(', ', array_slice($parts, 0, -2));
            $city = $parts[count($parts) - 2];
            $postalCode = $parts[count($parts) - 1];
        }

        return [
            'street' => $street,
            'city' => $city,
            'postal_code' => $postalCode,
        ];
    }

    /**
     * Ejecuta una operacion especifica de este componente.
     */
    private function buildOrderItems(): array
    {
        $productModel = model(ProductModel::class);
        $stockModel = model(StockModel::class);
        $productIds = (array) ($this->request->getPost('product_id') ?? []);
        $quantities = (array) ($this->request->getPost('quantity') ?? []);
        $items = [];
        $total = 0.0;

        foreach ($productIds as $i => $productId) {
            $qty = (int) ($quantities[$i] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $product = $productModel->find((int) $productId);
            if (! $product || ($product['status'] ?? null) !== 'activo') {
                continue;
            }

            $availableStock = $stockModel->availableQuantityForProduct((int) $productId);

            if ($qty > $availableStock) {
                return [[], 0.0, 'No puedes pedir mas unidades de las disponibles en stock para "' . ($product['name'] ?? 'este producto') . '".'];
            }

            $subtotal = $qty * (float) $product['price'];
            $total += $subtotal;
            $items[] = [
                'product_id' => $productId,
                'quantity' => $qty,
                'unit_price' => $product['price'],
                'tax_rate' => $product['tax_rate'],
                'subtotal' => $subtotal,
            ];
        }

        return [$items, $total, null];
    }
}
