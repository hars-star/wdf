/**
 * StudentHub - Practical 6: Fetch API, Dynamic Rendering, Filter, Search, Pagination
 */

let currentTab = 'events';
let rawData = [];
let filteredData = [];
let currentPage = 1;
const itemsPerPage = 6;

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initControls();
    loadDataset('events');
});

function initTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.getAttribute('data-tab');
            currentPage = 1;
            loadDataset(currentTab);
        });
    });
}

function initControls() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortBy = document.getElementById('sortBy');

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        applyFilters();
    });

    categoryFilter.addEventListener('change', () => {
        currentPage = 1;
        applyFilters();
    });

    sortBy.addEventListener('change', () => {
        currentPage = 1;
        applyFilters();
    });
}

async function loadDataset(datasetName) {
    const contentArea = document.getElementById('dataContent');
    contentArea.innerHTML = `
        <div class="loading-box">
            <div class="spinner"></div>
            <p>Fetching ${datasetName} JSON data...</p>
        </div>
    `;

    const cacheKey = `studenthub_cache_${datasetName}`;
    const url = `data/${datasetName}.json`;

    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
        const data = await response.json();
        
        // Save to cache for offline availability
        localStorage.setItem(cacheKey, JSON.stringify(data));
        rawData = data;
        updateFilterDropdown(datasetName);
        applyFilters();
    } catch (err) {
        console.warn(`Fetch error for ${url}, trying localStorage cache...`, err);
        const cached = localStorage.getItem(cacheKey);
        if (cached) {
            rawData = JSON.parse(cached);
            updateFilterDropdown(datasetName);
            applyFilters();
            showStatusBadge(`Offline Mode: Loaded ${datasetName} from localStorage cache.`);
        } else {
            contentArea.innerHTML = `
                <div class="empty-box">
                    <h3>⚠️ Failed to Load Data</h3>
                    <p>${err.message}</p>
                </div>
            `;
        }
    }
}

function updateFilterDropdown(datasetName) {
    const filterSelect = document.getElementById('categoryFilter');
    filterSelect.innerHTML = '<option value="">All Categories / Departments</option>';

    let uniqueValues = new Set();
    if (datasetName === 'events') {
        rawData.forEach(item => uniqueValues.add(item.category));
    } else if (datasetName === 'students') {
        rawData.forEach(item => uniqueValues.add(item.department));
    } else if (datasetName === 'faqs') {
        rawData.forEach(item => uniqueValues.add(item.category));
    }

    uniqueValues.forEach(val => {
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = val;
        filterSelect.appendChild(opt);
    });
}

function applyFilters() {
    const query = document.getElementById('searchInput').value.toLowerCase().trim();
    const selectedFilter = document.getElementById('categoryFilter').value;
    const sortVal = document.getElementById('sortBy').value;

    filteredData = rawData.filter(item => {
        // Search query across all object values
        const matchesQuery = Object.values(item).some(val => 
            String(val).toLowerCase().includes(query)
        );

        // Category / Department match
        let matchesCategory = true;
        if (selectedFilter) {
            if (currentTab === 'events' || currentTab === 'faqs') {
                matchesCategory = item.category === selectedFilter;
            } else if (currentTab === 'students') {
                matchesCategory = item.department === selectedFilter;
            }
        }

        return matchesQuery && matchesCategory;
    });

    // Apply Sorting
    sortData(sortVal);

    renderCurrentView();
}

function sortData(criteria) {
    filteredData.sort((a, b) => {
        if (criteria === 'name-asc') {
            const nameA = (a.title || a.name || a.question || '').toLowerCase();
            const nameB = (b.title || b.name || b.question || '').toLowerCase();
            return nameA.localeCompare(nameB);
        } else if (criteria === 'name-desc') {
            const nameA = (a.title || a.name || a.question || '').toLowerCase();
            const nameB = (b.title || b.name || b.question || '').toLowerCase();
            return nameB.localeCompare(nameA);
        } else if (criteria === 'date-asc' && a.date && b.date) {
            return new Date(a.date) - new Date(b.date);
        } else if (criteria === 'gpa-desc' && a.gpa && b.gpa) {
            return b.gpa - a.gpa;
        }
        return 0;
    });
}

