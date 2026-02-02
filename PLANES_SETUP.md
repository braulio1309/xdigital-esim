# Planes Disponibles System - Setup Instructions

## Quick Start Guide

This system allows users to browse and purchase eSIM plans filtered by country (Spain and USA) with Stripe payment integration.

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Stripe Configuration (use test keys for development)
STRIPE_PUBLISHABLE_KEY=pk_test_51xxx...
STRIPE_SECRET_KEY=sk_test_51xxx...

# eSIMfx API Configuration
ESIMFX_BASE_URL=https://api.esimfx.com
ESIMFX_CLIENT_ID=your_client_id_here
ESIMFX_CLIENT_KEY=your_client_key_here
```

### 2. Get Stripe Test Keys

1. Go to https://dashboard.stripe.com/test/apikeys
2. Copy your Publishable key (starts with `pk_test_`)
3. Copy your Secret key (starts with `sk_test_`)
4. Add them to your `.env` file

### 3. Test the System

1. Access: http://your-domain.com/planes-disponibles
2. Select a country (España or USA)
3. View available plans
4. Click "Comprar" on any plan
5. Login or register (if not authenticated)
6. Complete payment with test card:
   - Card: `4242 4242 4242 4242`
   - CVC: Any 3 digits
   - Date: Any future date
7. View QR code and activation data

### 4. Stripe Test Cards

**Successful Payment:**
- Card: `4242 4242 4242 4242`

**Requires Authentication (3D Secure):**
- Card: `4000 0025 0000 3155`

**Card Declined:**
- Card: `4000 0000 0000 9995`

**More test cards:** https://stripe.com/docs/testing

### 5. Features

✅ Country-based plan filtering (ES, US)
✅ Dynamic authentication (login/register without page reload)
✅ Stripe payment integration
✅ Free plans support (bypass payment)
✅ QR code generation for eSIM activation
✅ Manual installation data with copy buttons
✅ Responsive design (mobile-friendly)
✅ Toast notifications for errors
✅ Inline form validation

### 6. System Architecture

```
User Flow:
1. Visit /planes-disponibles
2. Select country → Load plans via AJAX
3. Click "Comprar" → Check authentication
4. Login/Register (if needed) → No page reload
5. View checkout modal:
   - Free plans: Direct activation
   - Paid plans: Stripe payment form
6. Process payment → Create eSIM order
7. Display QR code and activation data
```

### 7. Database Tables Used

- `users` - User accounts
- `clientes` - Customer records
- `transactions` - eSIM purchase transactions

### 8. API Endpoints

**Frontend Routes:**
- `GET /planes-disponibles` - Main page
- `GET /planes/verificar-auth` - Check authentication

**AJAX Endpoints:**
- `POST /planes/get-by-country` - Get plans (ES or US)
- `POST /planes/auth` - Login/register
- `POST /planes/checkout` - Process payment

### 9. Security Features

✅ CSRF protection on all forms
✅ Backend validation for all inputs
✅ Stripe Secret Key never exposed to frontend
✅ XSS prevention with HTML escaping
✅ Event delegation (no inline JS)
✅ Authentication required for purchases

### 10. Troubleshooting

**Plans not loading?**
- Check browser console for errors
- Verify eSIMfx API credentials in .env
- Check `storage/logs/laravel.log`

**Payment failing?**
- Verify Stripe keys are correct
- Use test cards from Stripe docs
- Check Stripe Dashboard logs

**QR code not showing?**
- Verify SimpleSoftwareIO/QrCode is installed
- Check transaction was saved in database
- Review controller error logs

### 11. Testing Checklist

Before deployment, verify:

- [ ] Access /planes-disponibles (page loads)
- [ ] Select "España" (plans load)
- [ ] Select "USA" (plans load)
- [ ] Click "Comprar" without login (modal appears)
- [ ] Login with existing account (modal closes)
- [ ] Register new account (account created)
- [ ] Checkout with free plan (QR generated)
- [ ] Checkout with paid plan (Stripe form shows)
- [ ] Complete payment with test card (success)
- [ ] QR code displays correctly
- [ ] Copy buttons work
- [ ] Test on mobile device
- [ ] Check transaction in database

### 12. Production Deployment

When deploying to production:

1. **Update .env with production Stripe keys:**
   ```env
   STRIPE_PUBLISHABLE_KEY=pk_live_xxx...
   STRIPE_SECRET_KEY=sk_live_xxx...
   ```

2. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

3. **Verify eSIMfx production credentials:**
   ```env
   ESIMFX_CLIENT_ID=production_id
   ESIMFX_CLIENT_KEY=production_key
   ```

4. **Test with real cards** (start with small amounts)

5. **Monitor logs:**
   - Application: `storage/logs/laravel.log`
   - Stripe: https://dashboard.stripe.com/logs

### 13. Support

For detailed documentation, see: `PLANES_DISPONIBLES_DOCS.md`

For issues:
1. Check logs in `storage/logs/`
2. Review Stripe Dashboard
3. Verify environment variables
4. Test with Stripe test cards first

---

**Created by:** Copilot Agent
**Date:** February 2026
**Version:** 1.0
