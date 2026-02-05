# Architecture Analysis & Improvement Plan
**Date:** 2026-02-04  
**Project:** Mini Splitwise - Expense Sharing Application  
**Laravel Version:** 12.0  
**Livewire Version:** 4.1

---

## 📋 EXECUTIVE SUMMARY

This document provides a comprehensive analysis of the current project state and identifies critical gaps that need to be addressed to align with the architectural requirements.

**Current State:** Early skeleton - Models and migrations exist but business logic, services, Livewire components, and proper relationships are missing.

**Critical Priority:** Services layer must be implemented before any UI work to ensure financial correctness.

---

## ✅ WHAT EXISTS (Current State)

### 1. Database Structure
- ✅ All core tables defined in migrations
- ✅ Logging tables (activity_logs, financial_logs, auth_logs)
- ✅ Soft deletes on appropriate tables
- ✅ Unique constraints on pivot tables

### 2. Models
- ✅ All 11 models exist (User, Group, Expense, Balance, Settlement, etc.)
- ✅ Basic fillable attributes defined
- ✅ Soft deletes where needed

### 3. Controllers
- ✅ Resource controllers created (empty stubs)
- ✅ ExpenseController, GroupController, SettlementController

### 4. Dependencies
- ✅ Laravel 12.0
- ✅ Livewire 4.1
- ✅ Tailwind CSS 4.0

---

## ❌ CRITICAL GAPS IDENTIFIED

### 🔴 PRIORITY 1: Services Layer (MISSING)

**Impact:** CRITICAL - Financial correctness depends on this

**Missing Services:**
1. `ExpenseService` - Handle expense creation, updates, deletion
   - Calculate splits
   - Update balances
   - Create financial logs
   - Validate participants vs group members

2. `BalanceService` - Balance management
   - Update balances (single source of truth)
   - Get user balances
   - Validate balance integrity

3. `SettlementService` - Settlement processing
   - Process settlements
   - Update balances after settlement
   - Create financial logs

4. `GroupService` - Group management
   - Add/remove members
   - Validate group operations

5. `LoggingService` - Centralized logging
   - Activity logging
   - Financial logging
   - Auth logging

**Why Critical:**
- Balances table is SINGLE SOURCE OF TRUTH
- UI must NEVER calculate balances
- All balance updates must go through service layer
- Financial audit trail requires service-level logging

---

### 🔴 PRIORITY 2: Model Relationships (INCOMPLETE)

**Current State:** Models have minimal relationships

**Missing Relationships:**

#### User Model
```php
// Missing:
- groups() - belongsToMany through group_users
- expenses() - hasMany (where paid_by = user_id)
- balancesOwed() - hasMany (where from_user_id = user_id)
- balancesOwedTo() - hasMany (where to_user_id = user_id)
- settlements() - hasMany (where paid_from or paid_to = user_id)
- activityLogs() - hasMany
- authLogs() - hasMany
```

#### Group Model
```php
// Missing:
- creator() - belongsTo(User::class, 'created_by')
- groupUsers() - hasMany(GroupUser::class)
- balances() - hasMany(Balance::class)
- settlements() - hasMany(Settlement::class)
```

#### Expense Model
```php
// Missing:
- group() - belongsTo(Group::class)
- paidByUser() - belongsTo(User::class, 'paid_by')
- createdByUser() - belongsTo(User::class, 'created_by')
```

#### Balance Model
```php
// Missing:
- group() - belongsTo(Group::class)
- fromUser() - belongsTo(User::class, 'from_user_id')
- toUser() - belongsTo(User::class, 'to_user_id')
```

#### Settlement Model
```php
// Missing:
- group() - belongsTo(Group::class)
- paidFromUser() - belongsTo(User::class, 'paid_from')
- paidToUser() - belongsTo(User::class, 'paid_to')
- createdByUser() - belongsTo(User::class, 'created_by')
```

#### ExpenseParticipant & ExpenseSplit
```php
// Missing:
- expense() - belongsTo(Expense::class)
- user() - belongsTo(User::class)
```

---

### 🔴 PRIORITY 3: Database Constraints & Indexes (MISSING)

**Foreign Key Constraints:**
- ❌ No foreign keys defined in migrations
- ❌ Risk of orphaned records
- ❌ No referential integrity

**Missing Foreign Keys:**
- `groups.created_by` → `users.id`
- `group_users.group_id` → `groups.id`
- `group_users.user_id` → `users.id`
- `expenses.group_id` → `groups.id`
- `expenses.paid_by` → `users.id`
- `expenses.created_by` → `users.id`
- `expense_participants.expense_id` → `expenses.id`
- `expense_participants.user_id` → `users.id`
- `expense_splits.expense_id` → `expenses.id`
- `expense_splits.user_id` → `users.id`
- `balances.group_id` → `groups.id`
- `balances.from_user_id` → `users.id`
- `balances.to_user_id` → `users.id`
- `settlements.group_id` → `groups.id`
- `settlements.paid_from` → `users.id`
- `settlements.paid_to` → `users.id`
- `settlements.created_by` → `users.id`
- All logging tables foreign keys

