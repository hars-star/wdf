/**
 * StudentHub - Practical 11: Student CRUD Client Scripts
 */

function openAddModal() {
    document.getElementById('addStudentModal').classList.add('active');
}

function closeAddModal() {
    document.getElementById('addStudentModal').classList.remove('active');
}

function openEditModal(student) {
    document.getElementById('edit_id').value = student.id;
    document.getElementById('edit_enrollment').value = student.enrollment_no;
    document.getElementById('edit_name').value = student.name;
    document.getElementById('edit_email').value = student.email;
    document.getElementById('edit_mobile').value = student.mobile;
    document.getElementById('edit_department').value = student.department;
    document.getElementById('edit_course').value = student.course;
    document.getElementById('edit_year').value = student.year;
    document.getElementById('edit_gender').value = student.gender;
    document.getElementById('edit_gpa').value = student.gpa;
    document.getElementById('edit_status').value = student.status;

    document.getElementById('editStudentModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editStudentModal').classList.remove('active');
}

function confirmDelete(id, name) {
    if (confirm(`Are you sure you want to permanently delete student "${name}" (ID: ${id})? This cannot be undone.`)) {
        window.location.href = `student_action.php?action=delete&id=${id}`;
    }
}

function confirmToggleStatus(id, currentStatus) {
    const nextStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
    if (confirm(`Change status of this student to "${nextStatus}"?`)) {
        window.location.href = `student_action.php?action=toggle_status&id=${id}&status=${nextStatus}`;
    }
}

// Close modals when clicking backdrop
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