function renderCurrentView() {
    const contentArea = document.getElementById('dataContent');
    const countLabel = document.getElementById('resultCount');
    countLabel.textContent = `Showing ${filteredData.length} records`;

    if (!filteredData.length) {
        contentArea.innerHTML = `
            <div class="empty-box">
                <h3>🔍 No Records Found</h3>
                <p>Try refining your search keyword or clearing the filter.</p>
            </div>
        `;
        renderPagination(0);
        return;
    }

    // Pagination slice
    const startIndex = (currentPage - 1) * itemsPerPage;
    const paginatedItems = filteredData.slice(startIndex, startIndex + itemsPerPage);

    if (currentTab === 'events') {
        renderEventsGrid(paginatedItems, contentArea);
    } else if (currentTab === 'students') {
        renderStudentsTable(paginatedItems, contentArea);
    } else if (currentTab === 'faqs') {
        renderFaqsGrid(paginatedItems, contentArea);
    }

    renderPagination(filteredData.length);
}

function renderEventsGrid(items, container) {
    let html = '<div class="cards-grid">';
    items.forEach(ev => {
        html += `
            <article class="data-card">
                <span class="badge">${ev.category}</span>
                <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">${ev.title}</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; flex: 1; margin-bottom: 1rem;">${ev.description}</p>
                <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; font-size: 0.85rem; display: flex; justify-content: space-between; color: var(--text-muted);">
                    <span>📍 ${ev.venue}</span>
                    <span>📅 ${ev.date}</span>
                </div>
                <div style="margin-top: 0.5rem; font-weight: bold; color: var(--primary); font-size: 0.9rem;">
                    Seats Available: ${ev.seats}
                </div>
            </article>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function renderStudentsTable(items, container) {
    let html = `
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Enrollment</th>
                        <th>Student Name</th>
                        <th>Department</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>GPA</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
    `;
    items.forEach(st => {
        html += `
            <tr>
                <td><strong>${st.enrollment}</strong></td>
                <td>${st.name}</td>
                <td>${st.department}</td>
                <td>${st.course}</td>
                <td>Year ${st.year}</td>
                <td><strong style="color: #16a34a;">${st.gpa}</strong></td>
                <td><span class="badge" style="background: #dcfce7; color: #15803d;">${st.status}</span></td>
            </tr>
        `;
    });
    html += `</tbody></table></div>`;
    container.innerHTML = html;
}

function renderFaqsGrid(items, container) {
    let html = '<div class="cards-grid">';
    items.forEach(faq => {
        html += `
            <article class="data-card">
                <span class="badge" style="background: #fef3c7; color: #b45309;">${faq.category}</span>
                <h3 style="font-size: 1.1rem; margin: 0.5rem 0;">${faq.question}</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">${faq.answer}</p>
            </article>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

function renderPagination(totalItems) {
    const paginationContainer = document.getElementById('paginationControls');
    const totalPages = Math.ceil(totalItems / itemsPerPage);

    if (totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }

    let html = `
        <button class="page-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">Prev</button>
    `;

    for (let p = 1; p <= totalPages; p++) {
        html += `
            <button class="page-btn ${p === currentPage ? 'active' : ''}" onclick="changePage(${p})">${p}</button>
        `;
    }

    html += `
        <button class="page-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">Next</button>
    `;

    paginationContainer.innerHTML = html;
}

window.changePage = function(page) {
    currentPage = page;
    renderCurrentView();
    window.scrollTo({ top: 180, behavior: 'smooth' });
};

function showStatusBadge(msg) {
    const countLabel = document.getElementById('resultCount');
    countLabel.textContent = msg;
}
