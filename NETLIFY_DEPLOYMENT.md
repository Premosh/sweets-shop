# Netlify Deployment Guide for Sweets Shop

## ⚠️ Important: Netlify Limitations for PHP

Netlify is optimized for **static sites and serverless functions** (JavaScript/TypeScript). Traditional PHP applications like this Sweets Shop are **not ideal** for Netlify because:

1. **No native PHP runtime** — Netlify doesn't run PHP directly (only Node.js, Go, Python for functions)
2. **Session handling** — PHP sessions may not persist reliably in serverless environments
3. **File storage** — The `/data/` directory with `products.json` won't persist across deploys
4. **Payment verification** — eSewa callbacks need a stable backend server

## ✅ Better Alternatives for This App

For traditional PHP apps, use one of these instead:
- **InfinityFree** (free, simple, best for this use case)
- **Railway.app** (free tier available, great PHP support)
- **Render.com** (free tier, PHP + persistent filesystem)
- **Vercel** (supports PHP, but similar serverless limitations)

## 📦 If You Still Want to Deploy on Netlify

### Option A: Deploy Frontend Only (Recommended)

1. Keep your PHP backend on **InfinityFree**, **Railway**, or **Render**
2. Modify your frontend to call the backend API endpoints via fetch()
3. Deploy static assets to Netlify

### Option B: Use Netlify Functions (Advanced)

Convert your PHP routes to Node.js serverless functions:

```
netlify/
  functions/
    cart.js          (handles cart API calls)
    checkout.js      (handles checkout + payment)
    wishlist.js      (handles wishlist API)
    products.js      (serves product data)
```

### Option C: Use a Netlify Build Plugin

Use a community plugin to run PHP during build (experimental, not recommended for production).

---

## 🚀 How to Deploy (Option A: Frontend Only)

### Step 1: Set Up Backend on InfinityFree/Railway

Follow the InfinityFree guide or Railway guide (see main README.md).

### Step 2: Connect Frontend to Backend

Update your API calls in `assets/js/app.js` and `wishlist.php`:

```javascript
// Before:
fetch('cart.php', { method: 'POST', ... })

// After (pointing to your backend):
fetch('https://your-backend.epizy.com/cart.php', { method: 'POST', ... })
```

### Step 3: Deploy to Netlify

```bash
# Install Netlify CLI
npm install -g netlify-cli

# Login to Netlify
netlify login

# Deploy
netlify deploy --prod --dir=public
```

### Step 4: Set Environment Variables

In Netlify Dashboard → Site Settings → Build & Deploy → Environment:

```
BACKEND_URL=https://your-backend.epizy.com
```

---

## 📋 Recommended Approach

**Deploy This App To:**
- **Backend (PHP + Session + File Storage):** InfinityFree, Railway, or Render
- **Frontend (Static Assets + Analytics):** Netlify or Vercel (optional)

This gives you:
✅ Stable PHP backend with sessions  
✅ Persistent file storage for products & orders  
✅ Reliable eSewa payment verification  
✅ Free or very cheap hosting  

---

## 🔧 Environment Variables for Netlify (if using serverless functions)

If you restructure to serverless, set these in Netlify Dashboard:

```
ESEWA_MODE=RC
ESEWA_MERCHANT_ID=your_test_merchant_id
ESEWA_SECRET_KEY=your_test_secret_key
BACKEND_URL=https://your-site.netlify.app
```

---

## ❓ Questions?

See `README.md` for InfinityFree/Railway/Render deployment guides (recommended).
