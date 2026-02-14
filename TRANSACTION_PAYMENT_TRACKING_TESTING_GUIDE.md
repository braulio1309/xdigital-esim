# Testing Guide for Transaction Payment Tracking Feature

## Pre-requisites

Before testing, ensure the following steps are completed:

1. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Run Database Migration**
   ```bash
   php artisan migrate
   ```
   This will add the `is_paid` and `paid_at` columns to the transactions table.

3. **Compile Frontend Assets**
   ```bash
   npm run dev
   # or for production
   npm run production
   ```

4. **Ensure Test Data Exists**
   - At least one beneficiary with associated clients
   - Some transactions with purchase_amount = 0 (free eSIMs)
   - Some transactions with purchase_amount > 0 (paid plans)
   - Transactions associated with clients that have beneficiarios

## Test Scenarios

### 1. Transaction List View - Basic Display

**Steps:**
1. Log in as an admin user
2. Navigate to Transactions page (/admin/transactions)

**Expected Results:**
- Table should display with new columns:
  - Purchase Amount (showing dollar amount or "Free" badge)
  - Commission (showing dollar amount and percentage, or just $0.85 for free)
  - Beneficiary (showing beneficiary name)
  - Payment Status (showing "Paid" or "Unpaid" badge)
- Header should show unpaid transactions counter if there are any unpaid free transactions
- Filter buttons should be visible at the top: All/Free/Payment Plans
- Payment status filter buttons should be visible: All Status/Unpaid/Paid
- Admin should see beneficiary dropdown filter
- "Mark as Paid" button should be visible (green button)
- "Add" button should still be visible

### 2. Commission Calculation - Free eSIMs

**Steps:**
1. Find a transaction with purchase_amount = 0

**Expected Results:**
- Purchase Amount column shows green "Free" badge
- Commission column shows exactly "$0.85"
- No percentage shown for free eSIMs

### 3. Commission Calculation - Paid Plans

**Steps:**
1. Find a transaction with purchase_amount > 0
2. Verify the beneficiary has a commission percentage or plan margin set

**Expected Results:**
- Purchase Amount column shows dollar amount (e.g., "$25.00")
- Commission column shows dollar amount AND percentage (e.g., "$5.00 (20%)")
- Commission amount should equal: purchase_amount × (percentage / 100)

**Test Different Commission Scenarios:**
a. Beneficiary with plan-specific margin (in beneficiary_plan_margins table)
   - Commission should use the plan margin percentage

b. Beneficiary with general commission_percentage (in beneficiarios table)
   - Commission should use the general percentage

### 4. Filter by Transaction Type

**Steps:**
1. Click "All" button (should be selected by default)
   - Should show all transactions

2. Click "Free" button
   - Should show only transactions with purchase_amount = 0
   - All visible transactions should show "Free" badge

3. Click "Payment Plans" button
   - Should show only transactions with purchase_amount > 0
   - All visible transactions should show dollar amounts

### 5. Filter by Beneficiary (Admin Only)

**Steps:**
1. Log in as admin
2. Select a beneficiary from the dropdown
3. Verify table updates

**Expected Results:**
- Table should only show transactions where the cliente's beneficiario_id matches selected beneficiary
- Changing selection should update the table
- Selecting "All Beneficiaries" should show all transactions

### 6. Filter by Payment Status

**Steps:**
1. Click "All Status" (default)
   - Should show both paid and unpaid transactions

2. Click "Unpaid"
   - Should show only transactions with is_paid = false
   - All visible transactions should show "Unpaid" badge in orange/warning color

3. Click "Paid"
   - Should show only transactions with is_paid = true
   - All visible transactions should show "Paid" badge in green

### 7. Unpaid Transaction Statistics

**Steps:**
1. Ensure there are some unpaid free eSIM transactions
2. Look at the badge in the header

**Expected Results:**
- Badge should display: "Unpaid Transactions: X ($Y)"
- X should equal the count of transactions where:
  - purchase_amount = 0 (free eSIMs)
  - is_paid = false
- Y should equal X × 0.85

**Example:**
- If there are 10 unpaid free eSIMs
- Badge should show: "Unpaid Transactions: 10 ($8.50)"

### 8. Mark Transactions as Paid - Modal Display

**Steps:**
1. Log in as admin
2. Click the green "Mark as Paid" button

**Expected Results:**
- Modal should open with title "Mark Transactions as Paid"
- Modal should contain:
  - Beneficiary dropdown (populated with all beneficiaries)
  - Start Date field (date picker)
  - End Date field (date picker)
  - Submit and Cancel buttons

### 9. Mark Transactions as Paid - Validation

**Steps:**
1. Open "Mark as Paid" modal
2. Click submit without filling any fields

**Expected Results:**
- Error message should display
- Modal should not close
- No transactions should be updated

### 10. Mark Transactions as Paid - Functionality

**Steps:**
1. Open "Mark as Paid" modal
2. Select a beneficiary
3. Set start date to a date in the past (e.g., 2026-01-01)
4. Set end date to today's date
5. Click submit

