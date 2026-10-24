# Practical 3: Responsive UI Design using CSS Grid and Flexbox

## Course Information
- **Course**: Web Development Frameworks (ITUE203)
- **Institution**: Charotar University of Science and Technology (CHARUSAT)
- **Faculty**: Faculty of Technology and Engineering (FTE)
- **CO Mapping**: CO2

---

## 1. Objective
Design responsive layouts for Home, About, Registration, Dashboard, Events, and Contact pages using CSS Grid and Flexbox, adhering to mobile-first responsive principles.

---

## 2. Layout Techniques & Rationale
1. **CSS Grid**:
   - Multi-column event card catalog (`repeat(auto-fit, minmax(290px, 1fr))`)
   - 4-column academic dashboard metrics cards
   - Hero banner dual-column grid (Headline + Realtime Stat Counter)
   - Multi-tier registration form layout
2. **CSS Flexbox**:
   - Sticky top navigation bar and announcement header
   - Alignment of card headers, tags, and action buttons
   - Dynamic footer column alignments
3. **Media Queries**:
   - Mobile: `< 640px` (single column flow, stacked forms)
   - Tablet: `640px - 992px` (2-column grid)
   - Desktop: `> 992px` (3/4-column grid with sidebar aside)

---

## 3. How to Run
Open directly in web browser or access via Apache:
```
http://localhost/StudentHub_Practicals/Practical_03/index.html
```
Test with browser DevTools Responsive Mode (iPhone, iPad, Desktop).
