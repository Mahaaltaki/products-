<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 960px; margin: auto; }
        .search-box { margin-bottom: 20px; }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .product-item.out-of-stock {
            border-color: #dc3545;
            background-color: #f8d7da;
            color: #dc3545;
        }
        .product-item h3 { margin-top: 0; }
        .pagination { margin-top: 20px; text-align: center; }
        .pagination button {
            padding: 8px 15px;
            margin: 0 5px;
            border: 1px solid #007bff;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .pagination button:disabled {
            background-color: #cccccc;
            border-color: #cccccc;
            cursor: not-allowed;
        }
        .pagination span {
            padding: 8px 15px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Our Products</h1>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search by product name..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div class="product-grid" id="productGrid">
            <p>Loading products...</p>
        </div>

        <div class="pagination">
            <button id="prevBtn" disabled>Previous</button>
            <span id="pageInfo">Page 1</span>
            <button id="nextBtn">Next</button>
        </div>
    </div>

    <script>
        const productGrid = document.getElementById('productGrid');
        const searchInput = document.getElementById('searchInput');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageInfo = document.getElementById('pageInfo');

        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        const itemsPerPage = 6;

        async function fetchProducts(searchQuery = '') {
            productGrid.innerHTML = '<p>Loading products...</p>';
            const apiUrl = `/api/products${searchQuery ? `?search=${searchQuery}` : ''}`;
            try {
                const response = await fetch(apiUrl);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                allProducts = await response.json();
                applySearchFilter();
            } catch (error) {
                console.error("Error fetching products:", error);
                productGrid.innerHTML = `<p style="color: red;">Failed to load products. Error: ${error.message}</p>`;
            }
        }

        function applySearchFilter() {
            const query = searchInput.value.toLowerCase();
            filteredProducts = allProducts.filter(product =>
                product.name.toLowerCase().includes(query)
            );
            currentPage = 1;
            displayProducts();
        }

        function displayProducts() {
            productGrid.innerHTML = '';
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const productsToDisplay = filteredProducts.slice(startIndex, endIndex);

            if (productsToDisplay.length === 0) {
                productGrid.innerHTML = '<p>No products found.</p>';
            } else {
                productsToDisplay.forEach(product => {
                    const productItem = document.createElement('div');
                    productItem.classList.add('product-item');

                    if (product.stock <= 0) {
                        productItem.classList.add('out-of-stock');
                    }

                    productItem.innerHTML = `
                        <h3>${product.name}</h3>
                        <p>Price: $${parseFloat(product.price).toFixed(2)}</p>
                        <p>Stock: ${product.stock > 0 ? product.stock : 'Out of Stock'}</p>
                    `;
                    productGrid.appendChild(productItem);
                });
            }
            updatePaginationControls();
        }

        function updatePaginationControls() {
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;

            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }

        searchInput.addEventListener('input', () => {
            applySearchFilter();
        });

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayProducts();
            }
        });

        nextBtn.addEventListener('click', () => {
            const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                displayProducts();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            fetchProducts();
        });
    </script>
</body>
</html>