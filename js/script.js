const filterButtons = document.querySelectorAll("[data-filter]");
const shopProducts = document.querySelectorAll(".shop-grid .product-card");
const productCount = document.querySelector(".product-count");
const productSearch = document.querySelector("#product-search");

let activeCategory = "all";

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

const tiltCard = document.querySelector('[data-tilt-card]');
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (tiltCard && !reduceMotion) {
    const tiltLimit = 9;

    tiltCard.addEventListener('mousemove', (event) => {
        const rect = tiltCard.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width - 0.5;
        const y = (event.clientY - rect.top) / rect.height - 0.5;

        tiltCard.style.setProperty('--tilt-y', `${x * tiltLimit}deg`);
        tiltCard.style.setProperty('--tilt-x', `${y * -tiltLimit}deg`);
    });

    tiltCard.addEventListener('mouseleave', () => {
        tiltCard.style.setProperty('--tilt-y', '0deg');
        tiltCard.style.setProperty('--tilt-x', '0deg');
    });
}