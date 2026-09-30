function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);

    fetch('ajax/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('cart-count').textContent = data.cart_count;
            showNotification('Product added to cart!', 'success');
        } else {
            showNotification(data.message || 'Error adding product', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Something went wrong', 'error');
    });
}

function updateCart(productId, quantity) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    fetch('ajax/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    });
}

function removeFromCart(productId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('product_id', productId);

    fetch('ajax/cart_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        }
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem 2rem;
        background: ${type === 'success' ? 'var(--primary)' : 'var(--danger)'};
        color: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        z-index: 9999;
        transition: opacity 0.5s;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
