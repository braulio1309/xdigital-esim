# Transaction Module Updates - Detailed Plan Information

## Overview
This document describes the implementation of enhanced transaction tracking that includes detailed plan information (data amount, duration, and purchase price) for both paid and free eSIM activations.

## Changes Summary

### 1. Database Schema Changes

**Migration**: `2026_02_13_173753_add_plan_details_to_transactions_table.php`

Added the following columns to the `transactions` table:
- `plan_name` (string, nullable) - Name of the purchased plan
- `data_amount` (decimal, nullable) - Amount of data in GB
- `duration_days` (integer, nullable) - Duration of the plan in days
- `purchase_amount` (decimal, nullable) - Purchase amount paid by customer
- `currency` (string, default: 'USD') - Currency code

### 2. Model Updates

**File**: `app/Models/App/Transaction/Transaction.php`

- Added new fields to `$fillable` array to allow mass assignment

### 3. Controller Updates

#### PlanesDisponiblesController
**File**: `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php`

**Modified Methods**:
1. `procesarPago()` - Updated to accept and save plan details when processing paid plans
   - Added validation for: `plan_name`, `data_amount`, `duration`, `purchase_amount`, `currency`
   - Transaction creation now includes all plan details

2. `activarGratis()` - Updated to accept and save plan details for free plans
   - Added validation for: `plan_name`, `data_amount`, `duration`
   - Sets `purchase_amount` to 0 and `currency` to 'USD' for free plans

#### RegistroEsimController
**File**: `app/Http/Controllers/App/Cliente/RegistroEsimController.php`

**Modified Method**:
- `registrarCliente()` - Updated transaction creation to include plan details from the selected product
  - Saves `plan_name`, `data_amount`, `duration_days` from the API response
  - Sets `purchase_amount` to 0 for free registrations

#### TransactionController
**File**: `app/Http/Controllers/App/Transaction/TransactionController.php`

**Modified Method**:
- `index()` - Enhanced with beneficiary filtering
  - Admin users can see all transactions
  - Beneficiary users can only see transactions for their clients
  - Uses `whereHas` to filter through the cliente relationship

### 4. Filter Updates

**File**: `app/Filters/App/Transaction/TransactionFilter.php`

**Enhanced Search Functionality**:
- Search across multiple fields:
  - `transaction_id`
  - `plan_name`
  - Cliente's `nombre`, `apellido`, and `email`

**Added Filter**:
- `status()` - Filter transactions by status

### 5. Frontend Updates

#### Planes Disponibles View
**File**: `resources/views/clientes/planes-disponibles.blade.php`

**Modified Methods**:
1. `processPayment()` - Now sends plan details to backend:
   - `plan_name`
   - `data_amount`
   - `duration`
   - `purchase_amount`
   - `currency`

2. `processFreeActivation()` - Now sends plan details to backend:
   - `plan_name`
   - `data_amount`
   - `duration`

#### Transactions Index Component
**File**: `resources/js/app/Components/Views/App/Transactions/Index.vue`

**Updated Columns** (in logical order):
1. Transaction ID
2. Date (creation_time)
3. Plan (plan_name)
4. Data Amount (with "GB" suffix)
5. Duration (with "días" suffix)
6. Amount (displays "Gratis" badge for free plans, otherwise shows price with $ symbol)
7. Client Name (nombre + apellido)
8. Status
9. Actions

**Enhanced Features**:
- Enabled search functionality (`showSearch: true`)
- Enabled filter functionality (`showFilter: true`)
- Custom HTML formatters for better data display

### 6. Translation Updates

**File**: `resources/lang/en/custom.php`

**Added Translations**:
- `transactions` - Transactions
- `transaction_id` - Transaction ID
- `plan` - Plan
- `data_amount` - Data
- `duration` - Duration
- `amount` - Amount
- `client_name` - Client Name
- `iccid` - ICCID
- `date` - Date

## User Roles and Permissions

### Admin Users
- Can view ALL transactions from all clients and beneficiaries
- Full access to search and filter capabilities
- Can see complete transaction history

### Beneficiary Users
- Can ONLY view transactions for their assigned clients
- Filtering is automatic based on `beneficiario_id` in the cliente relationship
- Same search and filter capabilities within their scope

## Data Flow

### Paid Plan Purchase Flow
1. User selects a plan on `/planes-disponibles`
2. Payment is processed through Stripe
3. Frontend sends plan details along with payment confirmation
4. Backend creates transaction with full plan information
5. Transaction appears in listing with all details

### Free Plan Activation Flow
1. User selects a free plan (or registers via `/registro/esim`)
2. Frontend/backend sends plan details
3. Transaction is created with `purchase_amount = 0`
4. Transaction shows "Gratis" badge in the amount column

### Existing Transaction Records
- Old transactions without plan details will show "N/A" in plan-related columns
- The system is backward compatible and won't break with existing data

## Display Logic

### Amount Column
- If `purchase_amount == 0`: Shows green "Gratis" badge
- Otherwise: Shows formatted price with $ symbol (e.g., "$10.50")

