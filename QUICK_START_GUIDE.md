# Quick Start Guide - Beneficiary Commissions Module

## What was implemented?

A complete commission system that allows beneficiaries (partners) to set their own profit margins on eSIM plans. These margins are added on top of admin margins.

## Files Created/Modified

### New Files (11 total)
**Backend:**
1. `database/migrations/2026_02_14_024000_create_beneficiary_plan_margins_table.php` - Database table
2. `app/Models/App/Settings/BeneficiaryPlanMargin.php` - Data model
3. `app/Services/App/Settings/BeneficiaryPlanMarginService.php` - Business logic
4. `app/Http/Controllers/App/Settings/BeneficiaryPlanMarginController.php` - API endpoints
5. `app/Http/Requests/App/Settings/BeneficiaryPlanMarginRequest.php` - Validation rules

**Frontend:**
6. `resources/js/app/Components/Views/Settings/BeneficiaryPlanMargins.vue` - Modal UI component

**Documentation:**
7. `BENEFICIARY_COMMISSIONS_IMPLEMENTATION.md` - Complete technical documentation
8. `QUICK_START_GUIDE.md` - This file

### Modified Files (6 total)
1. `routes/app/beneficiario.php` - Added API routes
2. `resources/js/app/AppComponents.js` - Registered Vue component
3. `resources/js/app/Config/ApiUrl.js` - Added API endpoint constants
4. `resources/lang/en/default.php` - Added translations
5. `resources/js/app/Components/Views/App/Beneficiarios/Index.vue` - Added "Manage Commissions" button
6. `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php` - Apply beneficiary margins to prices
7. `app/Models/App/Beneficiario/Beneficiario.php` - Added relationship

## Deployment Steps

### 1. Run Database Migration
```bash
cd /path/to/xdigital-esim
php artisan migrate
```

This creates the `beneficiary_plan_margins` table.

### 2. Build Frontend Assets
```bash
npm run dev    # For development
# OR
npm run prod   # For production
```

This compiles the Vue component and makes it available in the UI.

### 3. Clear Caches (Optional but recommended)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## How to Use

### As an Administrator

1. **Access Beneficiarios List**
   - Navigate to the admin panel
   - Go to **Beneficiarios** section

2. **Manage Commissions**
   - Find the beneficiary you want to configure
   - Click the **dollar-sign icon** (💵) in the Actions column
   - A modal will open titled "Plan Commissions - [Beneficiary Name]"

3. **Configure Margins**
   - You'll see a table with 6 plan sizes: 1GB, 3GB, 5GB, 10GB, 20GB, 50GB
   - Enter margin percentages (0-100%) for each plan
   - Example calculations show in real-time
   - Click **Save** to apply changes

4. **Reset to Defaults**
   - Click **Reset to Defaults** button to set all margins to 0%
   - Confirm when prompted

### As a Beneficiary (if accessing their own panel)

The same process applies if beneficiaries have access to manage their own commissions.

## How It Works

### Pricing Formula

```
Step 1: API Price → Apply Admin Margin → Admin Price
Step 2: Admin Price → Apply Beneficiary Margin → Final Price
```

**Example:**
- API returns price: $100
- Admin sets 30% margin
- Admin Price = $100 / (1 - 0.30) = $142.86

- Beneficiary sets 10% margin  
- Final Price = $142.86 / (1 - 0.10) = $158.73

**The customer sees:** $158.73

### When Margins Apply

- **Admin margins**: Always applied to all customers
- **Beneficiary margins**: Only applied to customers who have a `beneficiario_id` assigned

### Default Behavior

- New beneficiaries have 0% margins by default (no additional markup)
- If no margin is configured, the system uses 0%
- If a plan's margin is set to 0%, only admin margin applies

## Testing Checklist

After deployment, verify:

- [ ] Migration ran successfully (check database for `beneficiary_plan_margins` table)
- [ ] Frontend compiled without errors
- [ ] "Manage Commissions" button appears in Beneficiarios list
- [ ] Modal opens when clicking the button
- [ ] Can enter margin percentages
- [ ] Can save changes
- [ ] Changes persist after page reload
- [ ] Prices in "Planes Disponibles" reflect beneficiary margins for assigned clients
- [ ] Prices remain unchanged for clients without beneficiarios

## Troubleshooting

### Modal doesn't open
- Clear browser cache
- Check browser console for JavaScript errors
- Verify frontend assets were compiled: `npm run dev`

### Margins not saving
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database migration ran
- Check API endpoints are accessible

### Prices not changing
- Clear application cache: `php artisan cache:clear`
- Check that cliente has `beneficiario_id` set
- Verify margins are configured (not 0%)

### Frontend errors
- Rebuild assets: `npm run dev`
- Clear browser cache
- Check for JavaScript console errors

## API Endpoints

### Get Beneficiary Margins
```
GET /admin/app/beneficiario/plan-margins?beneficiario_id={id}
```

Response:
```json
{
  "margins": {
    "1": { "margin_percentage": 0.00, "is_active": true },
    "3": { "margin_percentage": 5.00, "is_active": true },
    "5": { "margin_percentage": 10.00, "is_active": true },
    ...
  }
}
```

### Update Beneficiary Margins
```
POST /admin/app/beneficiario/plan-margins
```

Body:
```json
{
  "beneficiario_id": 1,
  "margins": {
    "1": { "margin_percentage": 5.00, "is_active": true },
    "3": { "margin_percentage": 10.00, "is_active": true },
    ...
  }
}
```

## Security Notes

- Validation ensures margins are 0-100%
- Only admins can access beneficiary margin management
- Database constraints prevent invalid data
- Foreign keys ensure referential integrity
- Caching improves performance (1-hour cache, auto-cleared on updates)

## Need More Information?

See `BENEFICIARY_COMMISSIONS_IMPLEMENTATION.md` for complete technical documentation including:
- Detailed architecture
- Code examples
- Database schema
- Testing procedures
- Future enhancement ideas

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console for JavaScript errors
3. Verify all deployment steps were completed
4. Review the troubleshooting section above
