// assets/js/app.js
// Simple JS to post add-to-cart forms via fetch if available
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e){
            // If fetch/XHR not supported, allow normal submit
            if (!window.fetch) return;
            e.preventDefault();
            const data = new FormData(form);
            // Prefer server-generated absolute path if provided (window.SS.cartUrl), fallback to relative 'cart.php'
            const target = (window.SS && window.SS.cartUrl) ? window.SS.cartUrl : 'cart.php';
            // use fetch to post
            fetch(target, {
                method: 'POST',
                body: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(js => {
                if (js && js.count !== undefined) {
                    const el = document.getElementById('cart-count');
                    if (el) el.textContent = js.count;
                }
            }).catch(console.error);
        });
    });
});

// Small notification helper (similar to wishlist.js)
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 16px;
        background: ${type === 'error' ? '#ff6b6b' : '#51cf66'};
        color: white;
        border-radius: 6px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 2200);
}

// Add button animation when add-to-cart is used and show notification
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', function(e){
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.classList.add('btn-add-cart-clicked');
                const orig = btn.textContent;
                btn.textContent = '✓ Added!';
                btn.style.backgroundColor = '#27ae60';
                setTimeout(() => {
                    btn.classList.remove('btn-add-cart-clicked');
                    btn.textContent = orig;
                    btn.style.backgroundColor = '';
                }, 1400);
            }
            // Let existing fetch handler proceed (if fetch available). If fetch not supported, submit will navigate.
        });
    });

    // Intercept remove links in cart to perform AJAX remove with animation
    document.querySelectorAll('a[href*="cart.php?action=remove"]').forEach(link => {
        link.addEventListener('click', function(e){
            // Only intercept same-origin local links
            e.preventDefault();
            const href = this.getAttribute('href');
            // parse id from querystring
            const params = new URLSearchParams(href.split('?')[1] || '');
            const id = params.get('id');
            if (!id) return window.location = href; // fallback

            // send AJAX request
            const data = new FormData();
            data.append('action', 'remove');
            data.append('id', id);

            fetch('cart.php', {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(js => {
                if (js && js.count !== undefined) {
                    // update cart count
                    const el = document.getElementById('cart-count');
                    if (el) el.textContent = js.count;
                }
                // remove the table row containing this link for instant feedback
                const row = link.closest('tr');
                if (row) {
                    row.style.transition = 'opacity 0.35s, transform 0.35s';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(-10px)';
                    setTimeout(() => row.remove(), 360);
                }
                showNotification('Removed from cart', 'success');
            }).catch(err => {
                console.error(err);
                showNotification('Could not remove item', 'error');
            });
        });
    });
});