**Expected Results:**
- Success message should display
- Modal should close
- Table should refresh
- All free eSIM transactions for that beneficiary within the date range should:
  - Have is_paid = true
  - Have paid_at set to current timestamp
  - Show "Paid" badge in the table
- Unpaid transaction counter should update (decrease)

**Verify in Database:**
```sql
SELECT * FROM transactions 
WHERE purchase_amount = 0 
  AND is_paid = true 
  AND cliente_id IN (
    SELECT id FROM clientes WHERE beneficiario_id = [selected_beneficiary_id]
  )
  AND creation_time BETWEEN '[start_date]' AND '[end_date] 23:59:59';
```

### 11. Beneficiary View - Payment Statistics

**Steps:**
1. Log in as admin
2. Navigate to Beneficiaries page (/admin/beneficiarios or similar)

**Expected Results:**
- Table should have two new columns:
  - "Unpaid Transactions" - showing count with warning badge
  - "Total Owed" - showing dollar amount in red if > 0

**For each beneficiary:**
- Unpaid Transactions count should match the number of free eSIMs for their clients where is_paid = false
- Total Owed should equal: unpaid_count × 0.85
- If no unpaid transactions, should show "0" in green and "$0.00" in green

### 12. Beneficiary User View (Role-Based Access)

**Steps:**
1. Log out as admin
2. Log in as a beneficiary user
3. Navigate to Transactions page

**Expected Results:**
- Should only see transactions for clients assigned to this beneficiary
- Should NOT see beneficiary filter dropdown
- Should NOT see "Mark as Paid" button
- Should see unpaid transaction counter for their own transactions
- Should still see all filter buttons (Free/Paid/All, Paid/Unpaid status)

### 13. Combined Filters

**Steps:**
1. Apply multiple filters at once:
   - Select "Free" transaction type
   - Select "Unpaid" payment status
   - Select a specific beneficiary (if admin)

**Expected Results:**
- Table should show only transactions that match ALL criteria:
  - purchase_amount = 0
  - is_paid = false
  - cliente.beneficiario_id = selected beneficiary

### 14. Date Range Edge Cases for Mark as Paid

**Test Case A: Same Day**
- Start date = End date = today
- Should mark transactions from today only

**Test Case B: Future Dates**
- Start date = tomorrow
- Should find no transactions (assuming no future-dated transactions)

**Test Case C: Long Range**
- Start date = 6 months ago
- End date = today
- Should mark all unpaid free transactions in the 6-month period

## Performance Testing

### Large Dataset Test
1. Ensure database has at least 1000+ transactions
2. Navigate to transactions page
3. Apply various filters

**Expected Results:**
- Page should load within 2-3 seconds
- Filters should respond within 1 second
- Pagination should work smoothly
- No browser console errors

## Regression Testing

### Existing Functionality
1. Verify existing columns still work:
   - Transaction ID
   - Date
   - Plan
   - Data Amount
   - Duration
   - Client Name
   - Status

2. Verify existing actions still work:
   - Edit transaction
   - Delete transaction
   - Search functionality
   - Pagination

3. Verify existing beneficiary features:
   - Add/Edit/Delete beneficiaries
   - Referral link display and copy
   - Manage commissions action

## Troubleshooting

### Issue: Commission shows $0.00 for paid plans
**Cause:** Beneficiary may not have commission_percentage set or plan margins configured
**Solution:** 
- Check beneficiarios table for commission_percentage value
- Check beneficiary_plan_margins table for plan-specific margins
- Set appropriate commission values

### Issue: Filter buttons don't work
**Cause:** Vue event bus may not be properly configured
**Solution:**
- Check browser console for JavaScript errors
- Ensure npm build completed successfully
- Clear browser cache and reload

### Issue: "Mark as Paid" modal doesn't open
**Cause:** User may not have admin role or modal component not loaded
**Solution:**
- Verify user has admin role
- Check browser console for errors
- Ensure MarkAsPaidModal.vue is properly imported

### Issue: Unpaid counter shows wrong amount
**Cause:** Database may have inconsistent data
**Solution:**
- Run this query to verify: 
  ```sql
  SELECT COUNT(*) FROM transactions WHERE purchase_amount = 0 AND is_paid = false;
  ```
- Result should match the counter display

## Security Testing

1. **Authorization Check**
   - Beneficiary users should not be able to mark transactions as paid
   - Beneficiary users should only see their own transactions

2. **Input Validation**
   - Date fields should reject invalid dates
   - Beneficiary dropdown should only accept valid beneficiary IDs
   - SQL injection should not be possible through any filter

3. **CSRF Protection**
   - All POST requests should include CSRF token
   - Laravel's CSRF middleware should be active

## Browser Compatibility

Test in the following browsers:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

## Mobile Responsiveness

Test on different screen sizes:
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

Expected: All features should be usable on all screen sizes, with proper responsive layout.

## Post-Testing Checklist

After successful testing:
- [ ] All test scenarios pass
- [ ] No console errors
- [ ] Database integrity maintained
- [ ] Performance is acceptable
- [ ] Security measures verified
- [ ] Documentation is accurate
- [ ] Ready for production deployment
