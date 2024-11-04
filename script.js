document.addEventListener('DOMContentLoaded', function() {
    // Categories dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('a');
        const content = dropdown.querySelector('.dropdown-content');
        
        // For mobile devices, handle click instead of hover
        if (window.innerWidth <=   768) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            });
        }
    });


    // Update the aplicarFiltros function
    function aplicarFiltros() {
        const form = document.getElementById('filtroForm');
        const formData = new FormData(form);

        // Realizar una solicitud AJAX a filtros.php
        fetch('filtros.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('productsGrid').innerHTML = data;
            // Reattach event listeners to new add-to-cart buttons
            attachAddToCartListeners();
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to attach event listeners to add-to-cart buttons
    function attachAddToCartListeners() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const productId = this.getAttribute('data-product-id');
                addToCart(productId);
            });
        });
    }

    // Initial attachment of event listeners
    attachAddToCartListeners();

    // Search functionality
    const searchForm = document.querySelector('.search-bar');
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const searchTerm = this.querySelector('input').value;
        search(searchTerm);
    });

    async function search(term) {
        try {
            const response = await fetch(`search-products.php?q=${encodeURIComponent(term)}`);
            if (response.ok) {
                const html = await response.text();
                document.querySelector('.products-grid').innerHTML = html;
            } else {
                throw new Error('Error en la búsqueda');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
});


document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default button behavior
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });

    function addToCart(productId) {
        let cart = JSON.parse(localStorage.getItem('cart')) || {};
        cart[productId] = (cart[productId] || 0) + 1; // Add only 1 product at a time
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        updateCartDropdown();
        alert('Producto añadido al carrito');
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || {};
        const count = Object.values(cart).reduce((a, b) => a + b, 0);
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }

    // Initial cart count update
    updateCartCount();

    // Cart dropdown functionality
    function updateCartDropdown() {
        const cartDropdown = document.getElementById('cart-dropdown');
        if (!cartDropdown) return;

        const cart = JSON.parse(localStorage.getItem('cart')) || {};
        let dropdownContent = '';

        for (const [productId, quantity] of Object.entries(cart)) {
            dropdownContent += `
                <div class="cart-item" data-product-id="${productId}">
                    <img src="/img/product-${productId}.jpg" alt="Product Image" class="cart-item-img">
                    <div class="cart-item-info">
                        <p class="cart-item-name">Product ${productId}</p>
                        <p class="cart-item-price">$XX.XX</p>
                        <p class="cart-item-quantity">Cantidad: ${quantity}</p>
                    </div>
                    <button class="remove-from-cart" data-product-id="${productId}">Eliminar</button>
                </div>
            `;
        }

        cartDropdown.innerHTML = dropdownContent;

        // Add event listeners for remove buttons
        const removeButtons = cartDropdown.querySelectorAll('.remove-from-cart');
        removeButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default button behavior
                const productId = this.getAttribute('data-product-id');
                removeFromCart(productId);
            });
        });
    }

    function removeFromCart(productId) {
        let cart = JSON.parse(localStorage.getItem('cart')) || {};
        delete cart[productId];
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        updateCartDropdown();
        
        // If on the cart page, update the display
        if (window.location.pathname.includes('carrito.php')) {
            updateCartDisplay();
        }
    }

    // Initial cart dropdown update
    updateCartDropdown();

    // Cart page functionality
    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');
        if (!cartItemsContainer || !cartTotalElement) return;

        const cart = JSON.parse(localStorage.getItem('cart')) || {};
        let cartContent = '';
        let total = 0;

        for (const [productId, quantity] of Object.entries(cart)) {
            // In a real scenario, you would fetch product details from the server
            // For this example, we'll use placeholder values
            const productName = `Product ${productId}`;
            const productPrice = 10.00; // Placeholder price
            const subtotal = productPrice * quantity;
            total += subtotal;

            cartContent += `
                <div class="cart-item" data-product-id="${productId}">
                    <img src="/img/product-${productId}.jpg" alt="${productName}" class="cart-item-img">
                    <div class="cart-item-info">
                        <h3>${productName}</h3>
                        <p class="cart-item-price">$${productPrice.toFixed(2)}</p>
                        <p class="cart-item-quantity">Cantidad: ${quantity}</p>
                        <p class="cart-item-subtotal">Subtotal: $${subtotal.toFixed(2)}</p>
                    </div>
                    <button class="remove-from-cart" data-product-id="${productId}">Eliminar</button>
                </div>
            `;
        }

        cartItemsContainer.innerHTML = cartContent || '<p>Tu carrito está vacío.</p>';
        cartTotalElement.textContent = total.toFixed(2);

        // Add event listeners for remove buttons
        const removeButtons = cartItemsContainer.querySelectorAll('.remove-from-cart');
        removeButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default button behavior
                const productId = this.getAttribute('data-product-id');
                removeFromCart(productId);
            });
        });
    }

    // If on the cart page, update the display
    if (window.location.pathname.includes('carrito.php')) {
        updateCartDisplay();
    }

    // Categories dropdown functionality
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('a');
        const content = dropdown.querySelector('.dropdown-content');
        
        // For mobile devices, handle click instead of hover
        if (window.innerWidth <= 768) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                content.style.display = content.style.display === 'block' ? 'none' : 'block';
            });
        }
    });

    // Search functionality
    const searchForm = document.querySelector('.search-bar');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = this.querySelector('input').value;
            window.location.href = `busqueda.php?q=${encodeURIComponent(searchTerm)}`;
        });
    }
});