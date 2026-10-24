# Practical 14: Admin Dashboard with Role-Based Access Control, Dynamic Menu, and Audit Log

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO4, CO5

---

## 1. Objective
Develop an administrative dashboard displaying live user statistics, student count, event count, and latest registrations. Restrict access to admin users only, show dynamic menu options based on role, provide active/inactive user toggles, and record significant admin actions in the `audit_logs` table.

---

## 2. Implemented Features
- **Real-Time SQL Aggregations**:
  - Total Students, Active Events, Registrations, and User Accounts.
- **Analytics Visualization**:
  - CSS/Data-driven Bar Chart displaying student enrollment distribution across academic departments.
- **Role-Based Dynamic Menu**:
  - Admin sidebar with quick links to Student CRUD, Event CRUD, AJAX Center, and Audit Trails.
- **Account Moderation**:
  - One-click Active/Inactive status toggle for users, with audit trail recording.
- **Audit Log Inspection**:
  - `audit_logs.php` providing filterable traceability by action type (`STATUS_TOGGLE`, `EVENT_CREATE`, etc.) with client IP and timestamps.

---

## 3. How to Run
Access via Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_14/index.php
```
Test:
1. View KPI counter cards and department bar chart.
2. Toggle a user's status by clicking their status badge.
3. Click "View Audit Trail" to verify the recorded log entry.
