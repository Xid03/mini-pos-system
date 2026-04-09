<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/bootstrap.php';
require_once __DIR__ . '/../../includes/helpers/ui.php';
require_once __DIR__ . '/../../includes/helpers/pos.php';

require_role('admin', 'cashier');

$pageTitle = 'Point of Sale';
$currentPage = 'pos';
$search = normalize_search($_GET['search'] ?? '');
$checkoutForm = pos_checkout_defaults();
$checkoutErrors = [];
$checkoutValidation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'add-item') {
        $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$productId) {
            set_flash_message('error', 'Please choose a valid product to add.');
            redirect('modules/pos/index.php');
        }

        $result = add_to_pos_cart($productId, $quantity ?: 1);
        clear_pos_recent_sale();
        set_flash_message($result['success'] ? 'success' : 'error', $result['message']);
        redirect('modules/pos/index.php');
    }

    if ($action === 'update-cart') {
        if (isset($_POST['remove_product_id'])) {
            $productId = filter_var($_POST['remove_product_id'], FILTER_VALIDATE_INT);

            if ($productId) {
                remove_from_pos_cart($productId);
                clear_pos_recent_sale();
                set_flash_message('success', 'Item removed from cart.');
            }

            redirect('modules/pos/index.php');
        }

        $quantities = $_POST['quantities'] ?? [];
        $result = update_pos_cart_quantities(is_array($quantities) ? $quantities : []);
        clear_pos_recent_sale();
        set_flash_message('success', $result['message']);
        redirect('modules/pos/index.php');
    }

    if ($action === 'clear-cart') {
        clear_pos_cart();
        clear_pos_recent_sale();
        set_flash_message('success', 'Cart cleared successfully.');
        redirect('modules/pos/index.php');
    }

    if ($action === 'checkout') {
        $checkoutForm = [
            'payment_method' => trim((string) ($_POST['payment_method'] ?? 'cash')),
            'paid_amount' => trim((string) ($_POST['paid_amount'] ?? '')),
        ];
        $cartItems = pos_cart_details();
        $checkoutValidation = validate_pos_checkout($_POST, $cartItems);
        $checkoutErrors = $checkoutValidation['errors'];

        if ($checkoutErrors === []) {
            try {
                $result = complete_pos_checkout($cartItems, $checkoutValidation['data'], current_user_id());
                set_flash_message('success', 'Transaction ' . $result['invoice_number'] . ' completed successfully.');
                redirect('modules/pos/index.php');
            } catch (RuntimeException $exception) {
                $checkoutErrors['cart'] = $exception->getMessage();
            }
        }
    }
}

$products = pos_product_catalog($search);
$cartItems = pos_cart_details();
$totals = $checkoutValidation['totals'] ?? pos_cart_totals($cartItems);
$recentSale = pos_recent_sale();

