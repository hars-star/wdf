/**
 * StudentHub - Practical 4: JavaScript DOM Manipulation & Event Handling
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initHamburgerMenu();
    initNotificationBanner();
    initSlider();
    initAccordion();
    initModal();
    initToasts();
});

/* ----------------------------------------------------
   1. Light / Dark Theme Switcher with localStorage
---------------------------------------------------- */
function initTheme() {
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');

    // Retrieve saved theme or default to system preference
    const savedTheme = localStorage.getItem('studenthub_theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);
            showToast(`Theme switched to ${nextTheme.toUpperCase()} mode.`);
        });
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('studenthub_theme', theme);
        if (themeIcon && themeText) {
            if (theme === 'dark') {
                themeIcon.textContent = '☀️';
                themeText.textContent = 'Light Mode';
            } else {
                themeIcon.textContent = '🌙';
                themeText.textContent = 'Dark Mode';
            }
        }
    }
}

/* ----------------------------------------------------
   2. Hamburger Mobile Menu Toggle
---------------------------------------------------- */
function initHamburgerMenu() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navLinks = document.getElementById('navLinks');

    if (hamburgerBtn && navLinks) {
        hamburgerBtn.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('active');
            hamburgerBtn.setAttribute('aria-expanded', isOpen);
            hamburgerBtn.textContent = isOpen ? '✕' : '☰';
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!hamburgerBtn.contains(e.target) && !navLinks.contains(e.target)) {
                navLinks.classList.remove('active');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
                hamburgerBtn.textContent = '☰';
            }
        });
    }
}

/* ----------------------------------------------------
   3. Notification Banner with Dismissal
---------------------------------------------------- */
function initNotificationBanner() {
    const banner = document.getElementById('notificationBanner');
    const closeBtn = document.getElementById('closeBannerBtn');

    if (banner && closeBtn) {
        // Check if banner was previously closed in this session
        if (sessionStorage.getItem('banner_closed')) {
            banner.classList.add('hidden');
        }

        closeBtn.addEventListener('click', () => {
            banner.classList.add('hidden');
            sessionStorage.setItem('banner_closed', 'true');
        });
    }
}

/* ----------------------------------------------------
   4. Image / Content Slider (Carousel)
---------------------------------------------------- */
function initSlider() {
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.getElementById('sliderDots');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');
    let currentIndex = 0;
    let autoSlideInterval = null;

    if (!slides.length) return;

    // Create dot indicators dynamically
    dotsContainer.innerHTML = '';
    slides.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.classList.add('dot');
        if (i === 0) dot.classList.add('active');
        dot.setAttribute('aria-label', `Slide ${i + 1}`);
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    });

    const dots = dotsContainer.querySelectorAll('.dot');

    function updateSlider() {
        slides.forEach((slide, idx) => {
            slide.classList.toggle('active', idx === currentIndex);
        });
        dots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = (index + slides.length) % slides.length;
        updateSlider();
        restartAutoSlide();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));

    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            goToSlide(currentIndex + 1);
        }, 4000);
    }

    function restartAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // Pause on hover
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', () => clearInterval(autoSlideInterval));
        sliderContainer.addEventListener('mouseleave', () => startAutoSlide());
    }

    startAutoSlide();
}

/* ----------------------------------------------------
   5. Collapsible FAQ Accordion with Keyboard Support
---------------------------------------------------- */
function initAccordion() {
    const accordionItems = document.querySelectorAll('.accordion-item');

    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        if (!header) return;

        header.addEventListener('click', () => {
            const isOpen = item.classList.contains('open');

            // Close all other accordions (Single expand mode)
            accordionItems.forEach(other => {
                other.classList.remove('open');
                other.querySelector('.accordion-header').setAttribute('aria-expanded', 'false');
            });

            if (!isOpen) {
                item.classList.add('open');
                header.setAttribute('aria-expanded', 'true');
            }
        });
    });
}

/* ----------------------------------------------------
   6. Modal Popup with ESC and Backdrop Dismiss
---------------------------------------------------- */
function initModal() {
    const modalOverlay = document.getElementById('eventModal');
    const openModalBtns = document.querySelectorAll('.open-modal-btn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalTitle = document.getElementById('modalEventTitle');
    const modalDescription = document.getElementById('modalEventDesc');

    if (!modalOverlay) return;

    openModalBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const title = btn.getAttribute('data-title') || 'Event Details';
            const desc = btn.getAttribute('data-desc') || 'Full event agenda, venue details, and rules.';
            
            if (modalTitle) modalTitle.textContent = title;
            if (modalDescription) modalDescription.textContent = desc;

            modalOverlay.classList.add('open');
            document.body.style.overflow = 'hidden'; // prevent background scrolling
        });
    });

    function closeModal() {
        modalOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);

    // Click outside modal box to close
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) closeModal();
    });

    // Press Escape key to close
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalOverlay.classList.contains('open')) {
            closeModal();
        }
    });
}

/* ----------------------------------------------------
   7. Toast Notification System
---------------------------------------------------- */
function initToasts() {
    const triggerBtn = document.getElementById('triggerToastBtn');
    if (triggerBtn) {
        triggerBtn.addEventListener('click', () => {
            showToast('Event RSVP recorded successfully in memory!');
        });
    }
}

function showToast(message, duration = 3500) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.classList.add('toast-container');
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.classList.add('toast');
    toast.innerHTML = `<span>ℹ️</span> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, duration);
}
