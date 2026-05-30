const filterButtons = document.querySelectorAll("[data-filter]");
const shopProducts = document.querySelectorAll(".shop-grid .product-card");
const productCount = document.querySelector(".product-count");
const productSearch = document.querySelector("#product-search");
const productForm = document.querySelector(".product-form");
const cartItemsContainer = document.querySelector(".cart-items");
const cartSubtotal = document.querySelector("[data-cart-subtotal]");
const cartShipping = document.querySelector("[data-cart-shipping]");
const cartDiscount = document.querySelector("[data-cart-discount]");
const cartTotal = document.querySelector("[data-cart-total]");
const cartStorageKey = "tWorldCart";

let activeCategory = "all";

function formatPrice(value) {
    return `$${value.toFixed(2)}`;
}

function readCart() {
    const savedCart = localStorage.getItem(cartStorageKey);

    if (!savedCart) {
        return [];
    }

    try {
        return JSON.parse(savedCart);
    } catch {
        return [];
    }
}

function saveCart(cart) {
    localStorage.setItem(cartStorageKey, JSON.stringify(cart));
}

function updateProductCount(count) {
    if (!productCount) {
        return;
    }

    const productLabel = count === 1 ? "product" : "products";
    productCount.textContent = `Showing ${count} ${productLabel}`;
}

function filterProducts() {
    let visibleCount = 0;
    const searchTerm = productSearch ? productSearch.value.trim().toLowerCase() : "";

    shopProducts.forEach((product) => {
        const productCategory = product.dataset.category;
        const productName = product.dataset.name.toLowerCase();
        const matchesCategory = activeCategory === "all" || productCategory === activeCategory;
        const matchesSearch =
            productName.includes(searchTerm) || productCategory.includes(searchTerm);
        const shouldShow = matchesCategory && matchesSearch;

        product.classList.toggle("is-hidden", !shouldShow);

        if (shouldShow) {
            visibleCount += 1;
        }
    });

    updateProductCount(visibleCount);
}

filterButtons.forEach((button) => {
    button.addEventListener("click", () => {
        filterButtons.forEach((item) => item.classList.remove("active"));
        button.classList.add("active");

        activeCategory = button.dataset.filter;
        filterProducts();
    });
});

if (productSearch) {
    productSearch.addEventListener("input", filterProducts);

    function focusShopSearch() {
        if (window.location.hash === "#shop-search-area") {
            setTimeout(() => productSearch.focus(), 100);
        }
    }

    focusShopSearch();
    window.addEventListener("hashchange", focusShopSearch);
}

if (productForm) {
    productForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const selectedSize = productForm.querySelector('input[name="size"]:checked').value;
        const quantityInput = productForm.querySelector('input[name="quantity"]');
        const quantity = Math.max(1, Number(quantityInput.value));
        const product = {
            id: productForm.dataset.productId,
            name: productForm.dataset.productName,
            price: Number(productForm.dataset.productPrice),
            image: productForm.dataset.productImage,
            size: selectedSize,
            quantity
        };
        const cart = readCart();
        const existingItem = cart.find(
            (item) => item.id === product.id && item.size === product.size
        );

        if (existingItem) {
            existingItem.quantity += product.quantity;
        } else {
            cart.push(product);
        }

        saveCart(cart);
        window.location.href = "cart.html";
    });
}

function updateCartSummary(cart) {
    if (!cartSubtotal || !cartShipping || !cartDiscount || !cartTotal) {
        return;
    }

    const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const shipping = subtotal > 0 ? 5 : 0;
    const discount = 0;
    const total = subtotal + shipping - discount;

    cartSubtotal.textContent = formatPrice(subtotal);
    cartShipping.textContent = formatPrice(shipping);
    cartDiscount.textContent = `-${formatPrice(discount)}`;
    cartTotal.textContent = formatPrice(total);
}

function renderCart() {
    if (!cartItemsContainer) {
        return;
    }

    const cart = readCart();
    cartItemsContainer.innerHTML = "";

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <p class="empty-cart-message">
                Your cart is empty. Start by adding a T-shirt from the shop.
            </p>
        `;
        updateCartSummary(cart);
        return;
    }

    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        const cartItem = document.createElement("article");
        cartItem.className = "cart-item";
        cartItem.innerHTML = `
            <div class="cart-item-image image-placeholder" data-label="Product photo">
                <img
                    src="${item.image}"
                    alt="${item.name}"
                    loading="lazy"
                    onerror="this.hidden = true"
                />
            </div>

            <div class="cart-item-details">
                <h2>${item.name}</h2>
                <p>Size: ${item.size}</p>
                <button type="button" data-remove-index="${index}">Remove</button>
            </div>

            <div class="cart-item-quantity">
                <label for="cart-quantity-${index}">Qty</label>
                <input
                    id="cart-quantity-${index}"
                    type="number"
                    min="1"
                    value="${item.quantity}"
                    data-quantity-index="${index}"
                />
            </div>

            <p class="cart-item-price">${formatPrice(itemTotal)}</p>
        `;

        cartItemsContainer.appendChild(cartItem);
    });

    updateCartSummary(cart);
}

if (cartItemsContainer) {
    renderCart();

    cartItemsContainer.addEventListener("click", (event) => {
        const removeButton = event.target.closest("[data-remove-index]");

        if (!removeButton) {
            return;
        }

        const cart = readCart();
        cart.splice(Number(removeButton.dataset.removeIndex), 1);
        saveCart(cart);
        renderCart();
    });

    cartItemsContainer.addEventListener("input", (event) => {
        const quantityInput = event.target.closest("[data-quantity-index]");

        if (!quantityInput) {
            return;
        }

        const cart = readCart();
        const item = cart[Number(quantityInput.dataset.quantityIndex)];
        item.quantity = Math.max(1, Number(quantityInput.value));
        saveCart(cart);
        renderCart();
    });
}
