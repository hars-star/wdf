# Practical 12: Event Management CRUD with Poster Upload and File Validation

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO4, CO5

---

## 1. Objective
Develop complete event management functionality for campus administrators:
- Create, Read, Update, and Delete campus events.
- Event poster upload with server-side validation:
  - Allowed file formats: JPG, PNG, WEBP
  - Maximum upload size: 2 Megabytes
  - Safe unique filename generation via `uniqid('poster_', true)`
  - Directory protection using `.htaccess` preventing direct execution of PHP scripts
- Filter events by Category and Status (Upcoming, Ongoing, Closed).

---

## 2. File Upload Security Checklist
- [x] **MIME Validation**: Verified server-side via `finfo_file(finfo_open(FILEINFO_MIME_TYPE), ...)`.
- [x] **Extension Whitelisting**: Restricted to `jpg`, `jpeg`, `png`, and `webp`.
- [x] **Size Boundary**: Checked against `2 * 1024 * 1024` bytes.
- [x] **Safe Renaming**: Prevents directory traversal and overwriting existing assets.
- [x] **Script Execution Disabled**: `uploads/.htaccess` explicitly blocks PHP/CGI execution.

---

## 3. How to Run
Access via Apache:
```
http://localhost/StudentHub_Practicals/Practical_12/index.php
```
Upload an event poster and test editing/deleting events.
