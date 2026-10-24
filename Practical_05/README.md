# Practical 5: Registration Form with Frontend Validation and User-Friendly Error Handling

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO3

---

## 1. Objective
Create a student registration form with HTML5 input types and JavaScript validation for:
- Full Name, Email, Mobile Number, Password, Confirm Password, Course, Year, Gender, and Terms Acceptance.
- Use Regular Expressions for client-side validations.
- Real-time validation feedback on `input` / `change` events.
- Password strength meter with visual bar and rules checklist.
- Custom HTML5 Canvas CAPTCHA generation and validation.

---

## 2. Validation Test Cases Table

| Field | Valid Input | Invalid Input | Expected Error Message |
| :--- | :--- | :--- | :--- |
| **Full Name** | `Harshit Dhanani` | `Ha123` or `H` | "Name must contain 3-50 alphabetic characters only." |
| **Email** | `harshit@charusat.edu.in` | `harshit@` or `harshit.com` | "Please enter a valid email address." |
| **Mobile** | `9898123456` | `12345` or `5898123456` | "Must be a valid 10-digit Indian mobile number starting with 6-9." |
| **Password** | `P@ssw0rd2026` | `password` or `12345678` | "Password must fulfill all security criteria below." |
| **Confirm Password**| Same as Password | Does not match | "Passwords do not match." |
| **Gender** | Male / Female / Other | None selected | "Please choose your gender." |
| **CAPTCHA** | Exact alphanumeric match | Wrong characters | "Incorrect CAPTCHA. Please try again." |
| **Terms** | Checkbox checked | Unchecked | "You must accept the terms and conditions." |

---

## 3. How to Run
Open directly in web browser or visit via Apache:
```
http://localhost/StudentHub_Practicals/Practical_05/index.html
```
Test with valid and invalid inputs to see real-time error states, password strength transitions, and Canvas CAPTCHA verification.
