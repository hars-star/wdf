# Practical 13: PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO3, CO4, CO5

---

## 1. Objective
Create simple PHP JSON API endpoints for student/event management and consume them using JavaScript Fetch API. Perform list, add, update, and delete operations asynchronously without full page reload.

---

## 2. API Endpoints Specification
- **Endpoint**: `api/students.php`
  - `GET`: Returns JSON array of student records with support for `?search=...` and `?department=...`
  - `POST` (`action=create`): Inserts new student; returns HTTP 201 Created on success.
  - `POST` (`action=update`): Updates student details; returns HTTP 200 OK.
  - `POST` (`action=delete`): Deletes record; returns HTTP 200 OK.
  - Responses formatted as:
    ```json
    {
      "success": true,
      "message": "...",
      "data": [...],
      "timestamp": "2026-10-22T05:50:00+05:30"
    }
    ```

---

## 3. Frontend Interactivity
- Debounced live search (300ms threshold) minimizes network requests.
- Add and Edit dialogs handle data dispatch via JSON POST payloads.
- Client-side DOM row removal with smooth transitions.
- Toast notifications for asynchronous feedback.

---

## 4. How to Run
Access via Apache web server:
```
http://localhost/StudentHub_Practicals/Practical_13/index.html
```
Open browser DevTools (Network tab) to observe asynchronous JSON API transactions.
