# Practical 4: JavaScript DOM Manipulation, Event Handling, and UI Interactivity

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO3, CO4

---

## 1. Objective
Add dynamic client-side UI components:
1. Collapsible FAQ Accordion with smooth transitions and ARIA attributes
2. Modal Popup Dialog with ESC key & click-outside backdrop handlers
3. Image/Content Slider (Carousel) with auto-slide, pause on hover, next/prev arrows, and dynamic dot indicators
4. Notification Banner with session-based dismissal and interactive Toast notification system
5. Mobile Hamburger Navigation toggle
6. Light / Dark Theme Switcher with `localStorage` persistence

---

## 2. Key Implementations
- **Theme Switcher**: Detects system preferences (`prefers-color-scheme`) and persists user selection in `localStorage.setItem('studenthub_theme', theme)`.
- **Event Handling**: Uses `addEventListener` for `click`, `mouseenter`, `mouseleave`, `keydown` (Escape key), and `DOMContentLoaded`.
- **Accessibility**: Includes `aria-expanded`, `aria-modal="true"`, `role="dialog"`, and accessible labels.

---

## 3. How to Run
Open directly in web browser or visit via Apache:
```
http://localhost/StudentHub_Practicals/Practical_04/index.html
```
