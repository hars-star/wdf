# Practical 10: Secure Login/Logout with Sessions, Role-Based Access, Timeout, and Remember-Me Token

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO5

---

## 1. Objective
Develop secure login and logout functionality using PHP sessions:
- Password verification using `password_verify()`
- Session fixation protection using `session_regenerate_id(true)`
- Role-Based Access Control (RBAC):
  - Admin (`admin@studenthub.com` / `admin123`) -> `admin_dashboard.php`
  - Student (`student@studenthub.com` / `student123`) -> `student_dashboard.php`
- Inactivity session timeout after 15 minutes
- Secure 2-factor remember-me token stored as a SHA-256 hash in MySQL (`remember_tokens` table)
- Secure logout with complete session destruction and token revocation

---

## 2. Test Credentials
- **Admin**: `admin@studenthub.com` | Password: `admin123`
- **Student**: `student@studenthub.com` | Password: `student123`
- **Student**: `hrdhanani2117@gmail.com` | Password: `student123`

---

## 3. How to Run
Access via Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_10/login.php
```
Test:
1. Log in as Admin -> redirected to `admin_dashboard.php`.
2. Try directly opening `student_dashboard.php` as Admin -> redirected to appropriate dashboard.
3. Log out -> session and remember cookie are purged.
