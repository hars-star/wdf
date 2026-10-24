# Practical 9: Secure User Registration with Database Insert, Duplicate Email Check, and Password Hashing

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO3, CO5

---

## 1. Objective
Implement secure user registration by inserting user details into MySQL using prepared statements. Validate input on frontend and backend, check for duplicate email/username, hash passwords using `password_hash()`, and record registration events in the `audit_logs` table.

---

## 2. Security Mechanisms
1. **Prepared Statements**: Uses PDO parameterized queries (`?`) to prevent SQL Injection attacks.
2. **Password Hashing**: Implements BCRYPT algorithm with cost factor 12 via `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`.
3. **Duplicate Prevention**: Queries database before insertion to verify that neither email nor username is already taken.
4. **CSRF Protection**: Form submissions require a matching cryptographically generated session token.
5. **Audit Logging**: Every successful registration creates an immutable entry in the `audit_logs` table.

---

## 3. How to Run
Access via Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_09/index.php
```
Test registering with an existing email (`hrdhanani2117@gmail.com`) to observe the duplicate email detection.
