/**
 * StudentHub - Practical 13: AJAX / Fetch API Single Page CRUD Controller
 */

let debounceTimer = null;

document.addEventListener('DOMContentLoaded', () => {
    loadStudents();

    // Debounced search
    document.getElementById('ajaxSearch').addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadStudents(e.target.value, document.getElementById('ajaxDeptFilter').value);
        }, 300);
    });

    document.getElementById('ajaxDeptFilter').addEventListener('change', (e) => {
        loadStudents(document.getElementById('ajaxSearch').value, e.target.value);
    });

    // Form handlers
    document.getElementById('addStudentForm').addEventListener('submit', handleAddStudent);
    document.getElementById('editStudentForm').addEventListener('submit', handleEditStudent);
});

async function loadStudents(search = '', department = '') {
    const tableBody = document.getElementById('ajaxTableBody');
    tableBody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 2rem;">⚡ Loading records asynchronously...</td></tr>`;

    try {
        const queryParams = new URLSearchParams();
        if (search) queryParams.append('search', search);
        if (department) queryParams.append('department', department);

        const res = await fetch(`api/students.php?${queryParams.toString()}`);
        const result = await res.json();

        if (result.success && result.data.length > 0) {
            renderTable(result.data);
            document.getElementById('recordCountBadge').textContent = `${result.data.length} records loaded via API`;
        } else {
            tableBody.innerHTML = `<tr><td colspan="9" style="text-align: center; padding: 2rem; color: #64748b;">No matching student records found.</td></tr>`;
            document.getElementById('recordCountBadge').textContent = '0 records';
        }
    } catch (err) {
        showToast('API Connection Error: ' + err.message, 'error');
    }
}

function renderTable(students) {
    const tableBody = document.getElementById('ajaxTableBody');
    let html = '';
    students.forEach(st => {
        html += `
            <tr id="row-${st.id}">
                <td><strong>${escapeHtml(st.enrollment_no)}</strong></td>
                <td>${escapeHtml(st.name)}</td>
                <td>${escapeHtml(st.email)}</td>
                <td>${escapeHtml(st.department)}</td>
                <td>${escapeHtml(st.course)}</td>
                <td>Yr ${st.year}</td>
                <td><strong style="color: #16a34a;">${st.gpa}</strong></td>
                <td><span class="badge ${st.status === 'Active' ? 'badge-active' : 'badge-inactive'}">${st.status}</span></td>
                <td>
                    <button class="btn btn-outline btn-sm" onclick='openEditModal(${JSON.stringify(st)})'>✏️ Edit</button>
                    <button class="btn btn-danger btn-sm" onclick='deleteStudent(${st.id}, "${escapeHtml(st.name)}")'>🗑️</button>
                </td>
            </tr>
        `;
    });
    tableBody.innerHTML = html;
}

async function handleAddStudent(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.action = 'create';

    try {
        const res = await fetch('api/students.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.success) {
            showToast(result.message, 'success');
            form.reset();
            closeAddModal();
            loadStudents();
        } else {
            showToast(result.message, 'error');
        }
    } catch (err) {
        showToast('Failed to add student: ' + err.message, 'error');
    }
}

async function handleEditStudent(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    data.action = 'update';

    try {
        const res = await fetch('api/students.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if (result.success) {
            showToast(result.message, 'success');
            closeEditModal();
            loadStudents();
        } else {
            showToast(result.message, 'error');
        }
    } catch (err) {
        showToast('Update failed: ' + err.message, 'error');
    }
}

async function deleteStudent(id, name) {
    if (!confirm(`Are you sure you want to delete ${name} (ID: ${id}) via AJAX?`)) return;

    try {
        const res = await fetch('api/students.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: id })
        });
        const result = await res.json();

        if (result.success) {
            showToast(result.message, 'success');
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            showToast(result.message, 'error');
        }
    } catch (err) {
        showToast('Delete request failed: ' + err.message, 'error');
    }
}

// Modal open/close helpers
function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}
function closeAddModal() {
    document.getElementById('addModal').classList.remove('active');
}

function openEditModal(st) {
    document.getElementById('edit_id').value = st.id;
    document.getElementById('edit_enrollment').value = st.enrollment_no;
    document.getElementById('edit_name').value = st.name;
    document.getElementById('edit_email').value = st.email;
    document.getElementById('edit_mobile').value = st.mobile;
    document.getElementById('edit_dept').value = st.department;
    document.getElementById('edit_course').value = st.course;
    document.getElementById('edit_year').value = st.year;
    document.getElementById('edit_gpa').value = st.gpa;
    document.getElementById('edit_status').value = st.status;

    document.getElementById('editModal').classList.add('active');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.padding = '0.9rem 1.4rem';
    toast.style.borderRadius = '8px';
    toast.style.color = '#fff';
    toast.style.fontWeight = 'bold';
    toast.style.zIndex = '9999';
    toast.style.boxShadow = '0 10px 15px rgba(0,0,0,0.2)';
    toast.style.backgroundColor = type === 'success' ? '#16a34a' : (type === 'error' ? '#dc2626' : '#2563eb');
    toast.textContent = message;

    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]);
}
