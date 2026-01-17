# शिवाजी स्विमिंग पूल मॅनेजमेंट सिस्टम — वापरकर्ता मार्गदर्शिका (मराठी)

## 1) परिचय
ही प्रणाली **Shivaji Swimming Pool** साठी सदस्य नोंदणी, मेंबरशिप प्लॅन, नूतनीकरण/पेमेंट, दैनिक उपस्थिती (Attendance) आणि रिपोर्टिंग व्यवस्थापनासाठी तयार केली आहे.

- **Admin Panel (स्टाफ/अ‍ॅडमिन लॉगिन)**: `.../shivaji_pool/admin/index.php`
- **Attendance Kiosk (Member Self-Service + Reception PIN)**: `.../shivaji_pool/mark_attendance.php`

> टीप: प्रोजेक्टच्या root `index.php` मध्ये “hello world” आहे; प्रत्यक्ष कार्यरत भाग **admin panel** आणि **mark_attendance** पेजेसमध्ये आहे.

---

## 2) भूमिका (Roles) आणि अधिकार
सिस्टममध्ये मुख्यतः खालील भूमिका असतात:

- **Super Admin**
  - सर्व सेटिंग्ज बदलणे, स्टाफ/अ‍ॅडमिन व्यवस्थापन, सर्व रिपोर्ट्स.
- **Admin**
  - मेंबर्स/Attendance/Payments/Reports (काही मर्यादा).
- **Staff**
  - मुख्यतः दैनिक ऑपरेशन्स: Attendance mark करणे, payments/renewal करणे (परवानगीप्रमाणे).

> Roles database मध्ये `roles` टेबलमध्ये ठेवले आहेत.

---

## 3) सिस्टम सेटअप (संक्षिप्त)
जर तुम्हाला लोकल XAMPP वर सिस्टम रन करायची असेल:

- **Project Path**: `c:/xampp_old/htdocs/shivaji_pool`
- **Database**: `shivaji_pool`
- **DB schema**: `database/schema.sql`
- **DB connection**: `db_connect.php`
- **Base URL config**: `config/config.php`

### 3.1) Default Admin लॉगिन
`database/schema.sql` मध्ये default super admin:
- Username: `superadmin`
- Password: `Admin@123`

**पहिल्या लॉगिननंतर पासवर्ड बदलणे अत्यंत आवश्यक आहे.**

---

## 4) Admin Panel मध्ये लॉगिन/लॉगआउट
### 4.1) लॉगिन
1. ब्राउझरमध्ये उघडा: `.../shivaji_pool/admin/index.php`
2. `Username or Email` आणि `Password` टाका.
3. गरज असल्यास `Remember me` टिक करा.
4. `Sign In` क्लिक करा.

**सामान्य त्रुटी संदेश**
- `Invalid username or password`
- `Too many login attempts...` (जास्त वेळा चुकीचा पासवर्ड दिल्यास lock होऊ शकते)

### 4.2) लॉगआउट
- Admin Panel मध्ये वरच्या/साइड मेनूमधील `Logout` क्लिक करा.

---

## 5) Dashboard (Admin Home)
Dashboard मध्ये मुख्य आकडेवारी दिसते:
- **Today’s Attendance**
- **Currently Inside**
- **Active Members / Total Members**
- **This Month Revenue**
- **Expiring Soon / Expired Members**

याचा उपयोग रोजचे ऑपरेशन आणि pending renewals पटकन ओळखण्यासाठी करा.

---

## 6) Members Module (मेंबर व्यवस्थापन)
मेनू: **Members**

### 6.1) All Members (सर्व सदस्य)
पथ: `admin/admin_panel/members/index.php`

- Search बॉक्स मध्ये: `Member Code`, नाव, किंवा फोनने शोध.
- Status filter: `ACTIVE / EXPIRED / SUSPENDED / INACTIVE`.
- Actions:
  - **View** (डोळा आयकॉन)
  - **Edit** (पेन्सिल आयकॉन)
  - **Renew** (फक्त expired किंवा plan नसल्यास)

### 6.2) Add New Member (नवीन सदस्य नोंदणी)
पथ: `admin/admin_panel/members/add.php`

1. **Personal Information**
   - First Name, Last Name, Gender, Date of Birth (Required)
   - Blood Group, Medical Conditions (Optional)
2. **Contact Information**
   - Phone (Required, 10-digit)
   - Alternate Phone, Email (Optional)
3. **Address** (Optional)
4. **Identity Proof** (Optional)
5. **Emergency Contact** (Optional)
6. `Register Member` क्लिक करा.

नोंदणी झाल्यावर:
- सिस्टम auto **Member Code** तयार करते (उदा. `SPL-2026-0001`).
- नंतर त्या सदस्याला **Membership Plan assign/renew** करणे आवश्यक आहे.

