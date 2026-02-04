# Plan Margin Configuration System

## Overview

This system allows administrators to configure profit margins for eSIM plans (1GB, 3GB, 5GB, 10GB, 20GB, 50GB) and automatically calculate final prices based on the configured margins.

## Price Calculation Formula

```
Final Price = Cost / (1 - Margin)
```

### Example
If the cost is $100 and the margin is 30% (0.30):
```
Final Price = 100 / (1 - 0.30) = 100 / 0.70 = 142.86
Profit = 142.86 - 100 = 42.86 (which is 30% of 142.86)
```

## Architecture

### Backend Components

1. **Database Migration**: `database/migrations/2026_02_04_190000_create_plan_margins_table.php`
   - Creates `plan_margins` table with fields: `id`, `plan_capacity`, `margin_percentage`, `is_active`, `timestamps`

2. **Database Seeder**: `database/seeders/PlanMarginSeeder.php`
   - Seeds default 30% margins for all plan capacities

3. **Model**: `app/Models/App/Settings/PlanMargin.php`
   - Handles margin configuration with validation (0-100%)
   - Includes auto-casting and validation in boot method

4. **Service**: `app/Services/App/Settings/PlanMarginService.php`
   - `calculateFinalPrice($cost, $planCapacity)`: Applies margin formula
   - `updateMargins($data)`: Batch update margins
   - `getMargins()`: Get all active margins (with caching)
   - Implements caching for 1 hour (3600 seconds)

5. **Controller**: `app/Http/Controllers/App/Settings/PlanMarginController.php`
   - `index()`: GET `/admin/app/settings/plan-margins` - List margins
   - `update()`: POST `/admin/app/settings/plan-margins` - Update margins

6. **FormRequest**: `app/Http/Requests/App/Settings/PlanMarginRequest.php`
   - Validates margin data (array of margins with percentage 0-100)

7. **Routes**: `routes/core/app.php`
   - Admin-only routes for plan margin management

8. **Integration**: `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php`
   - Modified `getPlanes()` method to apply margins to product prices
   - Adds `original_price`, `price` (with margin), and `margin_applied` fields

### Frontend Components

1. **Vue Component**: `resources/js/app/Components/Views/Settings/PlanMargins.vue`
   - Interactive table for editing margins
   - Real-time example calculations
   - Reset to defaults functionality
   - Form validation and error handling

2. **Vue Registration**: `resources/js/app/AppComponents.js`
   - Registers `plan-margins` component

3. **Settings Integration**: `resources/js/app/Components/Views/Settings/Index.vue`
   - Adds "Plan Margins" tab to Settings page

4. **API Configuration**: `resources/js/app/Config/ApiUrl.js`
   - Exports `GET_PLAN_MARGINS` and `UPDATE_PLAN_MARGINS` endpoints

5. **Translations**: `resources/lang/en/default.php`
   - English translations for all UI elements

## Usage

### For Administrators

1. Navigate to **Settings → Plan Margins** in the admin panel
2. Configure the profit margin percentage for each plan (0-100%)
3. View real-time calculation examples
4. Click **Save** to apply changes
5. Use **Reset to Defaults** to restore all margins to 30%

### For Customers

When customers view available plans:
1. The system fetches product prices from the external API
2. The configured margin is automatically applied using the formula
3. Customers see the final price (with margin included)
4. Original prices are stored but not displayed

## Security

- Admin-only access enforced at controller level
- Validation ensures margins are between 0 and 100%
- Model-level validation prevents invalid data
- Cache invalidation on updates
- Input sanitization via FormRequest

## Testing

Run the test suite:
```bash
php artisan test --filter=PlanMarginServiceTest
```

### Test Coverage
- Price calculation with various margins (0%, 25%, 30%, 100%)
- Edge case handling (no margin, zero margin, 100% margin)
- Multiple margin updates
- Validation for out-of-range values

## API Endpoints

### GET /admin/app/settings/plan-margins
Returns current margin configuration:
```json
{
  "margins": {
    "1": { "margin_percentage": 30.00, "is_active": true },
    "3": { "margin_percentage": 30.00, "is_active": true },
    ...
  }
}
```

### POST /admin/app/settings/plan-margins
Updates margin configuration:
```json
{
  "margins": {
    "1": { "margin_percentage": 25.00, "is_active": true },
    "3": { "margin_percentage": 35.00, "is_active": true }
  }
}
```

## Caching

The system uses Laravel's caching mechanism:
- Cache Key: `plan_margins_config`
- Duration: 1 hour (3600 seconds)
- Automatically cleared on updates

## Database Setup

Run migrations and seed:
```bash
php artisan migrate
php artisan db:seed --class=PlanMarginSeeder
```

## Troubleshooting

### Margins not applying to prices
1. Check that margins are configured in the database
2. Verify cache is not stale: `php artisan cache:clear`
3. Ensure `PlanMarginService` is injected in `PlanesDisponiblesController`

### Vue component not showing
1. Rebuild frontend assets: `npm run dev` or `npm run production`
2. Clear browser cache
3. Check browser console for errors

### Permission errors
1. Verify user has admin role (`is_admin = 1`)
2. Check middleware configuration
3. Review `isAppAdmin()` method implementation
