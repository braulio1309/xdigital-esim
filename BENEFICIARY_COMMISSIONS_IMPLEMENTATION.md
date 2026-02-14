# Beneficiary Commissions Module - Implementation Summary

## Overview

This module allows beneficiaries (partners) to configure their own profit margins on eSIM plans. The margins are applied on top of the admin-configured margins, creating a two-tier pricing system:

1. **Admin Margin**: Set by administrators in `/app-setting?tab=Plan+Margins`
2. **Beneficiary Margin**: Set by each beneficiary for their own clients

## Formula

The pricing follows this formula:
```
API Price → Admin Margin → Beneficiary Margin → Final Price

Admin Price = API Price / (1 - Admin Margin %)
Final Price = Admin Price / (1 - Beneficiary Margin %)
```

### Example
- API Price: $100
- Admin Margin: 30%
- Beneficiary Margin: 10%

Calculation:
1. Admin Price = $100 / (1 - 0.30) = $142.86
2. Final Price = $142.86 / (1 - 0.10) = $158.73

## Architecture

### Backend Components

#### 1. Database Migration
**File**: `database/migrations/2026_02_14_024000_create_beneficiary_plan_margins_table.php`

Creates `beneficiary_plan_margins` table with:
- `id`: Primary key
- `beneficiario_id`: Foreign key to beneficiarios table
- `plan_capacity`: Plan size (1, 3, 5, 10, 20, 50 GB)
- `margin_percentage`: Margin percentage (0-100)
- `is_active`: Boolean flag
- `timestamps`: Created/updated timestamps
- Unique constraint on `(beneficiario_id, plan_capacity)`

#### 2. Model
**File**: `app/Models/App/Settings/BeneficiaryPlanMargin.php`

Features:
- Mass assignment protection
- Automatic casting (decimal for percentage, boolean for is_active)
- Validation in boot method (0-100% range)
- Helper method `getMarginDecimalAttribute()` for converting percentage to decimal
- Relationship with Beneficiario model

#### 3. Service
**File**: `app/Services/App/Settings/BeneficiaryPlanMarginService.php`

Methods:
- `calculateFinalPrice($adminPrice, $planCapacity, $beneficiarioId)`: Applies beneficiary margin on top of admin price
- `getMarginForPlan($planCapacity, $beneficiarioId)`: Gets margin for specific plan and beneficiary
- `getMargins($beneficiarioId)`: Gets all margins for a beneficiary (with 1-hour caching)
- `updateMargins($beneficiarioId, $data)`: Batch update margins
- `getFormattedMargins($beneficiarioId)`: Format margins for API response

Caching:
- Cache key: `beneficiary_plan_margins_{beneficiario_id}`
- Duration: 1 hour (3600 seconds)
- Cleared on updates

#### 4. Controller
**File**: `app/Http/Controllers/App/Settings/BeneficiaryPlanMarginController.php`

Routes:
- `GET /admin/app/beneficiario/plan-margins?beneficiario_id={id}`: Get margins
- `POST /admin/app/beneficiario/plan-margins`: Update margins

#### 5. Form Request
**File**: `app/Http/Requests/App/Settings/BeneficiaryPlanMarginRequest.php`

Validation rules:
- `beneficiario_id`: required, integer, exists in beneficiarios table
- `margins`: required array
- `margins.*.margin_percentage`: required, numeric, 0-100
- `margins.*.is_active`: optional boolean

#### 6. Routes
**File**: `routes/app/beneficiario.php`

Added routes for beneficiary plan margins management.

#### 7. Integration with PlanesDisponiblesController
**File**: `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php`

Modified `getPlanes()` method to:
1. Apply admin margin to API prices
2. Check if authenticated user's cliente has a beneficiario
3. If yes, apply beneficiary margin on top of admin price
4. Return final price to customer

### Frontend Components

#### 1. Vue Component
**File**: `resources/js/app/Components/Views/Settings/BeneficiaryPlanMargins.vue`

Features:
- Modal-based interface
- Shows beneficiary name in title
- Table with all plan capacities (1GB, 3GB, 5GB, 10GB, 20GB, 50GB)
- Input fields for margin percentages (0-100%)
- Real-time example calculations showing how margins affect prices
- Reset to defaults button (sets all to 0%)
- Save and Cancel buttons
- Loading states and error handling

Props:
- `beneficiarioId`: ID of the beneficiary (required)
- `beneficiarioName`: Name for display (optional)

