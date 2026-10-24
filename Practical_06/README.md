# Practical 6: Rendering External JSON Data using Fetch API, Search and Filter

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO3, CO4

---

## 1. Objective
Display event lists, student profiles, and FAQs from external JSON files using JavaScript Fetch API. Implement dynamic rendering, multi-field search, category filtering, sorting, and pagination.

---

## 2. Features Implemented
- **3 External Datasets** with 15+ comprehensive records each:
  - `data/events.json`
  - `data/students.json`
  - `data/faqs.json`
- **Asynchronous Fetching**: Implemented via modern `async/await` syntax.
- **Search & Filter**: Real-time evaluation across all item properties with category/department filtering.
- **Sorting**: Flexible sorting (Alphabetical, Date, GPA).
- **Pagination**: 6 items per page with Next/Previous and numerical buttons.
- **Offline Caching**: Caches responses in `localStorage` to allow offline-like display if the network is disconnected.

---

## 3. How to Run
Access via Apache web server (since modern browsers restrict `fetch()` on `file://` protocol):
```
http://localhost/StudentHub_Practicals/Practical_06/index.html
```
