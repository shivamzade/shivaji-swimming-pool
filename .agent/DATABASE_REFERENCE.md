# 📊 DATABASE SCHEMA REFERENCE
## Shivaji Swimming Pool Management System

---

## 🗄️ DATABASE OVERVIEW

**Database Name:** `shivaji_pool`  
**Tables:** 13  
**Views:** 2  
**Triggers:** 2  
**Stored Procedures:** 2

---

## 📋 TABLE SUMMARY

| # | Table Name | Purpose | Critical Indexes | Foreign Keys |
|---|-----------|---------|------------------|--------------|
| 1 | `users` | System users (admin/staff) | role_id, email | - |
| 2 | `roles` | User roles & permissions | - | - |
| 3 | `membership_plans` | Membership plan definitions | is_active | created_by → users |
| 4 | `members` | Pool members | member_code, status, phone, expiry_date | created_by → users |
| 5 | `member_memberships` | Member subscription history | member_id, plan_id, dates | member_id, plan_id, created_by |
| 6 | `attendance` | ⚡ Daily attendance (high-traffic) | **UNIQUE**(member_id, date), date | member_id, marked_by |
| 7 | `payments` | Payment transactions | member_id, receipt, date | member_id, created_by |
| 8 | `staff` | Staff information | status, designation | user_id, created_by |
| 9 | `shifts` | Shift definitions | is_active | - |
| 10 | `shift_assignments` | Staff shift scheduling | staff_id, shift_id, date | staff_id, shift_id |
| 11 | `notifications` | Notification queue | member_id, status | member_id |
| 12 | `audit_logs` | System activity tracking | user_id, action, date | user_id |
| 13 | `settings` | System configuration | setting_key | - |

---

## 🔑 KEY RELATIONSHIPS

```
users (1) ──────────> (N) members [created_by]
                   └──> (N) payments [created_by]
                   └──> (N) staff [created_by]
                   └──> (N) attendance [marked_by]

members (1) ────────> (N) member_memberships
            └───────> (N) attendance
            └───────> (N) payments
            └───────> (N) notifications

membership_plans (1) ──> (N) member_memberships

staff (1) ──────────> (N) shift_assignments
          └─────────> (1) users [optional login]

shifts (1) ─────────> (N) shift_assignments
```

---

## ⚡ CRITICAL PERFORMANCE INDEXES

### Attendance Table (5000+ daily records)
```sql
-- Prevents duplicate entry per day
UNIQUE KEY unique_member_date (member_id, attendance_date)

-- Fast date-based queries
INDEX idx_date (attendance_date)

-- Fast member lookup
INDEX idx_member (member_id)
```

### Members Table
```sql
-- Fast member code search (for QR scanning)
INDEX idx_member_code (member_code)

-- Filter by status
INDEX idx_status (status)

-- Find expiring memberships
INDEX idx_expiry (membership_end_date)
```

---

## 🎯 IMPORTANT BUSINESS RULES

### Attendance
- ✅ One entry per member per day (enforced by UNIQUE constraint)
- ✅ Entry only allowed if membership is active
- ✅ Exit time optional (auto-fill at closing)
- ✅ Duration auto-calculated on exit

### Membership
- Member status auto-updates to 'EXPIRED' when membership_end_date < today
- Member can have multiple membership records (history)
- Only one membership can be 'ACTIVE' at a time

### Payment
- Every payment gets unique receipt number
- Receipt format: RCP-YYYYMMDD-####
- Payment linked to membership renewal

### Member Code Generation
- Format: SPL-YYYY-####
- Example: SPL-2026-0001
- Auto-incremented per year

---

## 📊 VIEWS

### 1. v_active_members
```sql
-- Purpose: Get all members with their current plan and expiry status
-- Columns: All member columns + plan_name, plan_type, days_remaining, membership_status
-- Use Case: Dashboard, member listing, reports
```

### 2. v_today_attendance
```sql
-- Purpose: Real-time attendance summary
-- Columns: total_attendance, exited_count, currently_inside, first_entry, last_entry
-- Use Case: Dashboard widget, attendance monitoring
```

---

## ⚙️ TRIGGERS

### 1. trg_update_member_status
```sql
-- Fires: BEFORE UPDATE on members
-- Purpose: Auto-expire members when membership_end_date < today
```

### 2. trg_calculate_attendance_duration
```sql
-- Fires: BEFORE UPDATE on attendance
-- Purpose: Auto-calculate duration when exit_time is marked
```

---

## 🔧 STORED PROCEDURES

