// assets/js/wishlist.js - Wishlist functionality with AJAX

function toggleWishlist(button, productId) {
    event.preventDefault();
    event.stopPropagation();
    
    const isActive = button.classList.contains('active');
    const action = isActive ? 'remove' : 'add';
    
    // Create form data
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', productId);
    
    // Send AJAX request
    fetch('/sweets-shop/wishlist-api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Toggle button state
            button.classList.toggle('active');
            
            // Update wishlist count
            const countElement = document.getElementById('wishlist-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
            
            // Show feedback
            showNotification(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating wishlist', 'error');
    });
}

function showNotification(message, type = 'success') {
    // Create a temporary notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'error' ? '#ff6b6b' : '#51cf66'};
        color: white;
        border-radius: 4px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
