# Plan Margin Configuration System

## Overview
This feature implements a comprehensive profit margin configuration system for eSIM plans (1GB, 3GB, 5GB, 10GB, 20GB, 50GB). Administrators can define individual margin percentages for each plan, and the system automatically calculates final prices using a standard pricing formula.

## Pricing Formula
```
Final Price = Cost / (1 - Margin)
```

### Example
- Cost from API: $100
- Margin: 30% (0.30)
- Final Price: $100 / (1 - 0.30) = $100 / 0.70 = $142.86
- Profit: $142.86 - $100 = $42.86

## Features

### Backend
- **Database Table**: `plan_margins` stores margin configurations
- **Automatic Seeding**: Default 30% margin for all plans
- **Service Layer**: Handles business logic and caching
- **Admin-Only Access**: Restricted to users with `isAppAdmin()` permission
- **API Endpoints**:
  - `GET /admin/app/settings/plan-margins` - List all margins
  - `POST /admin/app/settings/plan-margins` - Update margins
- **Price Integration**: Automatically applies margins in `PlanesDisponiblesController`

### Frontend
- **Vue Component**: `PlanMargins.vue` provides intuitive UI
- **Settings Tab**: Integrated into main Settings page
- **Real-time Calculation Examples**: Shows expected final prices
- **Validation**: Ensures margins are between 0-100%
- **Multi-language Support**: Translations for Spanish

## Architecture

### Files Created/Modified

#### Backend
1. **Migration**: `database/migrations/2026_02_04_190000_create_plan_margins_table.php`
2. **Seeder**: `database/seeders/App/PlanMarginSeeder.php`
3. **Model**: `app/Models/App/Settings/PlanMargin.php`
4. **Service**: `app/Services/App/Settings/PlanMarginService.php`
5. **Controller**: `app/Http/Controllers/App/Settings/PlanMarginController.php`
6. **Request**: `app/Http/Requests/App/Settings/PlanMarginRequest.php`
7. **Routes**: `routes/core/app.php` (added plan-margins endpoints)
8. **Integration**: `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php` (modified to apply margins)

#### Frontend
1. **Component**: `resources/js/app/Components/Views/Settings/PlanMargins.vue`
2. **Registration**: `resources/js/app/AppComponents.js` (registered component)
3. **Settings**: `resources/js/app/Components/Views/Settings/Index.vue` (added tab)
4. **Translations**: `resources/lang/en/custom.php` (added translations)

#### Tests
1. **Unit Tests**: `tests/Unit/Services/PlanMarginServiceTest.php`

## Usage

### For Administrators

1. **Access Settings**
   - Navigate to Settings → Plan Margins

2. **Configure Margins**
   - Enter margin percentages (0-100%) for each plan capacity
   - See live calculation examples
   - Click "Save" to apply changes

3. **View Results**
   - Margins are automatically applied to all plan prices
   - Original prices are preserved
   - Final prices are displayed to customers

### API Usage

#### Get Current Margins
```bash
GET /admin/app/settings/plan-margins
```

**Response:**
```json
{
  "data": {
    "1": {
      "id": 1,
      "plan_capacity": "1",
      "margin_percentage": 30.00,
      "is_active": true
    },
    "3": {
      "id": 2,
      "plan_capacity": "3",
      "margin_percentage": 30.00,
      "is_active": true
    }
    // ... more plans
  }
}
```

#### Update Margins
```bash
POST /admin/app/settings/plan-margins
Content-Type: application/json

{
  "margins": [
    {
      "plan_capacity": "1",
      "margin_percentage": 25.00,
      "is_active": true
    },
    {
      "plan_capacity": "3",
      "margin_percentage": 30.00,
      "is_active": true
    }
    // ... more plans
  ]
}
```

**Response:**
```json
{
  "message": "Plan margins updated successfully.",
  "data": {
    // Updated margins
  }
}
```

## Database Schema

```sql
CREATE TABLE `plan_margins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plan_capacity` varchar(255) NOT NULL COMMENT 'Plan capacity in GB: 1, 3, 5, 10, 20, 50',
  `margin_percentage` decimal(5,2) NOT NULL COMMENT 'Profit margin percentage (0-100)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plan_margins_plan_capacity_unique` (`plan_capacity`)
);
```

## Caching
The system uses Laravel's caching mechanism to optimize performance:
- Cache key: `plan_margins`
- Cache duration: 1 hour (3600 seconds)
- Cache is automatically cleared when margins are updated

## Security
- **Admin-Only Access**: Enforced at multiple levels
  - FormRequest authorization
  - Controller middleware
  - Frontend permission checks
- **Input Validation**: All margins validated between 0-100%
- **SQL Injection Prevention**: Uses Eloquent ORM
- **XSS Prevention**: Vue.js automatic escaping

## Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run only plan margin tests
php artisan test --filter PlanMarginServiceTest
```

### Test Coverage
- ✅ Margin calculation with various percentages
- ✅ Edge case handling (0%, 100%)
- ✅ No margin configured scenario
- ✅ Update functionality
- ✅ Formatted output

## Installation

1. **Run Migrations**
   ```bash
   php artisan migrate
   ```

2. **Seed Default Data**
   ```bash
   php artisan db:seed --class=PlanMarginSeeder
   ```
   
   Or run all seeders:
   ```bash
   php artisan db:seed
   ```

3. **Build Frontend Assets**
   ```bash
   npm install
   npm run dev
   # or for production
   npm run production
   ```

4. **Clear Cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

## Troubleshooting

### Issue: Margins not applying to prices
**Solution**: Clear cache and ensure `PlanMarginService` is properly injected

### Issue: "Unauthorized" error
**Solution**: Ensure user has admin role (`is_admin = 1` in roles table)

### Issue: Frontend component not showing
**Solution**: 
1. Check if component is registered in `AppComponents.js`
2. Rebuild frontend assets: `npm run dev`
3. Clear browser cache

## Future Enhancements
- [ ] Historical margin tracking
- [ ] Bulk import/export of margins
- [ ] Margin change notifications
- [ ] Role-based margin viewing permissions
- [ ] Margin analytics dashboard

## Support
For issues or questions, please contact the development team or create an issue in the repository.
