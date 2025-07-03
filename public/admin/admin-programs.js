document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log('Admin Programs JS loaded');

    // ✅ Modal functionality
    const addModalBg = document.getElementById('addModalBg');
    const batchModalBg = document.getElementById('batchModalBg');
    const enrollmentsModal = document.getElementById('enrollmentsModal');
    const showAddModal = document.getElementById('showAddModal');
    const showBatchModal = document.getElementById('showBatchModal');
    const cancelAddModal = document.getElementById('cancelAddModal');
    const cancelBatchModal = document.getElementById('cancelBatchModal');
    const closeEnrollmentsModal = document.getElementById('closeEnrollmentsModal');

    console.log('Elements found:', {
        addModalBg: !!addModalBg,
        batchModalBg: !!batchModalBg,
        enrollmentsModal: !!enrollmentsModal,
        showAddModal: !!showAddModal,
        showBatchModal: !!showBatchModal
    });

    // Show Add Program Modal
    if (showAddModal) {
        showAddModal.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add button clicked');
            if (addModalBg) {
                addModalBg.classList.add('active');
                addModalBg.style.display = 'flex';
            }
        });
    }

    // Show Batch Upload Modal
    if (showBatchModal) {
        showBatchModal.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Batch button clicked');
            if (batchModalBg) {
                batchModalBg.classList.add('active');
                batchModalBg.style.display = 'flex';
            }
        });
    }

    // Close modals
    if (cancelAddModal) {
        cancelAddModal.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Cancel add clicked');
            if (addModalBg) {
                addModalBg.classList.remove('active');
                addModalBg.style.display = 'none';
            }
        });
    }

    if (cancelBatchModal) {
        cancelBatchModal.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Cancel batch clicked');
            if (batchModalBg) {
                batchModalBg.classList.remove('active');
                batchModalBg.style.display = 'none';
            }
        });
    }

    if (closeEnrollmentsModal) {
        closeEnrollmentsModal.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Close enrollments clicked');
            if (enrollmentsModal) {
                enrollmentsModal.classList.remove('active');
                enrollmentsModal.style.display = 'none';
            }
        });
    }

    // Close modal when clicking background
    [addModalBg, batchModalBg, enrollmentsModal].forEach(modal => {
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    modal.style.display = 'none';
                }
            });
        }
    });

    // ✅ View Enrollees functionality
    document.querySelectorAll('.view-enrollees-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const programId = this.getAttribute('data-program-id');
            console.log('View enrollees clicked for program:', programId);
            
            const loadingMessage = document.getElementById('loadingMessage');
            const enrollmentsList = document.getElementById('enrollmentsList');
            
            // Show modal and loading state
            if (enrollmentsModal) {
                enrollmentsModal.classList.add('active');
                enrollmentsModal.style.display = 'flex';
            }
            if (loadingMessage) {
                loadingMessage.style.display = 'block';
            }
            if (enrollmentsList) {
                enrollmentsList.style.display = 'none';
                enrollmentsList.innerHTML = '';
            }

            // Fetch enrollments
            fetch(`/admin/programs/${programId}/enrollments`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (loadingMessage) loadingMessage.style.display = 'none';
                if (enrollmentsList) {
                    enrollmentsList.style.display = 'block';
                    
                    if (data.enrollments && data.enrollments.length > 0) {
                        enrollmentsList.innerHTML = '';
                        data.enrollments.forEach(enrollment => {
                            const li = document.createElement('li');
                            li.innerHTML = `
                                <strong>${enrollment.student_name}</strong><br>
                                <small>Email: ${enrollment.student_email}</small><br>
                                <small>Enrolled: ${enrollment.enrollment_date}</small>
                            `;
                            li.style.cssText = 'margin-bottom: 10px; padding: 10px; background: #f8f9fa; border-radius: 5px; list-style: none;';
                            enrollmentsList.appendChild(li);
                        });
                    } else {
                        enrollmentsList.innerHTML = '<li style="text-align: center; color: #666; list-style: none; padding: 20px;">No enrollments found for this program.</li>';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (loadingMessage) loadingMessage.style.display = 'none';
                if (enrollmentsList) {
                    enrollmentsList.style.display = 'block';
                    enrollmentsList.innerHTML = '<li style="text-align: center; color: #dc3545; list-style: none; padding: 20px;">Error loading enrollments. Please try again.</li>';
                }
            });
        });
    });

    // ✅ Archive Program functionality
    document.querySelectorAll('.archive-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const programId = this.getAttribute('data-program-id');
            const programCard = this.closest('.program-card');
            const programName = programCard ? programCard.querySelector('.program-title')?.textContent : 'this program';
            
            console.log('Archive clicked for program:', programId);
            
            if (confirm(`Are you sure you want to archive "${programName}"?`)) {
                console.log('User confirmed archive');
                
                fetch(`/admin/programs/${programId}/archive`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        programCard.style.transition = 'opacity 0.3s ease';
                        programCard.style.opacity = '0';
                        setTimeout(() => {
                            programCard.remove();
                        }, 300);
                        showMessage(data.message, 'success');
                    } else {
                        showMessage(data.message || 'Error archiving program', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Error archiving program. Please try again.', 'error');
                });
            }
        });
    });

    // ✅ Course Assignment Form
    const courseAssignmentForm = document.getElementById('courseAssignmentForm');
    if (courseAssignmentForm) {
        console.log('Assignment form found');
        courseAssignmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Assignment form submitted');
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('.assign-btn');
            const originalText = submitBtn ? submitBtn.textContent : '';
            
            // Debug form data
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Show loading state
            if (submitBtn) {
                submitBtn.textContent = 'Assigning...';
                submitBtn.disabled = true;
            }
            
            // Check if we have an action URL
            const actionUrl = this.getAttribute('action');
            console.log('Form action URL:', actionUrl);
            
            if (!actionUrl) {
                showMessage('Form action URL not set. Please check the backend route.', 'error');
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
                return;
            }
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showMessage(data.message || 'Program assigned successfully!', 'success');
                    this.reset(); // Clear form
                } else {
                    showMessage(data.message || 'Error assigning program', 'error');
                }
            })
            .catch(error => {
                console.error('Assignment error:', error);
                showMessage('Error assigning program. Please try again.', 'error');
            })
            .finally(() => {
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    } else {
        console.log('Assignment form not found');
    }

    // ✅ Chart initialization (if Chart.js is loaded)
    const ctx = document.getElementById('programChart');
    if (ctx && typeof Chart !== 'undefined') {
        const programChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartLabels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [
                    {
                        label: 'Enrollments',
                        data: window.enrollmentData || [12, 19, 15, 25, 22, 30],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Chart controls
        document.querySelectorAll('.chart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.chart-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const chartType = this.getAttribute('data-chart');
                if (chartType === 'enrollments') {
                    programChart.data.datasets[0].hidden = false;
                } else {
                    programChart.data.datasets[0].hidden = true;
                }
                programChart.update();
            });
        });
    }

    // ✅ Show message function
    function showMessage(message, type = 'success') {
        console.log('Showing message:', message, type);
        
        // Remove existing messages
        document.querySelectorAll('.temp-message').forEach(msg => msg.remove());
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `${type}-message temp-message`;
        messageDiv.textContent = message;
        messageDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            padding: 15px 20px;
            border-radius: 5px;
            font-weight: bold;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background: ${type === 'success' ? '#d4edda' : type === 'error' ? '#f8d7da' : '#d1ecf1'};
            color: ${type === 'success' ? '#155724' : type === 'error' ? '#721c24' : '#0c5460'};
            border: 1px solid ${type === 'success' ? '#c3e6cb' : type === 'error' ? '#f5c6cb' : '#bee5eb'};
        `;
        
        document.body.appendChild(messageDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Test function - remove this after testing
    window.testMessage = function() {
        showMessage('Test message working!', 'success');
    };
});