**Missing Indexes:**
- `balances`: Index on `(group_id, from_user_id)` and `(group_id, to_user_id)`
- `expenses`: Index on `group_id`, `paid_by`, `created_at`
- `settlements`: Index on `group_id`, `paid_from`, `paid_to`
- `financial_logs`: Index on `group_id`, `related_type`, `related_id`
- `activity_logs`: Index on `user_id`, `module`, `created_at`

---

### 🔴 PRIORITY 4: Livewire Components (MISSING)

**Required Components:**
1. `Groups\Index` - List user's groups
2. `Groups\Show` - Group details with expenses
3. `Groups\Create` - Create new group
4. `Groups\ManageMembers` - Add/remove group members
5. `Expenses\Create` - Create expense with participants
6. `Expenses\Edit` - Edit expense
7. `Expenses\Show` - Expense details
8. `Balances\Index` - Show balances for a group
9. `Settlements\Create` - Create settlement
10. `Dashboard` - User dashboard with summary

**Architecture Rule:**
- Livewire components should be THIN
- All business logic in Services
- Components only handle UI state and call services

---

### 🔴 PRIORITY 5: Observers & Events (MISSING)

**Required Observers:**
1. `ExpenseObserver` - Log activity when expense created/updated/deleted
2. `SettlementObserver` - Log activity when settlement created
3. `GroupObserver` - Log activity when group created/updated
4. `UserObserver` - Log auth events (login, logout, registration)

**Required Events:**
- `ExpenseCreated`, `ExpenseUpdated`, `ExpenseDeleted`
- `SettlementCreated`
- `BalanceUpdated` (for financial logs)

---

### 🔴 PRIORITY 6: Policies (MISSING)

**Required Policies:**
1. `GroupPolicy` - Can user view/edit/delete group?
2. `ExpensePolicy` - Can user view/edit/delete expense?
3. `SettlementPolicy` - Can user create settlement?
4. `BalancePolicy` - Can user view balances?

**Authorization Rules:**
- User can only view groups they're members of
- User can only edit expenses they created (or group admin)
- User can only settle their own balances

---

### 🔴 PRIORITY 7: Model Casts & Attributes (INCOMPLETE)

**Missing Casts:**
- `Expense.total_amount` → `decimal:2`
- `ExpenseSplit.share_amount` → `decimal:2`
- `Balance.amount` → `decimal:2`
- `Settlement.amount` → `decimal:2`
- `FinancialLog.amount`, `balance_before`, `balance_after` → `decimal:2`

**Missing Accessors/Mutators:**
- `Balance` - Accessor to get net balance (from - to)
- `Expense` - Accessor to check if fully split (sum of splits = total)

---

### 🔴 PRIORITY 8: Routes & Middleware (MISSING)

**Current State:** Only welcome route exists

**Required Routes:**
- Auth routes (register, login, logout)
- Group routes (resource)
- Expense routes (nested under groups)
- Settlement routes (nested under groups)
- Balance routes (nested under groups)
- Dashboard route

**Required Middleware:**
- Auth middleware on all protected routes
- Rate limiting for API endpoints

---

### 🔴 PRIORITY 9: NativePHP Integration (MISSING)

**Current State:** Not integrated

**Required:**
- Install NativePHP package
- Configure for mobile/desktop
- Ensure Livewire components work with NativePHP
- Handle offline capabilities

---

### 🔴 PRIORITY 10: Validation & Form Requests (MISSING)

**Required Form Requests:**
1. `StoreExpenseRequest` - Validate expense creation
2. `UpdateExpenseRequest` - Validate expense updates
3. `StoreSettlementRequest` - Validate settlement
4. `StoreGroupRequest` - Validate group creation
5. `UpdateGroupRequest` - Validate group updates

**Validation Rules Needed:**
- Expense participants must be group members
- Split amounts must sum to total_amount
- Settlement amount cannot exceed balance
- Group must have at least 2 members

---

## 🟡 MEDIUM PRIORITY ISSUES

### 1. Database Seeding
- No seeders for testing
- No factories for models

### 2. Testing
- No feature tests
- No unit tests for services

### 3. API Resources
- No API resources for JSON responses
- No API versioning structure

### 4. Exception Handling
- No custom exceptions
- No centralized error handling

### 5. Configuration
- No app-specific config files
- No feature flags

---

## 📐 ARCHITECTURAL VIOLATIONS FOUND

### 1. No Services Layer
**Violation:** Business logic will end up in controllers/Livewire  
**Fix:** Create services layer immediately