#### 2. Integration with Beneficiarios List
**File**: `resources/js/app/Components/Views/App/Beneficiarios/Index.vue`

Added:
- "Manage Commissions" action button (dollar-sign icon)
- Opens modal when clicked
- Passes beneficiary ID and name to modal

#### 3. Component Registration
**File**: `resources/js/app/AppComponents.js`

Registered `beneficiary-plan-margins-modal` component globally.

#### 4. API Configuration
**File**: `resources/js/app/Config/ApiUrl.js`

Added exports:
- `GET_BENEFICIARY_PLAN_MARGINS`
- `UPDATE_BENEFICIARY_PLAN_MARGINS`

#### 5. Translations
**File**: `resources/lang/en/default.php`

Added translations:
- `beneficiary_plan_margins`: "Comisiones por Planes"
- `beneficiary_profit_margin_configuration`: "Configuración de Comisiones del Beneficiario"
- `configure_beneficiary_margins_explanation`: "Configura tus propios márgenes de ganancia sobre los precios establecidos por el administrador."
- `if_admin_price`: "Si el precio del admin es"
- `confirm_reset_beneficiary_margins`: "¿Está seguro de que desea restablecer todos los márgenes a 0%?"
- `manage_commissions`: "Gestionar Comisiones"

### Model Updates

#### Beneficiario Model
**File**: `app/Models/App/Beneficiario/Beneficiario.php`

Added:
- Relationship `planMargins()` with BeneficiaryPlanMargin model

## Usage

### For Administrators
1. Navigate to **Beneficiarios** in the admin panel
2. Click the **dollar-sign icon** (Gestionar Comisiones) for a beneficiary
3. Modal opens showing all plan capacities
4. Configure margin percentages (0-100%)
5. View real-time examples
6. Click **Save** to apply changes

### For Beneficiaries
When a beneficiary's client views available plans:
1. System fetches product prices from API
2. Admin margin is applied
3. If cliente has a beneficiario, their margin is applied on top
4. Client sees final price with both margins included

### For Customers
No changes to customer experience - they see final prices with all margins included transparently.

## Security Considerations

- Validation ensures margins are between 0 and 100%
- Model-level validation prevents invalid data
- Cache invalidation on updates
- Input sanitization via FormRequest
- Foreign key constraints ensure referential integrity
- Unique constraint prevents duplicate margin configurations

## Performance

- Caching of beneficiary margins for 1 hour
- Cache automatically cleared on updates
- Efficient database queries with proper indexing
- Lazy loading of relationships

## Testing Checklist

Before deployment, verify:

1. **Database**:
   - [ ] Run migration: `php artisan migrate`
   - [ ] Check table structure
   - [ ] Verify foreign key constraints

2. **Backend**:
   - [ ] Test margin calculation formula
   - [ ] Test validation (negative values, > 100%, non-numeric)
   - [ ] Test caching behavior
   - [ ] Test with and without beneficiario

3. **Frontend**:
   - [ ] Build assets: `npm run dev` or `npm run production`
   - [ ] Test modal opening/closing
   - [ ] Test form submission
   - [ ] Test input validation
   - [ ] Test real-time calculations
   - [ ] Test reset to defaults

4. **Integration**:
   - [ ] Test price calculation in PlanesDisponiblesController
   - [ ] Verify prices for clients with beneficiarios
   - [ ] Verify prices for clients without beneficiarios
   - [ ] Test with various margin combinations

5. **Edge Cases**:
   - [ ] Margin = 0%
   - [ ] Margin = 100%
   - [ ] No beneficiario assigned
   - [ ] Beneficiario with no margins configured
   - [ ] Multiple beneficiarios with different margins

## Migration Command

To apply the database changes:

```bash
php artisan migrate
```

## Build Command

To compile frontend assets:

```bash
npm run dev    # For development
npm run prod   # For production
```

## Rollback

If needed to rollback the migration:

```bash
php artisan migrate:rollback
```

This will drop the `beneficiary_plan_margins` table.

## Future Enhancements

Potential improvements:
1. Add beneficiary-specific margin history/audit log
2. Add bulk margin update for multiple beneficiaries
3. Add margin templates for quick setup
4. Add reports showing margin impact on sales
5. Add notifications when margins are changed
6. Add permission-based access control for margin management

## Notes

- Default margin for new beneficiaries is 0% (no additional markup)
- Beneficiaries can set different margins for different plan sizes
- The system gracefully handles missing or inactive margins
- All monetary calculations use 2 decimal precision
- Prices are rounded to 2 decimal places for display
