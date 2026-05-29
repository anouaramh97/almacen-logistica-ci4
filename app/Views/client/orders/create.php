<?php // Vista de cliente: muestra pedidos, facturas o productos disponibles para el usuario. ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$isEdit = ($mode ?? 'create') === 'edit';
$order = $order ?? null;
$quantitiesByProduct = $quantitiesByProduct ?? [];
$oldQuantities = old('quantity');
$deliveryFields = $deliveryFields ?? ['street' => '', 'city' => '', 'postal_code' => ''];
$notes = old('notes', $order['notes'] ?? '');
$formAction = $isEdit && ! empty($order['id'])
    ? site_url('client/orders/update/' . $order['id'])
    : site_url('client/orders');
$pageTitle = $isEdit ? 'Modificar pedido' : 'Nuevo pedido';
$pageCopy = $isEdit
    ? 'Puedes ajustar productos, cantidades y datos de entrega mientras el pedido siga pendiente.'
    : 'Selecciona productos, define cantidades y completa la direccion final de entrega.';
$submitLabel = $isEdit ? 'Guardar cambios' : 'Crear pedido';
$cancelUrl = $isEdit && ! empty($order['id']) ? site_url('client/orders/' . $order['id']) : site_url('client/orders');
$categories = [];
foreach ($products as $product) {
    $categoryName = trim((string) ($product['category_name'] ?? ''));
    if ($categoryName === '') {
        continue;
    }

    $categories[$categoryName] = $categoryName;
}
ksort($categories, SORT_NATURAL | SORT_FLAG_CASE);
?>
<section class="card form-card">
    <style>
        .catalog-tools {
            display: grid;
            grid-template-columns: minmax(0, 2fr) repeat(2, minmax(180px, 1fr)) auto;
            gap: .85rem;
            align-items: end;
            margin: 1.25rem 0 1rem;
            padding: 1rem;
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 22px;
            background: linear-gradient(180deg, rgba(248, 251, 255, .98), rgba(255, 255, 255, .98));
        }

        .catalog-tools .field {
            margin: 0;
        }

        .catalog-results {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            color: #6e7c90;
            font-size: .94rem;
            flex-wrap: wrap;
        }

        .catalog-product-cell {
            min-width: 240px;
        }

        .catalog-product-meta {
            display: flex;
            gap: .45rem;
            flex-wrap: wrap;
            margin-top: .55rem;
        }

        .catalog-product-link {
            color: inherit;
            text-decoration: none;
        }

        .catalog-product-link:hover,
        .catalog-product-link:focus {
            color: var(--brand);
            text-decoration: underline;
            outline: none;
        }

        .catalog-product-photo-link {
            display: inline-flex;
            border-radius: 18px;
        }

        .catalog-product-thumb-link {
            display: inline-flex;
            border-radius: 12px;
        }

        .catalog-sort-button {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: 0;
            border: 0;
            background: transparent;
            color: inherit;
            font: inherit;
            font-weight: 800;
            text-transform: inherit;
            letter-spacing: inherit;
            cursor: pointer;
        }

        .catalog-sort-button:hover,
        .catalog-sort-button:focus {
            color: var(--brand);
            outline: none;
        }

        .catalog-sort-indicator {
            min-width: 2.2rem;
            color: var(--muted);
            font-size: .72rem;
            text-transform: none;
        }

        .catalog-empty-row td {
            padding: 1rem .75rem;
            text-align: center;
            color: #6e7c90;
        }

        .stock-alert {
            display: none;
            margin-bottom: 1rem;
            padding: .95rem 1rem;
            border-radius: 18px;
            border: 1px solid #fecdd3;
            background: #fff1f2;
            color: #9f1239;
        }

        .stock-alert.is-visible {
            display: block;
        }

        .quantity-input.is-invalid {
            border-color: #e11d48;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, .12);
        }

        @media (max-width: 900px) {
            .catalog-tools {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .catalog-tools {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="heading">
        <h2><?= esc($pageTitle) ?></h2>
        <p><?= esc($pageCopy) ?></p>
    </div>

    <div id="stock-alert" class="stock-alert" role="alert" aria-live="polite"></div>

    <form method="post" action="<?= esc($formAction) ?>">
        <?= csrf_field() ?>

        <div class="grid-2">
            <div class="field">
                <label>Direccion</label>
                <input name="delivery_street" value="<?= esc($deliveryFields['street']) ?>" required>
            </div>
            <div class="field">
                <label>Ciudad o poblacion</label>
                <input name="delivery_city" value="<?= esc($deliveryFields['city']) ?>" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
                <label>Codigo postal</label>
                <input name="delivery_postal_code" value="<?= esc($deliveryFields['postal_code']) ?>" required>
            </div>
            <div class="field">
                <label>Notas</label>
                <textarea name="notes"><?= esc($notes) ?></textarea>
            </div>
        </div>

        <div class="catalog-tools">
            <div class="field">
                <label for="product-search">Buscar producto</label>
                <input
                    id="product-search"
                    type="search"
                    placeholder="Busca por nombre, SKU o descripción"
                    autocomplete="off"
                >
            </div>
            <div class="field">
                <label for="product-category-filter">Categoría</label>
                <select id="product-category-filter">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc($category) ?>"><?= esc($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="product-stock-filter">Disponibilidad</label>
                <select id="product-stock-filter">
                    <option value="">Todas</option>
                    <option value="in-stock">Con stock</option>
                    <option value="out-of-stock">Sin stock</option>
                </select>
            </div>
            <button type="button" class="btn btn-outline" id="clear-product-filters">Limpiar filtros</button>
        </div>

        <div class="catalog-results">
            <div id="catalog-results-text">Mostrando <?= count($products) ?> de <?= count($products) ?> productos</div>
            <div>El pedido conserva las cantidades que ya hayas escrito aunque filtres la lista.</div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><button type="button" class="catalog-sort-button" data-sort-button="product">Producto <span class="catalog-sort-indicator" data-sort-indicator="product"></span></button></th>
                        <th>Categoría</th>
                        <th>Foto</th>
                        <th><button type="button" class="catalog-sort-button" data-sort-button="price">Precio <span class="catalog-sort-indicator" data-sort-indicator="price"></span></button></th>
                        <th>IVA</th>
                        <th><button type="button" class="catalog-sort-button" data-sort-button="stock">Stock <span class="catalog-sort-indicator" data-sort-indicator="stock"></span></button></th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody id="catalog-products-body">
                    <?php foreach ($products as $index => $product): ?>
                        <?php $gallery = $galleryByProduct[$product['id']] ?? []; ?>
                        <?php
                        $productName = (string) ($product['name'] ?? '');
                        $productSku = (string) ($product['sku'] ?? '');
                        $productDescription = (string) ($product['description'] ?? '');
                        $categoryName = (string) ($product['category_name'] ?? 'Sin categoría');
                        $stockTotal = (int) ($product['stock_total'] ?? 0);
                        $searchIndex = mb_strtolower(trim($productName . ' ' . $productSku . ' ' . $productDescription . ' ' . $categoryName));
                        $shortDescription = trim($productDescription);
                        $quantityValue = 0;
                        if (is_array($oldQuantities)) {
                            $quantityValue = (int) ($oldQuantities[$index] ?? 0);
                        } elseif ($isEdit) {
                            $quantityValue = (int) ($quantitiesByProduct[(int) $product['id']] ?? 0);
                        }
                        if (mb_strlen($shortDescription) > 120) {
                            $shortDescription = rtrim(mb_substr($shortDescription, 0, 117)) . '...';
                        }
                        $productUrl = site_url('client/products/' . $product['id']);
                        ?>
                        <tr
                            class="catalog-product-row"
                            data-search="<?= esc($searchIndex) ?>"
                            data-category="<?= esc($categoryName) ?>"
                            data-stock-state="<?= $stockTotal > 0 ? 'in-stock' : 'out-of-stock' ?>"
                            data-sort-product="<?= esc(mb_strtolower($productName)) ?>"
                            data-sort-price="<?= esc((string) ((float) $product['price'])) ?>"
                            data-sort-stock="<?= esc((string) $stockTotal) ?>"
                        >
                            <td class="catalog-product-cell">
                                <input type="hidden" name="product_id[]" value="<?= $product['id'] ?>">
                                <strong><a href="<?= esc($productUrl) ?>" class="catalog-product-link" target="_blank" rel="noopener noreferrer"><?= esc($product['name']) ?></a></strong>
                                <div class="muted"><?= esc($product['sku']) ?></div>
                                <?php if ($shortDescription !== ''): ?>
                                    <div class="muted" style="margin-top:.35rem;"><?= esc($shortDescription) ?></div>
                                <?php endif; ?>
                                <div class="catalog-product-meta">
                                    <span class="pill"><?= esc($categoryName) ?></span>
                                    <span class="pill <?= $stockTotal > 0 ? 'is-success-soft' : 'is-warning' ?>">
                                        <?= $stockTotal > 0 ? 'Disponible' : 'Agotado' ?>
                                    </span>
                                </div>
                                <?php if ($gallery): ?>
                                    <div style="display:flex;gap:.45rem;flex-wrap:wrap;margin-top:.6rem;">
                                        <?php foreach (array_slice($gallery, 0, 3) as $galleryImage): ?>
                                            <a href="<?= esc($productUrl) ?>" class="catalog-product-thumb-link" target="_blank" rel="noopener noreferrer" aria-label="Ver <?= esc($product['name']) ?>">
                                                <img
                                                    src="<?= esc(product_image_url($galleryImage['path'] ?? null, $product['name'])) ?>"
                                                    alt="<?= esc($product['name']) ?>"
                                                    style="width:40px;height:40px;object-fit:cover;border-radius:12px;border:1px solid rgba(15,23,42,.08);"
                                                >
                                            </a>
                                        <?php endforeach; ?>
                                        <?php if (count($gallery) > 3): ?>
                                            <span class="pill" style="align-self:center;">+<?= count($gallery) - 3 ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($categoryName) ?></td>
                            <td style="width:90px;">
                                <a href="<?= esc($productUrl) ?>" class="catalog-product-photo-link" target="_blank" rel="noopener noreferrer" aria-label="Ver <?= esc($product['name']) ?>">
                                    <img
                                        src="<?= esc(product_image_url($product['image_path'] ?? null, $product['name'])) ?>"
                                        alt="<?= esc($product['name']) ?>"
                                        style="width:64px;height:64px;object-fit:cover;border-radius:18px;"
                                    >
                                </a>
                            </td>
                            <td><?= number_format((float) $product['price'], 2) ?> EUR</td>
                            <td><?= number_format((float) $product['tax_rate'], 2) ?>%</td>
                            <td>
                                <span class="pill <?= $stockTotal > 0 ? 'is-success-soft' : 'is-warning' ?>">
                                    <?= esc((string) $stockTotal) ?>
                                </span>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="quantity[]"
                                    value="<?= esc((string) $quantityValue) ?>"
                                    min="0"
                                    max="<?= esc((string) $stockTotal) ?>"
                                    class="quantity-input"
                                    data-product-name="<?= esc($productName) ?>"
                                    data-stock-total="<?= esc((string) $stockTotal) ?>"
                                >
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr id="catalog-empty-row" class="catalog-empty-row" hidden>
                        <td colspan="7">No hay productos que coincidan con los filtros actuales.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="toolbar" style="margin-top:1rem;">
            <button class="btn btn-primary"><?= esc($submitLabel) ?></button>
            <a class="btn btn-outline" href="<?= esc($cancelUrl) ?>">Cancelar</a>
        </div>
    </form>
</section>
<script>
    (() => {
        const searchInput = document.getElementById('product-search');
        const categoryFilter = document.getElementById('product-category-filter');
        const stockFilter = document.getElementById('product-stock-filter');
        const clearButton = document.getElementById('clear-product-filters');
        const resultsText = document.getElementById('catalog-results-text');
        const emptyRow = document.getElementById('catalog-empty-row');
        const stockAlert = document.getElementById('stock-alert');
        const form = document.querySelector('form');
        const tableBody = document.getElementById('catalog-products-body');
        const rows = Array.from(document.querySelectorAll('.catalog-product-row'));
        const quantityInputs = Array.from(document.querySelectorAll('.quantity-input'));
        const sortButtons = Array.from(document.querySelectorAll('[data-sort-button]'));
        const sortIndicators = Array.from(document.querySelectorAll('[data-sort-indicator]'));
        let currentSort = { key: '', direction: 'asc' };

        if (!searchInput || !categoryFilter || !stockFilter || !clearButton || !resultsText || !emptyRow || !stockAlert || !form || !tableBody || !rows.length) {
            return;
        }

        const totalRows = rows.length;

        const updateSortIndicators = () => {
            sortIndicators.forEach((indicator) => {
                const key = indicator.dataset.sortIndicator || '';
                indicator.textContent = key === currentSort.key ? currentSort.direction : '';
            });
        };

        const sortRows = (key) => {
            const direction = currentSort.key === key && currentSort.direction === 'asc' ? 'desc' : 'asc';
            currentSort = { key, direction };

            const sortedRows = [...rows].sort((a, b) => {
                const aValue = a.dataset[`sort${key.charAt(0).toUpperCase()}${key.slice(1)}`] || '';
                const bValue = b.dataset[`sort${key.charAt(0).toUpperCase()}${key.slice(1)}`] || '';
                const result = key === 'product'
                    ? aValue.localeCompare(bValue, 'es', { sensitivity: 'base' })
                    : Number.parseFloat(aValue) - Number.parseFloat(bValue);

                return direction === 'asc' ? result : -result;
            });

            sortedRows.forEach((row) => tableBody.insertBefore(row, emptyRow));
            updateSortIndicators();
            applyFilters();
        };

        const applyFilters = () => {
            const query = searchInput.value.trim().toLowerCase();
            const category = categoryFilter.value.trim().toLowerCase();
            const stock = stockFilter.value;
            let visibleCount = 0;

            rows.forEach((row) => {
                const matchesQuery = query === '' || (row.dataset.search || '').includes(query);
                const matchesCategory = category === '' || (row.dataset.category || '').toLowerCase() === category;
                const matchesStock = stock === '' || row.dataset.stockState === stock;
                const isVisible = matchesQuery && matchesCategory && matchesStock;

                row.hidden = !isVisible;
                if (isVisible) {
                    visibleCount += 1;
                }
            });

            emptyRow.hidden = visibleCount !== 0;
            resultsText.textContent = `Mostrando ${visibleCount} de ${totalRows} productos`;
        };

        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            categoryFilter.value = '';
            stockFilter.value = '';
            applyFilters();
            searchInput.focus();
        });

        const showStockAlert = (message) => {
            stockAlert.textContent = message;
            stockAlert.classList.add('is-visible');
        };

        const hideStockAlert = () => {
            stockAlert.textContent = '';
            stockAlert.classList.remove('is-visible');
        };

        const validateQuantityInput = (input) => {
            const stockTotal = Number.parseInt(input.dataset.stockTotal || '0', 10);
            const productName = input.dataset.productName || 'este producto';
            const rawValue = input.value.trim();
            const quantity = rawValue === '' ? 0 : Number.parseInt(rawValue, 10);

            if (!Number.isFinite(quantity) || quantity <= stockTotal) {
                input.classList.remove('is-invalid');
                if (!document.querySelector('.quantity-input.is-invalid')) {
                    hideStockAlert();
                }
                return true;
            }

            input.value = String(stockTotal);
            input.classList.add('is-invalid');
            showStockAlert(`No puedes pedir mas de ${stockTotal} unidades de ${productName} porque solo hay ${stockTotal} en stock.`);
            input.reportValidity?.();
            return false;
        };

        quantityInputs.forEach((input) => {
            input.addEventListener('input', () => {
                const wasInvalid = input.classList.contains('is-invalid');
                const isValid = validateQuantityInput(input);

                if (wasInvalid && isValid) {
                    input.classList.remove('is-invalid');
                }
            });

            input.addEventListener('blur', () => {
                if (input.value.trim() === '') {
                    input.classList.remove('is-invalid');
                    if (!document.querySelector('.quantity-input.is-invalid')) {
                        hideStockAlert();
                    }
                    return;
                }

                validateQuantityInput(input);
            });
        });

        form.addEventListener('submit', (event) => {
            let hasErrors = false;

            quantityInputs.forEach((input) => {
                if (!validateQuantityInput(input)) {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                event.preventDefault();
                const firstInvalid = document.querySelector('.quantity-input.is-invalid');
                firstInvalid?.focus();
            }
        });

        searchInput.addEventListener('input', applyFilters);
        categoryFilter.addEventListener('change', applyFilters);
        stockFilter.addEventListener('change', applyFilters);
        sortButtons.forEach((button) => {
            button.addEventListener('click', () => sortRows(button.dataset.sortButton || 'product'));
        });
        updateSortIndicators();
        applyFilters();
    })();
</script>
<?= $this->endSection() ?>