### Data Amount Column
- Shows value with "GB" suffix (e.g., "3 GB")
- Shows "N/A" if no data available

### Duration Column
- Shows value with "días" suffix (e.g., "7 días")
- Shows "N/A" if no data available

### Plan Name Column
- Shows the plan name as received from the API
- Shows "N/A" if no data available

## Search and Filter Capabilities

### Search Functionality
Users can search across:
- Transaction ID
- Plan Name
- Client Name (nombre + apellido)
- Client Email

### Filter Options
- Date range filtering (inherited from DateRangeFilter)
- Status filtering

## Testing Recommendations

### Manual Testing Steps

1. **Test Paid Plan Purchase**:
   - Login as a cliente
   - Go to `/planes-disponibles`
   - Select a country and paid plan
   - Complete the purchase
   - Check transactions list to verify plan details are saved

2. **Test Free Plan Activation**:
   - Login as a cliente
   - Activate a free plan
   - Verify transaction shows "Gratis" badge and plan details

3. **Test Public Registration**:
   - Register a new cliente via `/registro/esim`
   - Verify transaction is created with plan details

4. **Test Admin View**:
   - Login as admin
   - Navigate to transactions
   - Verify all transactions are visible
   - Test search and filter functions

5. **Test Beneficiary View**:
   - Login as beneficiary
   - Navigate to transactions
   - Verify only their clients' transactions are visible
   - Test search and filter functions

### Database Verification

```sql
-- Check migration was applied
SELECT * FROM migrations WHERE migration LIKE '%add_plan_details_to_transactions%';

-- Check new columns exist
DESCRIBE transactions;

-- Check sample transaction data
SELECT id, transaction_id, plan_name, data_amount, duration_days, purchase_amount, currency, cliente_id 
FROM transactions 
ORDER BY created_at DESC 
LIMIT 10;
```

### API Testing

```bash
# Get transactions as admin
curl -X GET http://localhost/app/transactions \
  -H "Authorization: Bearer {admin_token}"

# Get transactions as beneficiary
curl -X GET http://localhost/app/transactions \
  -H "Authorization: Bearer {beneficiary_token}"

# Search transactions
curl -X GET "http://localhost/app/transactions?search=plan_name" \
  -H "Authorization: Bearer {token}"
```

## Migration Instructions

### For Development
```bash
# Run the migration
php artisan migrate

# If needed, rollback and re-run
php artisan migrate:rollback --step=1
php artisan migrate
```

### For Production
```bash
# Always backup first
mysqldump -u username -p database_name > backup.sql

# Run migration
php artisan migrate --force

# Verify the columns were added
php artisan tinker
>>> \Schema::hasColumn('transactions', 'plan_name')
>>> \Schema::hasColumn('transactions', 'data_amount')
>>> \Schema::hasColumn('transactions', 'duration_days')
>>> \Schema::hasColumn('transactions', 'purchase_amount')
>>> \Schema::hasColumn('transactions', 'currency')
```

## Backward Compatibility

The implementation is fully backward compatible:
- Existing transactions without plan details will continue to work
- New fields are nullable, so no data migration is required
- Frontend gracefully handles missing data by showing "N/A"
- No breaking changes to existing functionality

## Future Enhancements

Potential improvements for future iterations:
1. Add export functionality to download transaction reports
2. Implement advanced filtering (by date range, amount range, specific plans)
3. Add transaction statistics and analytics
4. Create email notifications for transactions
5. Add currency conversion for multi-currency support
6. Implement transaction refund functionality

## Files Modified

### Backend
1. `database/migrations/2026_02_13_173753_add_plan_details_to_transactions_table.php` (NEW)
2. `app/Models/App/Transaction/Transaction.php`
3. `app/Http/Controllers/App/Cliente/PlanesDisponiblesController.php`
4. `app/Http/Controllers/App/Cliente/RegistroEsimController.php`
5. `app/Http/Controllers/App/Transaction/TransactionController.php`
6. `app/Filters/App/Transaction/TransactionFilter.php`

### Frontend
7. `resources/js/app/Components/Views/App/Transactions/Index.vue`
8. `resources/views/clientes/planes-disponibles.blade.php`

### Translations
9. `resources/lang/en/custom.php`

## Technical Notes

- All new fields are nullable to ensure backward compatibility
- The `currency` field defaults to 'USD'
- Free plans are identified by `purchase_amount = 0`
- Beneficiary filtering uses Laravel's `whereHas` for efficient querying
- Search uses `orWhere` clauses for flexible searching across multiple fields
- Frontend uses custom HTML modifiers for proper data formatting

## Security Considerations

- Beneficiary users are properly restricted to their own clients' data
- No sensitive payment information is stored in transactions (handled by Stripe)
- Input validation is in place for all new fields
- Authorization checks are performed before displaying transactions

## Performance Considerations

- Eager loading of `cliente` relationship prevents N+1 queries
- Indexed columns (`cliente_id`) ensure efficient filtering
- Pagination is maintained to handle large transaction volumes
- Search queries are optimized with proper use of indexes
