// Cart functionality
const Cart = {
    items: [],

    init() {
        this.load();
        this.updateUI();
    },

    load() {
        const saved = localStorage.getItem('arimbi_cart');
        this.items = saved ? JSON.parse(saved) : [];
    },

    save() {
        localStorage.setItem('arimbi_cart', JSON.stringify(this.items));
    },

    add(product) {
        const existing = this.items.find(item =>
            item.id === product.id && item.type === product.type
        );

        if (existing) {
            existing.qty += 1;
        } else {
            this.items.push({
                id: product.id,
                type: product.type, // 'product' or 'bundle'
                name: product.name,
                price: product.price,
                image: product.image,
                qty: 1
            });
        }

        this.save();
        this.updateUI();
        this.showNotification('Ditambahkan ke keranjang!');
    },

    remove(id, type) {
        this.items = this.items.filter(item => !(item.id === id && item.type === type));
        this.save();
        this.updateUI();
    },

    updateQty(id, type, qty) {
        const item = this.items.find(item => item.id === id && item.type === type);
        if (item) {
            item.qty = Math.max(1, qty);
            this.save();
            this.updateUI();
        }
    },

    getTotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },

    getCount() {
        return this.items.reduce((sum, item) => sum + item.qty, 0);
    },

    clear() {
        this.items = [];
        this.save();
        this.updateUI();
    },

    updateUI() {
        // Update cart bar
        const cartBar = document.getElementById('cartBar');
        const cartTotal = document.getElementById('cartTotal');
        const cartTotalSidebar = document.getElementById('cartTotalSidebar');

        if (this.items.length > 0) {
            cartBar.style.display = 'block';
            const total = this.getTotal();
            const formatted = 'Rp ' + total.toLocaleString('id-ID');
            cartTotal.textContent = formatted;
            if (cartTotalSidebar) cartTotalSidebar.textContent = formatted;
        } else {
            cartBar.style.display = 'none';
        }

        // Update cart items in sidebar
        const cartItemsEl = document.getElementById('cartItems');
        if (cartItemsEl) {
            if (this.items.length === 0) {
                cartItemsEl.innerHTML = '<div class="empty-state"><div class="icon">🛒</div><p>Keranjang kosong</p></div>';
            } else {
                cartItemsEl.innerHTML = this.items.map(item => `
                    <div class="cart-item">
                        <div class="cart-item-image">
                            ${item.image ? `<img src="${item.image}" alt="${item.name}">` : ''}
                        </div>
                        <div class="cart-item-info">
                            <h5>${item.name}</h5>
                            <p class="price">Rp ${item.price.toLocaleString('id-ID')}</p>
                        </div>
                        <div class="cart-item-qty">
                            <button onclick="Cart.updateQty(${item.id}, '${item.type}', ${item.qty - 1})">-</button>
                            <span>${item.qty}</span>
                            <button onclick="Cart.updateQty(${item.id}, '${item.type}', ${item.qty + 1})">+</button>
                            <button onclick="Cart.remove(${item.id}, '${item.type}')" style="color: #ef4444; border-color: #fecaca;">✕</button>
                        </div>
                    </div>
                `).join('');
            }
        }
    },

    showNotification(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 2000);
    }
};

// Cart sidebar functions
function openCart() {
    document.getElementById('cartOverlay').classList.add('active');
    document.getElementById('cartSidebar').classList.add('active');
}

function closeCart() {
    document.getElementById('cartOverlay').classList.remove('active');
    document.getElementById('cartSidebar').classList.remove('active');
}

// Add to cart function for buttons
function addToCart(id, type, name, price, image) {
    Cart.add({ id, type, name, price, image });
}

// Checkout Modal Functions
function checkout() {
    if (Cart.items.length === 0) {
        alert('Keranjang kosong!');
        return;
    }
    closeCart();
    document.getElementById('checkoutModal').classList.add('active');
}

function closeCheckoutModal() {
    document.getElementById('checkoutModal').classList.remove('active');
}

function toggleDropship() {
    const isDropship = document.getElementById('isDropship').checked;
    document.getElementById('dropshipFields').style.display = isDropship ? 'block' : 'none';
}

function processCheckout() {
    const buyerName = document.getElementById('buyerName').value;
    const buyerPhone = document.getElementById('buyerPhone').value;
    const isDropship = document.getElementById('isDropship').checked;
    const address = document.getElementById('deliveryAddress').value;
    const note = document.getElementById('deliveryNote').value;

    if (!buyerName || !buyerPhone || !address) {
        alert('Mohon lengkapi Nama, No. WhatsApp, dan Alamat Pengiriman!');
        return;
    }

    let receiverName = buyerName;
    let receiverPhone = buyerPhone;

    if (isDropship) {
        receiverName = document.getElementById('receiverName').value;
        receiverPhone = document.getElementById('receiverPhone').value;

        if (!receiverName || !receiverPhone) {
            alert('Mohon lengkapi Data Penerima (Dropship)!');
            return;
        }
    }

    // Build Message
    let message = `Halo kak! Aku mau checkout, ini detailnya ya:\n\n`;

    Cart.items.forEach((item, index) => {
        message += `- ${item.name} (${item.qty}x) = Rp ${(item.price * item.qty).toLocaleString('id-ID')}\n`;
    });

    message += `\nTotal: Rp ${Cart.getTotal().toLocaleString('id-ID')}\n`;
    message += `\nPesanan Atas nama: ${buyerName}\n`;
    message += `Nomor Telp: ${buyerPhone}\n`;

    message += `\nPengiriman ke:\n`;
    message += `Nama Penerima: ${receiverName}\n`;
    message += `Nomor Telp: ${receiverPhone}\n`;
    message += `Alamat: ${address}\n`;

    if (note) {
        message += `Catatan: ${note}\n`;
    }

    message += `\nTerima Kasih`;

    // Send to WhatsApp
    const phone = typeof WA_NUMBER !== 'undefined' ? WA_NUMBER : '6281234567890';
    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');

    // Optional: Close modal after sending
    // closeCheckoutModal();
    // Cart.clear(); // Uncomment if you want to clear cart after checkout
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', () => {
    Cart.init();
});

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
`;
document.head.appendChild(style);