### 2. Missing Balance Update Logic
**Violation:** No mechanism to update balances when expenses change  
**Fix:** Implement BalanceService with atomic updates

### 3. No Logging Integration
**Violation:** Logging models exist but nothing writes to them  
**Fix:** Create LoggingService and integrate with observers

### 4. Incomplete Relationships
**Violation:** Models can't navigate relationships  
**Fix:** Add all relationships to models

---

## 🎯 RECOMMENDED IMPLEMENTATION ORDER

### Phase 1: Foundation (CRITICAL)
1. ✅ Add foreign key constraints to migrations
2. ✅ Add indexes to migrations
3. ✅ Complete model relationships
4. ✅ Add model casts
5. ✅ Create Services layer (ExpenseService, BalanceService, SettlementService, GroupService, LoggingService)

### Phase 2: Business Logic (CRITICAL)
6. ✅ Implement ExpenseService with balance updates
7. ✅ Implement BalanceService
8. ✅ Implement SettlementService
9. ✅ Create Observers for logging
10. ✅ Create Policies for authorization

### Phase 3: UI Layer
11. ✅ Create Livewire components
12. ✅ Implement routes
13. ✅ Create form requests
14. ✅ Build views with Alpine.js

### Phase 4: Integration
15. ✅ Integrate NativePHP
16. ✅ Add tests
17. ✅ Add seeders

---

## 🔍 DETAILED FINDINGS BY FILE

### Models Analysis

#### User.php
- ❌ No relationships defined
- ❌ No casts for timestamps (if needed)
- ✅ Fillable attributes correct

#### Group.php
- ✅ Has `users()` relationship (but incomplete - missing pivot)
- ✅ Has `expenses()` relationship
- ❌ Missing `creator()`, `groupUsers()`, `balances()`, `settlements()`
- ❌ No casts

#### Expense.php
- ✅ Has `participants()` and `splits()` relationships
- ❌ Missing `group()`, `paidByUser()`, `createdByUser()`
- ❌ No casts for `total_amount`
- ❌ No validation logic

#### Balance.php
- ❌ No relationships at all
- ❌ No casts for `amount`
- ❌ No accessors for net balance

#### Settlement.php
- ❌ No relationships
- ❌ No casts for `amount`

### Migrations Analysis

#### balances_table
- ✅ Unique constraint on `(group_id, from_user_id, to_user_id)`
- ❌ Missing foreign keys
- ❌ Missing indexes for queries
- ❌ Missing check constraint: `from_user_id != to_user_id`

#### expenses_table
- ✅ Soft deletes
- ❌ Missing foreign keys
- ❌ Missing indexes
- ❌ Missing check constraint: `total_amount > 0`

#### expense_splits_table
- ✅ Unique constraint
- ❌ Missing foreign keys
- ❌ Missing index on `expense_id` for aggregation queries

---

## 💡 SPECIFIC RECOMMENDATIONS

### 1. Balance Service Design

```php
class BalanceService
{
    /**
     * Update balances when expense is created/updated
     * 
     * Flow:
     * 1. Get expense splits
     * 2. For each split (except paid_by):
     *    - Increase balance: paid_by → split_user
     *    - Decrease balance: split_user → paid_by (if exists)
     * 3. Log all changes
     */
    public function updateBalancesFromExpense(Expense $expense): void
    {
        // Implementation
    }
    
    /**
     * Get net balance between two users in a group
     */
    public function getNetBalance(int $groupId, int $userId1, int $userId2): float
    {
        // Implementation
    }
}
```

### 2. Expense Service Design

```php
class ExpenseService
{
    public function __construct(
        private BalanceService $balanceService,
        private LoggingService $loggingService
    ) {}
    
    /**
     * Create expense with participants and splits
     * 
     * Flow:
     * 1. Validate participants are group members
     * 2. Validate splits sum to total_amount
     * 3. Create expense
     * 4. Create participants
     * 5. Create splits
     * 6. Update balances (via BalanceService)
     * 7. Log activity and financial changes
     */
    public function createExpense(array $data): Expense
    {
        // Implementation
    }
}
```

### 3. Migration Improvements Needed

```php
// Example: balances migration should have:
$table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
$table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
$table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
$table->index(['group_id', 'from_user_id']);
$table->index(['group_id', 'to_user_id']);
$table->check('from_user_id != to_user_id');
```

---

## ✅ NEXT STEPS

1. **Review this analysis** with the team
2. **Confirm architectural decisions** before implementation
3. **Start with Phase 1** (Foundation) - migrations and models
4. **Implement Services layer** before any UI work
5. **Test balance calculations** thoroughly before proceeding

---

## 📝 NOTES

- This is a financial application - accuracy is critical
- All balance updates must be atomic (database transactions)
- Logging is mandatory for audit trail
- Services layer ensures business logic is testable and reusable
- Livewire components should be thin - delegate to services

---

**End of Analysis**
