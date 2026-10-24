# Practical 11: Student Management CRUD with Search and Filter

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO4, CO5

---

## 1. Objective
Create a student management module to add, view, edit, delete, search, and filter student records. All database operations must use MySQLi prepared statements and show clear success/failure notifications.

---

## 2. Features Implemented
- **Create**: Add student modal with duplication check on enrollment number & email.
- **Read**: Dynamic responsive table with pagination (8 items per page).
- **Update**: Interactive edit modal pre-filled with student data.
- **Delete**: Confirmation dialog preventing accidental data loss.
- **Search**: Multi-column live search matching Name, Email, Enrollment No, or Course.
- **Filters**: Independent filtering by Department, Year of Study, and Status.
- **CSV Export**: `export_csv.php` allows downloading the full student roster directly to CSV.
- **Seeded Dataset**: 20+ realistic student records pre-seeded into MySQL.

---

## 3. How to Run
Access via Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_11/index.php
```
Test:
1. Search "Harshit" or "Patel" to test the search query.
2. Filter by Department "Information Technology".
3. Click "📥 Export CSV" to download the roster.
4. Click "+ Add New Student" to add a new record.