### 1. sp_generate_member_code()
```sql
-- Purpose: Generate next member code (SPL-2026-####)
-- Output: new_code
-- Usage: CALL sp_generate_member_code(@code); SELECT @code;
```

### 2. sp_generate_receipt_number()
```sql
-- Purpose: Generate next receipt number (RCP-20260101-####)
-- Output: new_receipt
-- Usage: CALL sp_generate_receipt_number(@receipt); SELECT @receipt;
```

---

## 📦 DEFAULT DATA INSERTED

### Roles
1. Super Admin (full access)
2. Admin (manager access)
3. Staff (limited access)
4. Member (member portal)

### Default Admin User
- Username: `superadmin`
- Email: `admin@shivajipool.com`
- Password: `Admin@123` ⚠️ CHANGE THIS!
- Role: Super Admin

### Membership Plans
1. Daily Pass - ₹50 (1 day)
2. Weekly Pass - ₹300 (7 days)
3. Monthly - ₹1,000 (30 days)
4. Quarterly - ₹2,700 (90 days, 10% off)
5. Half Yearly - ₹5,100 (180 days, 15% off)
6. Annual - ₹9,600 (365 days, 20% off)

### Shifts
1. Morning Shift (6 AM - 2 PM)
2. Afternoon Shift (2 PM - 10 PM)
3. Full Day (6 AM - 10 PM)

### Settings
- Pool name, capacity, timing
- Member ID prefix (SPL)
- Receipt prefix (RCP)
- Expiry reminder (7 days)
- Timezone, currency

---

## 🔍 COMMON QUERIES

### Get Active Members Count
```sql
SELECT COUNT(*) FROM members WHERE status = 'ACTIVE';
```

### Get Today's Attendance
```sql
SELECT * FROM v_today_attendance;
```

### Members Expiring in 7 Days
```sql
SELECT * FROM members 
WHERE membership_end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
AND status = 'ACTIVE';
```

### Monthly Revenue
```sql
SELECT SUM(amount) as total_revenue 
FROM payments 
WHERE MONTH(payment_date) = MONTH(CURDATE())
AND YEAR(payment_date) = YEAR(CURDATE());
```

### Member Attendance History
```sql
SELECT * FROM attendance 
WHERE member_id = ? 
ORDER BY attendance_date DESC 
LIMIT 30;
```

### Check Duplicate Attendance Today
```sql
SELECT COUNT(*) FROM attendance 
WHERE member_id = ? AND attendance_date = CURDATE();
-- Should return 0 or 1 (enforced by unique constraint)
```

---

## 🛡️ SECURITY FEATURES

### Database Level
- ✅ Foreign key constraints
- ✅ UNIQUE constraints (no duplicates)
- ✅ NOT NULL on critical fields
- ✅ ENUM for controlled values
- ✅ Character set: utf8mb4 (supports all languages)
- ✅ Collation: utf8mb4_unicode_ci

### Application Level (To Implement)
- ⚠️ Use prepared statements (prevent SQL injection)
- ⚠️ Hash passwords with bcrypt/argon2
- ⚠️ Validate input before insert
- ⚠️ Sanitize output (prevent XSS)
- ⚠️ Implement CSRF tokens
- ⚠️ Audit log all critical actions

---

## 📈 SCALABILITY NOTES

### Current Capacity
- ✅ Can handle 5,000+ daily attendance records
- ✅ Optimized indexes for fast queries
- ✅ Proper data types for efficiency

### Future Optimizations (if needed)
- Partition attendance table by year
- Archive old attendance data (>1 year)
- Implement caching layer (Redis)
- Read replicas for reports
- Full-text search for member lookup
- Elasticsearch for advanced analytics

---

## 🔄 MAINTENANCE TASKS

### Daily
- Monitor attendance table growth
- Check error logs
- Verify backup completion

### Weekly
- Run ANALYZE TABLE for optimization
- Review slow query log
- Clean up old notifications (sent > 30 days)

### Monthly
- Archive old attendance records
- Review and optimize indexes
- Update statistics

### Yearly
- Full database backup
- Review and clean audit logs
- Performance review

---

## 📚 REFERENCES

**Documentation:**
- `/database/schema.sql` - Full schema with comments
- `/.agent/PROJECT_ANALYSIS.md` - Project overview
- `/.agent/CHECKLIST.md` - Implementation tasks

**Tools:**
- phpMyAdmin: http://localhost/phpmyadmin
- Database: shivaji_pool
- Charset: utf8mb4

---

**Last Updated:** 2026-01-01
**Schema Version:** 1.0