### 6.3) View Member Profile (मेंबर प्रोफाइल)
पथ: `admin/admin_panel/members/view.php?id=...`

येथे दिसते:
- Member status (ACTIVE/EXPIRED)
- Membership plan details (plan name/type, start-end)
- Tabs:
  - **Information** (संपूर्ण माहिती)
  - **Attendance** (शेवटच्या 10 visits)
  - **Payments** (शेवटचे 10 payments)

Quick actions:
- **Edit**
- **Renew**
- **Print** (फॉर्म/रेकॉर्ड)

### 6.4) Edit Member (मेंबर माहिती अपडेट)
पथ: `admin/admin_panel/members/edit.php?id=...`

- बदल करून `Update Details` क्लिक करा.

### 6.5) Print Form
पथ:
- Blank Form: `admin/admin_panel/members/print_form.php`
- Filled Form: `admin/admin_panel/members/print_form.php?id=...`

हे registration फॉर्म प्रिंट/रेकॉर्डसाठी उपयोगी आहे.

---

## 7) Membership Plans (प्लॅन व्यवस्थापन)
मेनू: **Membership Plans**

### 7.1) Plans List
पथ: `admin/admin_panel/plans/index.php`

- सर्व प्लॅनची यादी, price, duration, active/inactive.

### 7.2) Add Plan
पथ: `admin/admin_panel/plans/add.php`

1. Plan Name
2. Plan Type (Daily/Weekly/Monthly/Quarterly/HalfYearly/Yearly)
3. Price
4. Duration (Days)
5. Active status
6. `Save Plan`

### 7.3) Edit Plan
पथ: `admin/admin_panel/plans/edit.php?id=...`

- बदल करून `Update Plan`.

---

## 8) Membership Renewal + Payment (नूतनीकरण व फी)
### 8.1) Renew/Assign Plan
पथ: `admin/admin_panel/members/renew.php?id=...`

1. सदस्य निवडा (Member profile मधून `Renew` क्लिक)
2. `Select Plan` मधून प्लॅन निवडा
3. `Start Date` तपासा/निवडा
   - जर आधीची membership चालू असेल तर default start = expiry नंतरचा दिवस
4. `Payment Mode` निवडा (Cash/UPI/Card/Bank Transfer)
5. `Confirm Renewal & Payment`

यामुळे:
- Payment record तयार होतो (Receipt no. generate)
- Member ला membership assign होते
- `members.membership_end_date` update होते

### 8.2) Pending Dues / Expired Members
पथ: `admin/admin_panel/payments/dues.php`

- EXPIRED किंवा आजपर्यंत expiry झालेले members list.
- `Renew & Pay` क्लिक करून लगेच renewal करा.

### 8.3) Payment History
पथ: `admin/admin_panel/payments/index.php`

- Receipt #/Member नाव/Code ने search.
- Receipt view/print.

### 8.4) Receipt Print
पथ: `admin/admin_panel/payments/view.php?id=...`

- `Print Receipt` करून रसीद प्रिंट करा.

---

## 9) Attendance (उपस्थिती / Entry-Exit)
मेनू: **Attendance**

### 9.1) Live Dashboard (Today)
पथ: `admin/admin_panel/attendance/today.php`

- Currently Inside list
- Today’s timeline
- `Manual Exit` करून कोणालाही exit mark करता येते.

### 9.2) Manual Attendance (QR/Member Code scan)
पथ: `admin/admin_panel/attendance/mark.php`

1. `ENTRY` किंवा `EXIT` mode निवडा
2. `Member Code` मध्ये QR स्कॅन करा किंवा code टाइप करा
3. `PROCESS` क्लिक

**सिस्टम नियम (code नुसार):**
- Member **ACTIVE** असणे आवश्यक
- `membership_end_date` आजपेक्षा जुनी असल्यास entry deny आणि status `EXPIRED` होऊ शकतो
- **एका दिवसात 1च entry** allow (आधीच आज entry असेल तर error)

### 9.3) Member Self-Attendance (Reception PIN मोड)
पथ: `.../shivaji_pool/mark_attendance.php`

हा screen reception जवळ kiosk/tablet वर ठेवण्यासाठी आहे.

1. Member `Member Code` किंवा `Phone` टाकतो
2. Reception desk वर display केलेला **Daily PIN** टाकतो
3. System member जर आत नसेल तर **ENTRY** mark; जर आधी entry असेल तर **EXIT** mark

**Daily PIN**
- सिस्टम रोज नवीन PIN बनवते (`Attendance::get_daily_pin()`)
- PIN reception board वर दाखवण्यासाठी (किंवा admin/desk staff ला सांगण्यासाठी) वापरता येतो.