require __DIR__ . '/../../includes/layout/app-shell-start.php';
?>
<section class="hero-panel">
    <div class="hero-content glass-card">
        <span class="badge-soft-primary mb-3">
            <i class="bi bi-receipt-cutoff"></i>
            Live POS Counter
        </span>
        <h3>Search products, build a cart fast, take payment, and save the sale with stock deduction.</h3>
        <p>
            This POS screen is designed for quick cashier flow. It combines product lookup, cart management, totals,
            payment handling, and transaction saving while preventing sales that exceed available stock.
        </p>
        <div class="hero-actions">
            <a href="<?= htmlspecialchars(url('modules/transactions/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">
                <i class="bi bi-clock-history me-2"></i>
                Transaction Module
            </a>
            <a href="<?= htmlspecialchars(url('modules/inventory/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">
                <i class="bi bi-boxes me-2"></i>
                Check Inventory
            </a>
        </div>
    </div>

    <div class="feature-grid">
        <article class="summary-card glass-card">
            <div class="summary-card__icon primary"><i class="bi bi-cart3"></i></div>
            <span class="summary-card__label">Cart Items</span>
            <strong class="summary-card__value"><?= pos_cart_count(); ?></strong>
            <span class="summary-card__change">Units currently queued for checkout</span>
        </article>
        <article class="summary-card glass-card">
            <div class="summary-card__icon success"><i class="bi bi-cash-coin"></i></div>
            <span class="summary-card__label">Current Total</span>
            <strong class="summary-card__value">RM <?= htmlspecialchars(number_format($totals['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span class="summary-card__change">Calculated from the active cart only</span>
        </article>
    </div>
</section>

<?php if ($recentSale !== null): ?>
    <section class="section-card glass-card">
        <div class="admin-toolbar">
            <div>
                <h3 class="section-title">Latest Completed Sale</h3>
                <p class="section-subtitle">Quick confirmation after checkout before receipt and history screens arrive in Step 6.</p>
            </div>
            <div class="table-actions">
                <a href="<?= htmlspecialchars(url('modules/transactions/view.php?id=' . (int) $recentSale['sale_id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">View Details</a>
                <a href="<?= htmlspecialchars(url('modules/transactions/receipt.php?id=' . (int) $recentSale['sale_id']), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-soft">Print Receipt</a>
            </div>
        </div>
        <div class="pos-success-grid">
            <div class="feature-card glass-card">
                <div class="feature-card__icon"><i class="bi bi-receipt"></i></div>
                <h4><?= htmlspecialchars((string) $recentSale['invoice_number'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p><?= (int) $recentSale['item_count']; ?> line item(s) completed successfully.</p>
            </div>
            <div class="feature-card glass-card">
                <div class="feature-card__icon"><i class="bi bi-wallet2"></i></div>
                <h4>RM <?= htmlspecialchars(number_format((float) $recentSale['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></h4>
                <p>Total charged via <?= htmlspecialchars(ucfirst((string) $recentSale['payment_method']), ENT_QUOTES, 'UTF-8'); ?>.</p>
            </div>
            <div class="feature-card glass-card">
                <div class="feature-card__icon"><i class="bi bi-currency-exchange"></i></div>
                <h4>RM <?= htmlspecialchars(number_format((float) $recentSale['balance_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></h4>
                <p>Balance returned to the customer.</p>
            </div>
        </div>
    </section>
<?php endif; ?>

<section class="pos-layout">
    <article class="section-card glass-card">
        <div class="admin-toolbar">
            <div>
                <h3 class="section-title">Product Search</h3>
                <p class="section-subtitle">Add active products with available stock into the cashier cart.</p>
            </div>
            <form method="get" class="admin-filter-form admin-filter-form--wide">
                <div class="search-input-group">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Search by product, SKU, or category" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-soft">Search</button>
                <?php if ($search !== ''): ?>
                    <a href="<?= htmlspecialchars(url('modules/pos/index.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($products === []): ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="bi bi-search-heart"></i></div>
                <h4>No products found</h4>
                <p><?= $search === '' ? 'Create active products first so they can appear in the POS counter.' : 'Try another search keyword or clear the current search.'; ?></p>
                <a href="<?= htmlspecialchars(url('modules/products/create.php'), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">Create Product</a>
            </div>
        <?php else: ?>
            <div class="pos-product-grid">
                <?php foreach ($products as $product): ?>
                    <?php
                    $availableStock = (int) $product['stock_quantity'];
                    $isOutOfStock = $availableStock === 0;
                    $isLowStock = $availableStock <= (int) $product['min_stock_level'];
                    ?>
                    <article class="pos-product-card">
                        <div class="pos-product-card__top">
                            <span class="badge-soft-info"><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="<?= $isOutOfStock ? 'badge-soft-warning' : ($isLowStock ? 'badge-soft-warning' : 'badge-soft-success'); ?>">
                                <?= $isOutOfStock ? 'Out of stock' : ($isLowStock ? 'Low stock' : 'Ready'); ?>
                            </span>
                        </div>
                        <h4><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                        <p><?= htmlspecialchars((string) ($product['description'] ?: 'No description available.'), ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="pos-product-card__meta">
                            <div>
                                <strong>RM <?= htmlspecialchars(number_format((float) $product['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </div>
                            <div class="text-end">
                                <strong><?= $availableStock; ?> units</strong>
                                <small>Min <?= (int) $product['min_stock_level']; ?></small>
                            </div>
                        </div>
                        <form method="post" class="pos-add-form">
                            <input type="hidden" name="action" value="add-item">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
                            <input type="number" name="quantity" class="form-control" min="1" max="<?= $availableStock; ?>" value="1" <?= $isOutOfStock ? 'disabled' : ''; ?>>
                            <button type="submit" class="btn btn-primary" <?= $isOutOfStock ? 'disabled' : ''; ?>>
                                <i class="bi bi-plus-circle me-2"></i>
                                Add
                            </button>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <aside class="section-card glass-card pos-cart-panel">
        <div class="admin-toolbar">
            <div>
                <h3 class="section-title">Current Cart</h3>
                <p class="section-subtitle">Update quantities, remove lines, and finish checkout.</p>
            </div>
            <?php if ($cartItems !== []): ?>
                <form
                    method="post"
                    data-confirm-dialog
                    data-confirm-title="Clear cart?"
                    data-confirm-message="This will remove all items currently queued in the POS cart."
                    data-confirm-button="Clear Cart"
                >
                    <input type="hidden" name="action" value="clear-cart">
                    <button type="submit" class="btn btn-light btn-sm">Clear Cart</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (isset($checkoutErrors['cart'])): ?>
            <div class="alert alert-danger custom-alert" role="alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <span><?= htmlspecialchars((string) $checkoutErrors['cart'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($cartItems === []): ?>
            <div class="empty-state empty-state--compact">
                <div class="empty-state__icon"><i class="bi bi-cart-x"></i></div>
                <h4>Your cart is empty</h4>
                <p>Select products from the left panel to start a new transaction.</p>
            </div>
        <?php else: ?>
            <form method="post" class="admin-form">
                <input type="hidden" name="action" value="update-cart">
                <div class="pos-cart-list">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="pos-cart-item <?= $item['status'] !== 'ok' ? 'pos-cart-item--warning' : ''; ?>">
                            <div class="pos-cart-item__head">
                                <div>
                                    <h4><?= htmlspecialchars((string) $item['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <small><?= htmlspecialchars((string) $item['sku'], ENT_QUOTES, 'UTF-8'); ?> • <?= htmlspecialchars((string) $item['category_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </div>
                                <button type="submit" name="remove_product_id" value="<?= (int) $item['id']; ?>" class="btn btn-light btn-sm" aria-label="Remove item">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="pos-cart-item__meta">
                                <span>RM <?= htmlspecialchars(number_format((float) $item['unit_price'], 2), ENT_QUOTES, 'UTF-8'); ?></span>
                                <span>Available: <?= (int) $item['available_stock']; ?></span>
                            </div>
                            <div class="pos-cart-item__controls">
                                <input
                                    type="number"
                                    min="0"
                                    max="<?= (int) $item['available_stock']; ?>"
                                    name="quantities[<?= (int) $item['id']; ?>]"
                                    class="form-control"
                                    value="<?= (int) $item['requested_quantity']; ?>"
                                >
                                <strong>RM <?= htmlspecialchars(number_format((float) $item['line_total'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                            <?php if ($item['status'] === 'insufficient'): ?>
                                <small class="text-danger">Requested quantity exceeds available stock.</small>
                            <?php elseif ($item['status'] === 'unavailable'): ?>
                                <small class="text-danger">This product is no longer available for sale.</small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-soft w-100">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Update Cart Quantities
                </button>
            </form>

            <div class="pos-summary-card">
                <div class="pos-summary-row">
                    <span>Subtotal</span>
                    <strong>RM <?= htmlspecialchars(number_format($totals['subtotal'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="pos-summary-row">
                    <span>Tax</span>
                    <strong>RM <?= htmlspecialchars(number_format($totals['tax_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="pos-summary-row">
                    <span>Discount</span>
                    <strong>RM <?= htmlspecialchars(number_format($totals['discount_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div class="pos-summary-row pos-summary-row--total">
                    <span>Total</span>
                    <strong data-pos-total-display>RM <?= htmlspecialchars(number_format($totals['total_amount'], 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
            </div>

            <form method="post" class="admin-form pos-checkout-form" data-pos-checkout data-total-amount="<?= htmlspecialchars(number_format($totals['total_amount'], 2, '.', ''), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="checkout">
                <div class="form-grid">
                    <div>
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="form-select <?= isset($checkoutErrors['payment_method']) ? 'is-invalid' : ''; ?>">
                            <option value="cash" <?= $checkoutForm['payment_method'] === 'cash' ? 'selected' : ''; ?>>Cash</option>
                            <option value="card" <?= $checkoutForm['payment_method'] === 'card' ? 'selected' : ''; ?>>Card</option>
                            <option value="ewallet" <?= $checkoutForm['payment_method'] === 'ewallet' ? 'selected' : ''; ?>>E-Wallet</option>
                        </select>
                        <?php if (isset($checkoutErrors['payment_method'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars((string) $checkoutErrors['payment_method'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="paid_amount" class="form-label">Paid Amount (RM)</label>
                        <input type="number" step="0.01" min="0" id="paid_amount" name="paid_amount" class="form-control <?= isset($checkoutErrors['paid_amount']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars((string) $checkoutForm['paid_amount'], ENT_QUOTES, 'UTF-8'); ?>" data-pos-paid>
                        <?php if (isset($checkoutErrors['paid_amount'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars((string) $checkoutErrors['paid_amount'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pos-balance-panel">
                    <div>
                        <span>Change / Balance</span>
                        <strong data-pos-balance>RM <?= htmlspecialchars(number_format(max(0, (float) ($checkoutForm['paid_amount'] !== '' ? $checkoutForm['paid_amount'] : 0) - $totals['total_amount']), 2), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <small>Paid amount must cover the total before the transaction can be saved.</small>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-check2-circle me-2"></i>
                    Complete Sale
                </button>
            </form>
        <?php endif; ?>
    </aside>
</section>
<?php require __DIR__ . '/../../includes/layout/app-shell-end.php'; ?>
