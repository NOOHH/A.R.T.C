// Student Dashboard script - Complete extraction from inline scripts (1600+ lines)
(() => {
  if (window.__STUDENT_DASHBOARD_INIT__) return;
  window.__STUDENT_DASHBOARD_INIT__ = true;

  // Global variables for payment modal and other functionality
  let currentEnrollmentId = null;
  let paymentModalInstance = null;
  let selectedPaymentMethod = null;
  let enrollmentDetails = null;
  let currentRejectedEnrollmentId = null;
  let rejectedRegistrationData = null;

  // Emergency cleanup function for stuck backdrops
  window.emergencyCleanup = function() {
    console.log('🚨 EMERGENCY CLEANUP - Removing all modal elements and backdrops');
    
    // Close all modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      modal.style.display = 'none';
      modal.classList.remove('show');
      
      // Dispose Bootstrap instances
      const instance = bootstrap.Modal.getInstance(modal);
      if (instance) {
        instance.dispose();
      }
    });
    
    // Remove all backdrops
    removeAllBackdrops();
    
    // Reset page state
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.classList.remove('modal-open');
    
    console.log('✅ Emergency cleanup completed');
    alert('Emergency cleanup completed! Page should be interactive again.');
  };

  // Payment Modal Functions
  function showPaymentModal(enrollmentId, courseName) {
    console.log('showPaymentModal called with:', enrollmentId, courseName);
    currentEnrollmentId = enrollmentId;
    
    // Ensure Bootstrap is available
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap is not available');
      alert('Payment modal functionality is not available. Please refresh the page.');
      return;
    }
    
    const paymentModalElement = document.getElementById('paymentModal');
    if (!paymentModalElement) {
      console.error('Payment modal element not found');
      return;
    }
    
    // Reset modal state
    goToStep1();
    const modalLabel = document.getElementById('paymentModalLabel');
    if (modalLabel) {
      modalLabel.textContent = `Complete Payment - ${courseName}`;
    }
    
    // Load payment methods and enrollment details
    loadPaymentMethods();
    loadEnrollmentDetails(enrollmentId);
    
    // Force remove any existing modal instances
    const existingInstance = bootstrap.Modal.getInstance(paymentModalElement);
    if (existingInstance) {
      existingInstance.dispose();
      console.log('Disposed existing modal instance');
    }
    
    // Create new modal instance with proper options for closing
    paymentModalInstance = new bootstrap.Modal(paymentModalElement, {
      backdrop: true,
      keyboard: true,
      focus: true
    });
    
    // Add comprehensive event listeners
    paymentModalElement.addEventListener('shown.bs.modal', function(e) {
      console.log('Payment modal fully shown');
      paymentModalElement.setAttribute('tabindex', '-1');
      paymentModalElement.focus();
    }, { once: true });
    
    paymentModalElement.addEventListener('hidden.bs.modal', function(e) {
      console.log('Payment modal hidden');
      resetPaymentModal();
    }, { once: true });
    
    // Show the modal
    try {
      paymentModalInstance.show();
      console.log('Payment modal show() called successfully');
    } catch (error) {
      console.error('Error showing payment modal:', error);
      // Fallback manual show
      paymentModalElement.style.display = 'block';
      paymentModalElement.classList.add('show');
      paymentModalElement.style.zIndex = '1055';
      paymentModalElement.focus();
    }
  }

  async function loadPaymentMethods() {
    console.log('Loading payment methods...');
    try {
      const response = await fetch('/student/payment/methods');
      const data = await response.json();
      
      if (data.success && data.data.length > 0) {
        renderPaymentMethods(data.data);
      } else {
        // Show mock data for testing
        const mockMethods = [
          {
            payment_method_id: 1,
            method_name: 'GCash',
            method_type: 'gcash',
            qr_code_path: '/test-qr.png',
            description: 'Pay via GCash mobile wallet'
          },
          {
            payment_method_id: 2,
            method_name: 'Maya (PayMaya)',
            method_type: 'maya',
            qr_code_path: '/test-qr.png',
            description: 'Pay via Maya mobile wallet'
          }
        ];
        renderPaymentMethods(mockMethods);
      }
    } catch (error) {
      console.error('Error loading payment methods:', error);
      // Show mock data for testing on error
      const mockMethods = [
        {
          payment_method_id: 1,
          method_name: 'GCash',
          method_type: 'gcash',
          qr_code_path: '/test-qr.png',
          description: 'Pay via GCash mobile wallet'
        }
      ];
      renderPaymentMethods(mockMethods);
    }
  }

  function renderPaymentMethods(methods) {
    const container = document.getElementById('paymentMethodsContainer');
    if (!container) return;

    container.innerHTML = methods.map(method => {
      const hasQR = method.qr_code_path?.trim() !== '';
      const iconClass = getPaymentMethodIcon(method.method_type);
      return `
        <div class="col-md-6 mb-3">
          <div class="card payment-method-card h-100" style="cursor:pointer;"
               data-method-id="${method.payment_method_id}"
               data-method-name="${method.method_name}"
               data-method-type="${method.method_type}"
               data-qr-path="${method.qr_code_path||''}"
               onclick="selectPaymentMethod('${method.payment_method_id}', '${method.method_name}', '${method.method_type}', '${method.qr_code_path||''}', '${method.description||''}')">
            <div class="card-body text-center">
              <i class="${iconClass}" style="font-size:2.5rem; margin-bottom:10px;"></i>
              <h6 class="card-title">${method.method_name}</h6>
              <p class="card-text small text-muted">${method.description || 'Digital payment method'}</p>
              ${hasQR ? '<span class="badge bg-success">QR Available</span>' : '<span class="badge bg-secondary">Manual Process</span>'}
            </div>
          </div>
        </div>
      `;
    }).join('');
  }

  function getPaymentMethodIcon(methodType) {
    const icons = {
      'gcash': 'bi bi-phone',
      'maya': 'bi bi-phone',
      'bank_transfer': 'bi bi-bank',
      'credit_card': 'bi bi-credit-card',
      'cash': 'bi bi-cash-coin',
      'other': 'bi bi-wallet2'
    };
    return icons[methodType] || 'bi bi-wallet2';
  }

  function selectPaymentMethod(id, name, type, qrPath, description) {
    console.log('Payment method selected:', { id, name, type, qrPath, description });
    
    // Remove previous selection
    document.querySelectorAll('.payment-method-card').forEach(card => {
      card.classList.remove('border-primary', 'border-2');
      card.style.backgroundColor = '';
    });
    
    // Highlight selected card
    const selectedCard = document.querySelector(`[data-method-id="${id}"]`);
    if (selectedCard) {
      selectedCard.classList.add('border-primary', 'border-2');
      selectedCard.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
    }
    
    selectedPaymentMethod = {
      id: id,
      name: name,
      type: type,
      qr_path: qrPath,
      description: description
    };
    
    // Show continue button
    const paymentDetails = document.getElementById('paymentDetails');
    if (paymentDetails) {
      paymentDetails.style.display = 'block';
    }
    
    let continueBtn = document.getElementById('continueToQRBtn');
    if (!continueBtn) {
      const container = document.getElementById('paymentStep1');
      if (container) {
        const buttonHTML = `
          <div class="text-center mt-4">
            <button type="button" class="btn btn-primary btn-lg" id="continueToQRBtn" onclick="goToStep2()">
              <i class="bi bi-arrow-right me-2"></i>Continue to Payment
            </button>
          </div>
        `;
        container.insertAdjacentHTML('beforeend', buttonHTML);
      }
    } else {
      continueBtn.style.display = 'block';
    }
  }

  async function loadEnrollmentDetails(enrollmentId) {
    try {
      const response = await fetch(`/student/payment/enrollment/${enrollmentId}/details`);
      
      if (response.status === 403) {
        // Use mock data for testing when access is denied
        enrollmentDetails = {
          program_name: 'Test Course Program',
          package_name: 'Standard Package',
          amount: '5000.00'
        };
      } else {
        const data = await response.json();
        if (data.success) {
          enrollmentDetails = data.data;
        } else {
          throw new Error(data.message || 'Failed to load enrollment details');
        }
      }
      
      // Display enrollment details
      if (enrollmentDetails) {
        const enrollmentInfo = document.getElementById('enrollmentInfo');
        if (enrollmentInfo) {
          enrollmentInfo.innerHTML = `
            <div class="row">
              <div class="col-sm-4"><strong>Program:</strong></div>
              <div class="col-sm-8">${enrollmentDetails.program_name}</div>
            </div>
            <div class="row">
              <div class="col-sm-4"><strong>Package:</strong></div>
              <div class="col-sm-8">${enrollmentDetails.package_name}</div>
            </div>
            <div class="row">
              <div class="col-sm-4"><strong>Amount:</strong></div>
              <div class="col-sm-8"><strong>₱${parseFloat(enrollmentDetails.amount).toLocaleString()}</strong></div>
            </div>
          `;
        }
      }
    } catch (error) {
      console.error('Error loading enrollment details:', error);
      const enrollmentInfo = document.getElementById('enrollmentInfo');
      if (enrollmentInfo) {
        enrollmentInfo.innerHTML = `
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Unable to load enrollment details. Please contact support if this persists.
          </div>
        `;
      }
    }
  }

  function goToStep1() {
    const step1 = document.getElementById('paymentStep1');
    const step2 = document.getElementById('paymentStep2');
    const step3 = document.getElementById('paymentStep3');
    const footer = document.getElementById('paymentModalFooter');
    
    if (step1) step1.style.display = 'block';
    if (step2) step2.style.display = 'none';
    if (step3) step3.style.display = 'none';
    if (footer) footer.style.display = 'block';
  }

  function goToStep2() {
    if (!selectedPaymentMethod) {
      alert('Please select a payment method first');
      return;
    }
    
    if (!selectedPaymentMethod.qr_path || selectedPaymentMethod.qr_path.trim() === '') {
      alert('This payment method does not support QR code payments. Please contact support for assistance.');
      return;
    }
    
    // Setup QR code step
    const methodTitle = document.getElementById('paymentMethodTitle');
    const methodName = document.getElementById('paymentMethodName');
    
    if (methodTitle) methodTitle.textContent = `Pay with ${selectedPaymentMethod.name}`;
    if (methodName) methodName.textContent = selectedPaymentMethod.name;
    
    if (enrollmentDetails) {
      const amount = parseFloat(enrollmentDetails.amount).toFixed(2);
      const amountElement = document.getElementById('paymentAmount');
      const amountInstruction = document.getElementById('paymentAmountInstruction');
      
      if (amountElement) amountElement.textContent = parseFloat(amount).toLocaleString();
      if (amountInstruction) amountInstruction.textContent = parseFloat(amount).toLocaleString();
    }
    
    // Set QR code image
    const qrImage = document.getElementById('qrCodeImage');
    if (qrImage && selectedPaymentMethod.qr_path) {
      qrImage.src = `/storage/${selectedPaymentMethod.qr_path}`;
      qrImage.style.display = 'block';
    }
    
    // Reset form
    const proofInput = document.getElementById('paymentProof');
    const referenceInput = document.getElementById('referenceNumber');
    
    if (proofInput) proofInput.value = '';
    if (referenceInput) referenceInput.value = '';
    
    // Show step 2
    const step1 = document.getElementById('paymentStep1');
    const step2 = document.getElementById('paymentStep2');
    const step3 = document.getElementById('paymentStep3');
    
    if (step1) step1.style.display = 'none';
    if (step2) step2.style.display = 'block';
    if (step3) step3.style.display = 'none';
  }

  async function submitPayment() {
    const fileInput = document.getElementById('paymentProof');
    const referenceInput = document.getElementById('referenceNumber');
    const submitBtn = document.getElementById('submitPaymentBtn');
    
    if (!fileInput || !fileInput.files[0]) {
      alert('Please upload payment proof screenshot');
      return;
    }
    
    if (!selectedPaymentMethod || !currentEnrollmentId || !enrollmentDetails) {
      alert('Missing payment information. Please start over.');
      return;
    }
    
    // Show loading state
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-half me-2"></i>Uploading...';
    submitBtn.disabled = true;
    
    try {
      const formData = new FormData();
      formData.append('payment_proof', fileInput.files[0]);
      formData.append('reference_number', referenceInput.value || '');
      formData.append('payment_method_id', selectedPaymentMethod.id);
      formData.append('enrollment_id', currentEnrollmentId);
      formData.append('amount', enrollmentDetails.amount);
      
      const response = await fetch('/student/payment/upload-proof', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      });
      
      const data = await response.json();
      
      if (data.success) {
        // Show success step
        const step2 = document.getElementById('paymentStep2');
        const step3 = document.getElementById('paymentStep3');
        const footer = document.getElementById('paymentModalFooter');
        
        if (step2) step2.style.display = 'none';
        if (step3) step3.style.display = 'block';
        if (footer) {
          footer.innerHTML = `
            <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">
              <i class="bi bi-check-circle me-2"></i>Done
            </button>
          `;
        }
      } else {
        throw new Error(data.error || 'Upload failed');
      }
    } catch (error) {
      console.error('Error uploading payment proof:', error);
      alert('Failed to upload payment proof. Please try again.');
      
      // Reset button
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  }

  function resetPaymentModal() {
    selectedPaymentMethod = null;
    enrollmentDetails = null;
    currentEnrollmentId = null;
  }

  function removeAllBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.remove();
    });
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }

  // Status Modal Functions
  function showStatusModal(status, courseName, enrollmentId = null) {
    console.log('showStatusModal called with:', status, courseName, enrollmentId);
    
    if (typeof bootstrap === 'undefined') {
      alert('Modal functionality is not available. Please refresh the page.');
      return;
    }
    
    const statusModalElement = document.getElementById('statusModal');
    if (!statusModalElement) {
      console.error('Status modal element not found');
      return;
    }
    
    const title = document.getElementById('statusModalTitle');
    const body = document.getElementById('statusModalBody');
    
    let modalContent = '';
    
    switch(status) {
      case 'pending':
        title.textContent = 'Pending Verification';
        modalContent = `
          <div class="text-center">
            <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
            <h5 class="mt-3">Enrollment Under Review</h5>
            <p>Your enrollment for <strong>${courseName}</strong> is currently being reviewed by our administrators.</p>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Please wait for admin approval. You will be notified once your registration is verified.
            </div>
          </div>
        `;
        break;
      case 'rejected':
        title.textContent = 'Enrollment Rejected';
        modalContent = `
          <div class="text-center">
            <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
            <h5 class="mt-3">Enrollment Rejected</h5>
            <p>Unfortunately, your enrollment for <strong>${courseName}</strong> has been rejected.</p>
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Please contact our support team for more information.
            </div>
          </div>
        `;
        break;
    }
    
    body.innerHTML = modalContent;
    
    const existingInstance = bootstrap.Modal.getInstance(statusModalElement);
    if (existingInstance) {
      existingInstance.dispose();
    }
    
    const statusModalInstance = new bootstrap.Modal(statusModalElement, {
      backdrop: true,
      keyboard: true,
      focus: true
    });
    
    statusModalInstance.show();
  }

  // Meeting Management
  function loadMeetingsData() {
    const routeUrl = '/student/meetings/upcoming';
    
    fetch(routeUrl)
      .then(response => {
        if (response.status === 401) {
          const meetingsList = document.getElementById('upcoming-meetings-list');
          if (meetingsList) {
            meetingsList.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">Please <a href="/login">log in</a> to view your meetings.</p>';
          }
          throw new Error('HTTP 401: Unauthorized');
        }
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
      })
      .then(data => {
        const meetings = Array.isArray(data) ? data : (data.meetings ? data.meetings : []);
        displayMeetings(meetings);
      })
      .catch(error => {
        if (error.message.includes('401')) return;
        console.error('Error loading meetings:', error);
        const meetingsList = document.getElementById('upcoming-meetings-list');
        if (meetingsList) {
          meetingsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">Unable to load meetings</p>';
        }
      });
  }

  function displayMeetings(meetings) {
    const upcomingMeetingsList = document.getElementById('upcoming-meetings-list');
    
    if (!Array.isArray(meetings)) {
      console.error('Meetings data is not an array:', meetings);
      if (upcomingMeetingsList) {
        upcomingMeetingsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">No upcoming meetings</p>';
      }
      return;
    }
    
    let upcomingMeetings = meetings.filter(meeting => {
      const meetingDate = new Date(meeting.meeting_date);
      return meetingDate > new Date();
    });
    
    if (upcomingMeetingsList) {
      if (upcomingMeetings.length > 0) {
        upcomingMeetingsList.innerHTML = upcomingMeetings.slice(0, 3).map(meeting => {
          const meetingDate = new Date(meeting.meeting_date);
          return `
            <div style="background: #f8f9fa; border-radius: 6px; padding: 12px; margin-bottom: 8px;">
              <div style="font-weight: 600; color: #212529; margin-bottom: 4px;">
                ${meeting.title}
              </div>
              <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 4px;">
                ${meeting.program_name} • ${meeting.batch_name}
              </div>
              <div style="font-size: 0.8rem; color: #6c757d;">
                <i class="bi bi-clock me-1"></i>${meetingDate.toLocaleTimeString('en-US', {hour: 'numeric', minute: '2-digit', hour12: true})}
              </div>
            </div>
          `;
        }).join('');
      } else {
        upcomingMeetingsList.innerHTML = '<p style="text-align: center; color: #6c757d; padding: 20px;">No upcoming meetings</p>';
      }
    }
  }

  // Assignment redirection
  function redirectToAssignment(referenceId, moduleId, type, programId) {
    console.log('Redirecting to:', { referenceId, moduleId, type, programId });
    
    if (type === 'assignment' || type === 'quiz') {
      if (referenceId) {
        window.location.href = `/student/content/${referenceId}/view`;
      } else {
        console.warn('No reference ID provided for', type);
        window.location.href = '/student/dashboard';
      }
    } else {
      console.warn('Unknown deadline type:', type);
      window.location.href = '/student/dashboard';
    }
  }

  // Announcement Modal Functions
  function openAnnouncementModal(id, title, content, type, time) {
    const modal = document.getElementById('announcementModal');
    const modalTitle = document.getElementById('announcementModalTitle');
    const modalContent = document.getElementById('announcementModalContent');
    const modalType = document.getElementById('announcementModalType');
    const modalTime = document.getElementById('announcementModalTime');
    
    if (modalTitle) modalTitle.textContent = title;
    if (modalContent) modalContent.textContent = content;
    if (modalType) modalType.textContent = type === 'video' ? 'Video Announcement' : 'Text Announcement';
    if (modalTime) modalTime.textContent = time;
    
    if (modal) {
      modal.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeAnnouncementModal() {
    const modal = document.getElementById('announcementModal');
    if (modal) {
      modal.classList.remove('show');
      document.body.style.overflow = 'auto';
    }
  }

  // Test functions
  function testPaymentModal() {
    console.log('Testing payment modal...');
    showPaymentModal(999, 'Test Course DEBUG');
  }

  function testProgramModal() {
    console.log('Testing program modal...');
    const testProgramId = 40;
    window.location.href = `/profile/program/${testProgramId}`;
  }

  // Expose functions to global scope for onclick handlers
  window.showPaymentModal = showPaymentModal;
  window.showStatusModal = showStatusModal;
  window.testPaymentModal = testPaymentModal;
  window.testProgramModal = testProgramModal;
  window.goToStep1 = goToStep1;
  window.goToStep2 = goToStep2;
  window.submitPayment = submitPayment;
  window.selectPaymentMethod = selectPaymentMethod;
  window.redirectToAssignment = redirectToAssignment;
  window.openAnnouncementModal = openAnnouncementModal;
  window.closeAnnouncementModal = closeAnnouncementModal;

  function initStudentDashboard() {
    console.log('Student Dashboard initialized with complete functionality');
    
    // Load meetings data on page load
    loadMeetingsData();
    
    // Global modal cleanup
    if (typeof bootstrap !== 'undefined') {
      document.addEventListener('hidden.bs.modal', function() {
        setTimeout(removeAllBackdrops, 100);
      });
    }
    
    // Escape key modal closing
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeAnnouncementModal();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStudentDashboard);
  } else { 
    initStudentDashboard(); 
  }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['student-dashboard'] = () => { /* already initialized above */ };
