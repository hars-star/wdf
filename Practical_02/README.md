# Practical 2: Semantic HTML5 Pages with Accessibility-Ready Structure

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **Semester**: 3rd Semester, 2026-27 (ODD)
- **CO Mapping**: CO1, CO2

---

## 1. Objective
Develop static HTML5 skeletons for at least 10 StudentHub pages (`Home`, `About`, `Register`, `Login`, `Dashboard`, `Events`, `Profile`, `Contact`, `Admin`, `FAQ`, and `Feedback`) using semantic tags and accessibility-friendly structure.

---

## 2. Implemented Pages List
1. **`index.html`** - Home Page with Hero Banner, Quick Portal Modules, and Campus Announcements Aside.
2. **`about.html`** - About Us page with figures, project scope, and university accreditation.
3. **`register.html`** - Student registration form with accessible labels, input constraints, and ARIA attributes.
4. **`login.html`** - Accessible account sign-in interface with keyboard-navigable controls.
5. **`dashboard.html`** - Student overview with academic KPIs, registered events, and progress trackers.
6. **`events.html`** - Campus event catalog displaying card grid with date, venue, and seat badges.
7. **`profile.html`** - Detailed student profile page with semantic data table and action aside.
8. **`contact.html`** - Contact inquiries form with university physical address and phone details.
9. **`admin.html`** - University administrative console with role-based statistics.
10. **`faq.html`** - Frequently Asked Questions answering key student queries.
11. **`feedback.html`** - Student experience and rating submission form.

---

## 3. Semantic HTML5 Elements Used
- `<header role="banner">` - Top global website header.
- `<nav aria-label="...">` - Primary navigation and breadcrumb navigation.
- `<main id="main-content" role="main">` - Main landmark container.
- `<section aria-labelledby="...">` - Independent thematic content sections.
- `<article>` - Self-contained cards and FAQ entries.
- `<aside>` - Contextual sidebars for notices and quick actions.
- `<figure>` & `<figcaption>` - Illustrative campus diagrams.
- `<address>` - Contact information.
- `<footer role="contentinfo">` - Global footer with sitemap and copyright.

---

## 4. Accessibility Checklist (WCAG 2.1 AA)
- [x] **Skip-to-Content Link**: `#main-content` skip link allows keyboard users to bypass navigation.
- [x] **Semantic Form Labels**: Every `<input>`, `<select>`, and `<textarea>` is tied to a `<label for="...">`.
- [x] **Heading Hierarchy**: Correct logical structure (`<h1>` -> `<h2>` -> `<h3>`) without skipped heading levels.
- [x] **Keyboard Navigation**: Interactive elements receive high-contrast visual focus rings (`outline: 3px solid #f59e0b`).
- [x] **Breadcrumb Navigation**: Marked up with `<nav aria-label="Breadcrumb Navigation">` and `aria-current="page"`.
- [x] **High Contrast Color Scheme**: Background and text colors conform to standard 4.5:1 contrast ratio.

---

## 5. How to Run
Open any HTML file in this directory directly in your web browser, or via Apache:
```
http://localhost/StudentHub_Practicals/Practical_02/index.html
```