---

## 10) Reports (रिपोर्ट्स)
मेनू: **Reports** (साधारणपणे Super Admin/Admin)

### 10.1) Daily Attendance Report
पथ: `admin/admin_panel/reports/daily.php`

- Date filter करून त्या दिवशीची attendance यादी.
- Total entries, completed visits, avg duration.
- Print option.

### 10.2) Revenue Report
पथ: `admin/admin_panel/reports/revenue.php`

- Month/Year filter
- Total revenue + mode-wise breakup (Cash/UPI/Card etc.)
- Last 6 months trend
- Print option

### 10.3) Member Report
पथ: `admin/admin_panel/reports/members.php`

- Total/Active/Expired/Expiring soon
- Gender distribution
- Last 6 months registrations trend
- Expiring soon list (renewal follow-up)

### 10.4) Attendance Analytics
पथ: `admin/admin_panel/reports/attendance.php`

- Peak hours
- Monthly day-wise trend
- Avg duration
- Top visitors

---

## 11) Staff Management (स्टाफ)
मेनू: **Staff** (Super Admin/Admin)

### 11.1) Staff List
पथ: `admin/admin_panel/staff/index.php`

- सर्व staff accounts
- Edit/View actions

### 11.2) Add Staff
पथ: `admin/admin_panel/staff/add.php`

1. Login credentials: Username, Password, Email, Role
2. Staff details: Full Name, Phone, Employee ID, Designation, Join date, Salary
3. Save

### 11.3) Edit Staff
पथ: `admin/admin_panel/staff/edit.php?id=...`

- Role बदलणे, active/inactive करणे, password reset (optional)

### 11.4) View Staff
पथ: `admin/admin_panel/staff/view.php?id=...`

- Staff प्रोफाइल + recent activity log

### 11.5) Shift Management
पथ: `admin/admin_panel/staff/shifts.php`

- Defined shifts list
- Today assignments list

> टीप: या पेजमध्ये “New Shift” / “Assign Shift” साठी UI placeholders आहेत; पूर्ण form/submit logic भविष्यात implement होऊ शकते.

---

## 12) System Settings (फक्त Super Admin)
मेनू: **Settings**
पथ: `admin/admin_panel/settings/index.php`

येथे बदलता येणारे मुख्य सेटिंग्ज:
- Pool Name / Phone / Email / Address
- Member ID Prefix
- Currency Symbol
- Opening/Closing time
- Records per page
- Session lifetime

> काही सेटिंग्ज लागू होण्यासाठी logout/login करणे उपयोगी ठरू शकते.

---

## 13) सामान्य समस्या (Troubleshooting)
- **Login होत नाही**
  - Username/Password तपासा
  - खूप वेळा चुकीचा पासवर्ड दिल्यास काही वेळ lock होऊ शकतो
- **Member entry होत नाही**
  - Member status `ACTIVE` आहे का?
  - `membership_end_date` expire झाली आहे का?
  - आज आधीच entry झाली आहे का? (सिस्टम 1 entry/day enforce करते)
- **Receipt/Payments दिसत नाहीत**
  - Member profile → Payments tab तपासा
  - Payment History मध्ये receipt # ने शोधा
- **Daily PIN mismatch**
  - Reception board वरचा आजचा PINच वापरा

---

## 14) Best Practices (ऑपरेशनल टिप्स)
- रोज सकाळी dashboard वर **Expiring Soon/Expired** यादी पहा.
- Renewal करताना **Start Date** नीट तपासा (active membership असल्यास पुढच्या दिवसापासून).
- Receipts नियमित प्रिंट/डिजिटल archive ठेवा.
- Staff accounts मध्ये अनावश्यक users **inactive** करा.
- Default superadmin पासवर्ड बदलून सुरक्षित ठेवा.

---

## 15) Quick Reference (द्रुत लिंक)
- Admin Login: `/shivaji_pool/admin/index.php`
- Admin Dashboard: `/shivaji_pool/admin/admin_panel/index.php`
- Members List: `/shivaji_pool/admin/admin_panel/members/index.php`
- Add Member: `/shivaji_pool/admin/admin_panel/members/add.php`
- Attendance (Manual): `/shivaji_pool/admin/admin_panel/attendance/mark.php`
- Attendance (Today): `/shivaji_pool/admin/admin_panel/attendance/today.php`
- Payments History: `/shivaji_pool/admin/admin_panel/payments/index.php`
- Expired Dues: `/shivaji_pool/admin/admin_panel/payments/dues.php`
- Reports: `/shivaji_pool/admin/admin_panel/reports/*`
- Settings: `/shivaji_pool/admin/admin_panel/settings/index.php`
- Member Kiosk (PIN): `/shivaji_pool/mark_attendance.php`
