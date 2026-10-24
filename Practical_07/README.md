# Practical 7: PHP Form Processing with Server-Side Validation and CSV/JSON Storage

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO5

---

## 1. Objective
Process submitted student registration/inquiry data using PHP backend scripts with:
- Server-side validation (`filter_var`, regex checks for mobile and email)
- Input sanitization (`FILTER_SANITIZE_SPECIAL_CHARS`, `htmlspecialchars`)
- CSRF Token generation and verification
- Concurrency-safe file writing with exclusive file locking (`LOCK_EX`) to both `storage/submissions.csv` and `storage/submissions.json`
- Submissions viewer table at `view_submissions.php`

---

## 2. Server-Side Validations
1. **Name**: Checked for minimum 3 characters.
2. **Email**: Verified using `filter_var($email, FILTER_VALIDATE_EMAIL)`.
3. **Mobile**: Verified against regex `/^[6-9]\d{9}$/` for 10-digit Indian numbers.
4. **CSRF**: Verifies cryptographic random session token using `hash_equals()`.

---

## 3. How to Run
Run through Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_07/index.php
```
View saved submissions:
```
http://localhost/StudentHub_Practicals/Practical_07/view_submissions.php
```
