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
