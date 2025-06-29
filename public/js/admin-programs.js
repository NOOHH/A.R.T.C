document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.show-enrollments-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const programId = this.getAttribute('data-program-id');
            fetch(`/admin/programs/${programId}/enrollments`)
                .then(response => response.json())
                .then(enrollments => {
                    const modal = document.getElementById('enrollmentsModal');
                    const list = document.getElementById('enrollmentsList');
                    list.innerHTML = '';
                    if (enrollments.length === 0) {
                        list.innerHTML = '<li>No students enrolled.</li>';
                    } else {
                        enrollments.forEach(function (enrollment) {
                            list.innerHTML += `<li>${enrollment.student_name || 'Unknown Student'}</li>`;
                        });
                    }
                    modal.classList.add('active');
                });
        });
    });
    document.getElementById('closeEnrollmentsModal').onclick = function () {
        document.getElementById('enrollmentsModal').classList.remove('active');
    };
    document.getElementById('enrollmentsModal').onclick = function (e) {
        if (e.target === this) this.classList.remove('active');
    };
});
