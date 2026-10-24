# Practical 8: MySQL Schema Design, ER Model, and Database Connectivity

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO1, CO5

---

## 1. Objective
Design the relational database for StudentHub with students, events, registrations, users, and audit logs. Create PHP database connectivity using PDO and MySQLi with prepared statements, error exception handling, and normalized ER architecture.

---

## 2. Key Components
- **`db.php`**: Secure database library offering both `getPDOConnection()` and `getMySQLiConnection()`.
- **`schema.sql`**: Full DDL schema with foreign key constraints, indexes, and seeded records.
- **`test_connection.php`**: Interactive diagnostic script checking connectivity, table statistics, and character encoding.
- **`er_diagram.html`**: Visual representation of the 3NF relational schema.

---

## 3. How to Run
Test MySQL connection via Apache:
```
http://localhost/StudentHub_Practicals/Practical_08/test_connection.php
```
View Interactive ER Diagram:
```
http://localhost/StudentHub_Practicals/Practical_08/er_diagram.html
```
