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
    
    // Close all Bootstrap modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
      modal.style.display = 'none';
      modal.classList.remove('show');
      
      // Dispose Bootstrap instances
      if (typeof bootstrap !== 'undefined') {
        const instance = bootstrap.Modal.getInstance(modal);
        if (instance) {
          instance.dispose();
        }
      }
    });
    
    // Close custom modals
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal && paymentModal.getAttribute('data-payment-modal-active') === 'true') {
      closeCustomModal(paymentModal);
    }
    
    const announcementModal = document.getElementById('announcementModal');
    if (announcementModal && announcementModal.getAttribute('data-announcement-modal-active') === 'true') {
      closeCustomAnnouncementModal(announcementModal);
    }
    
    // Remove all backdrops (both Bootstrap and custom)
    removeAllBackdrops();
    const customBackdrop = document.getElementById('customModalBackdrop');
    if (customBackdrop) {
      customBackdrop.remove();
    }
    
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
    
    // Create custom modal implementation
    showCustomModal(paymentModalElement);
  }
  
  // Custom Modal Implementation
  function showCustomModal(modalElement) {
    console.log('Showing custom modal...');
    
    // Remove any existing Bootstrap instances
    if (typeof bootstrap !== 'undefined') {
      const existingInstance = bootstrap.Modal.getInstance(modalElement);
      if (existingInstance) {
        existingInstance.dispose();
      }
    }
    
    // Clean up any existing custom backdrops
    const existingBackdrops = document.querySelectorAll('.custom-modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());
    
    // Set modal as active
    modalElement.setAttribute('data-payment-modal-active', 'true');
    
    // Create custom backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'custom-modal-backdrop';
    backdrop.id = 'customModalBackdrop';
    backdrop.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: auto;
    `;
    
    // Style the modal - ensure it's positioned correctly
    modalElement.style.cssText = `
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1000001;
    display: block;
    opacity: 0;
    transform: scale(0.7);
    transition: all 0.3s ease;
    background: white;
    border-radius: 0; /* no rounded corners for fullscreen */
    box-shadow: none; /* remove shadow if you want flush edges */
    width: 100vw;
    height: 110vh;
    overflow: auto;
    margin: 0;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
  `;
  
    
    // Remove Bootstrap classes that might interfere
    modalElement.classList.remove('fade', 'show');
    modalElement.classList.add('custom-modal');
    
    // Add backdrop to body first
    document.body.appendChild(backdrop);
    
    // Add modal to body (not to backdrop) to avoid nesting issues
    document.body.appendChild(modalElement);
    
    // Position the modal absolutely within the backdrop
    modalElement.style.position = 'fixed';
    modalElement.style.top = '50%';
    modalElement.style.left = '50%';
    modalElement.style.transform = 'translate(-50%, -50%) scale(0.7)';
    modalElement.style.zIndex = '1000001';
    
    // Add body styles
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '0';
    
    // Animate in
    requestAnimationFrame(() => {
      backdrop.style.opacity = '1';
      modalElement.style.opacity = '1';
      modalElement.style.transform = 'translate(-50%, -50%) scale(1)';
    });
    
    // Add click handlers
    backdrop.addEventListener('click', function(e) {
      if (e.target === backdrop) {
        closeCustomModal(modalElement);
      }
    });
    
    // Add escape key handler
    const escapeHandler = function(e) {
      if (e.key === 'Escape') {
        closeCustomModal(modalElement);
        document.removeEventListener('keydown', escapeHandler);
      }
    };
    document.addEventListener('keydown', escapeHandler);
    
    // Add close button handler
    const closeBtn = modalElement.querySelector('.btn-close');
    if (closeBtn) {
      closeBtn.onclick = function() {
        closeCustomModal(modalElement);
      };
    }
    
    // Focus the modal
    modalElement.focus();
    
    console.log('Custom modal shown successfully');
  }
  
  function closeCustomModal(modalElement) {
    console.log('Closing custom modal...');
    
    const backdrop = document.getElementById('customModalBackdrop');
    if (!backdrop) return;
    
    // Animate out
    backdrop.style.opacity = '0';
    modalElement.style.opacity = '0';
    modalElement.style.transform = 'translate(-50%, -50%) scale(0.7)';
    
    // Remove after animation
    setTimeout(() => {
      // Reset modal styles
      modalElement.style.cssText = '';
      modalElement.classList.remove('custom-modal');
      modalElement.classList.add('fade');
      
      // Remove backdrop
      if (backdrop && backdrop.parentNode) {
        backdrop.parentNode.removeChild(backdrop);
      }
      
      // Reset body styles
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
      
      // Clean up
      resetPaymentModal();
      
          console.log('Custom modal closed successfully');
  }, 300);
}

  // Custom Announcement Modal Implementation
  function showCustomAnnouncementModal(modalElement) {
    console.log('Showing custom announcement modal...');
    
    // Remove any existing Bootstrap instances
    if (typeof bootstrap !== 'undefined') {
      const existingInstance = bootstrap.Modal.getInstance(modalElement);
      if (existingInstance) {
        existingInstance.dispose();
      }
    }
    
    // Clean up any existing custom backdrops
    const existingBackdrops = document.querySelectorAll('.custom-announcement-modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());
    
    // Set modal as active
    modalElement.setAttribute('data-announcement-modal-active', 'true');
    
    // Create custom backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'custom-announcement-modal-backdrop';
    backdrop.id = 'customAnnouncementModalBackdrop';
    backdrop.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 1000000;
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: auto;
    `;
    
    // Style the modal - ensure it's positioned correctly
    modalElement.style.cssText = `
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000001;
      display: block;
      opacity: 0;
      transform: scale(0.7);
      transition: all 0.3s ease;
      background: white;
      border-radius: 0; /* no rounded corners for fullscreen */
      box-shadow: none; /* remove shadow if you want flush edges */
      width: 100vw;
      height: 110vh;
      overflow: auto;
      margin: 0;
      padding: 0;
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
    `;
    
    // Remove Bootstrap classes that might interfere
    modalElement.classList.remove('fade', 'show');
    modalElement.classList.add('custom-announcement-modal');
    
    // Add backdrop to body first
    document.body.appendChild(backdrop);
    
    // Add modal to body (not to backdrop) to avoid nesting issues
    document.body.appendChild(modalElement);
    
    // Position the modal absolutely within the backdrop
    modalElement.style.position = 'fixed';
    modalElement.style.top = '50%';
    modalElement.style.left = '50%';
    modalElement.style.transform = 'translate(-50%, -50%) scale(0.7)';
    modalElement.style.zIndex = '1000001';
    
    // Add body styles
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '0';
    
    // Animate in
    requestAnimationFrame(() => {
      backdrop.style.opacity = '1';
      modalElement.style.opacity = '1';
      modalElement.style.transform = 'translate(-50%, -50%) scale(1)';
    });
    
    // Add click handlers
    backdrop.addEventListener('click', function(e) {
      if (e.target === backdrop) {
        closeCustomAnnouncementModal(modalElement);
      }
    });
    
    // Add escape key handler
    const escapeHandler = function(e) {
      if (e.key === 'Escape') {
        closeCustomAnnouncementModal(modalElement);
        document.removeEventListener('keydown', escapeHandler);
      }
    };
    document.addEventListener('keydown', escapeHandler);
    
    // Add close button handler
    const closeBtn = modalElement.querySelector('.btn-close');
    if (closeBtn) {
      closeBtn.onclick = function() {
        closeCustomAnnouncementModal(modalElement);
      };
    }
    
    // Focus the modal
    modalElement.focus();
    
    console.log('Custom announcement modal shown successfully');
  }
  
  function closeCustomAnnouncementModal(modalElement) {
    console.log('Closing custom announcement modal...');
    
    const backdrop = document.getElementById('customAnnouncementModalBackdrop');
    if (!backdrop) return;
    
    // Animate out
    backdrop.style.opacity = '0';
    modalElement.style.opacity = '0';
    modalElement.style.transform = 'translate(-50%, -50%) scale(0.7)';
    
    // Remove after animation
    setTimeout(() => {
      // Reset modal styles
      modalElement.style.cssText = '';
      modalElement.classList.remove('custom-announcement-modal');
      modalElement.classList.add('fade');
      
      // Remove backdrop
      if (backdrop && backdrop.parentNode) {
        backdrop.parentNode.removeChild(backdrop);
      }
      
      // Reset body styles
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
      
      // Clean up
      modalElement.removeAttribute('data-announcement-modal-active');
      
      console.log('Custom announcement modal closed successfully');
    }, 300);
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
    
    // Remove protection attribute
    const paymentModalElement = document.getElementById('paymentModal');
    if (paymentModalElement) {
      paymentModalElement.removeAttribute('data-payment-modal-active');
    }
  }

  function removeAllBackdrops() {
    // Remove all Bootstrap modal backdrops
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
      backdrop.remove();
    });
    
    // Remove custom modal backdrops
    const customBackdrop = document.getElementById('customModalBackdrop');
    if (customBackdrop) {
      customBackdrop.remove();
    }
    
    const customAnnouncementBackdrop = document.getElementById('customAnnouncementModalBackdrop');
    if (customAnnouncementBackdrop) {
      customAnnouncementBackdrop.remove();
    }
    
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
    console.log('openAnnouncementModal called with:', id, title, content, type, time);
    
    const modalElement = document.getElementById('announcementModal');
    if (!modalElement) {
      console.error('Announcement modal element not found');
      return;
    }
    
    // Set modal content
    const modalTitle = document.getElementById('announcementModalTitle');
    const modalContent = document.getElementById('announcementModalContent');
    const modalType = document.getElementById('announcementModalType');
    const modalTime = document.getElementById('announcementModalTime');
    const modalLabel = document.getElementById('announcementModalLabel');
    
    if (modalTitle) modalTitle.textContent = title;
    if (modalContent) modalContent.textContent = content;
    if (modalType) {
      const typeText = type === 'video' ? 'Video Announcement' : 'Text Announcement';
      modalType.innerHTML = `<i class="bi bi-megaphone"></i> ${typeText}`;
    }
    if (modalTime) modalTime.textContent = time;
    if (modalLabel) modalLabel.textContent = 'Announcement';
    
    // Create custom modal implementation
    showCustomAnnouncementModal(modalElement);
  }

  function closeAnnouncementModal() {
    const modalElement = document.getElementById('announcementModal');
    if (modalElement) {
      closeCustomAnnouncementModal(modalElement);
    }
  }

  // Test functions
  function testPaymentModal() {
    console.log('Testing payment modal...');
    showPaymentModal(999, 'Test Course DEBUG');
  }
  
  // Debug function to check z-index conflicts
  function debugZIndexConflicts() {
    console.log('🔍 Checking for z-index conflicts...');
    
    const elements = document.querySelectorAll('*');
    const highZIndexElements = [];
    
    elements.forEach(el => {
      const zIndex = window.getComputedStyle(el).zIndex;
      if (zIndex !== 'auto' && parseInt(zIndex) > 1000) {
        highZIndexElements.push({
          element: el,
          zIndex: zIndex,
          tagName: el.tagName,
          className: el.className,
          id: el.id
        });
      }
    });
    
    console.log('High z-index elements found:', highZIndexElements);
    
    // Check if payment modal exists and its z-index
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
      const modalZIndex = window.getComputedStyle(paymentModal).zIndex;
      console.log('Payment modal z-index:', modalZIndex);
      console.log('Payment modal display:', window.getComputedStyle(paymentModal).display);
      console.log('Payment modal visibility:', window.getComputedStyle(paymentModal).visibility);
      console.log('Payment modal active attribute:', paymentModal.getAttribute('data-payment-modal-active'));
    }
    
    return highZIndexElements;
  }
  
  // Debug function to monitor modal state
  function debugModalState() {
    console.log('🔍 Debugging modal state...');
    
    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
      console.log('Payment Modal State:');
      console.log('- Display:', window.getComputedStyle(paymentModal).display);
      console.log('- Visibility:', window.getComputedStyle(paymentModal).visibility);
      console.log('- Z-index:', window.getComputedStyle(paymentModal).zIndex);
      console.log('- Classes:', paymentModal.className);
      console.log('- Active attribute:', paymentModal.getAttribute('data-payment-modal-active'));
      console.log('- Bootstrap instance:', bootstrap.Modal.getInstance(paymentModal));
      
      // Check for overlaying elements
      const rect = paymentModal.getBoundingClientRect();
      const elementsAtPosition = document.elementsFromPoint(
        rect.left + rect.width / 2,
        rect.top + rect.height / 2
      );
      
      console.log('Elements at modal center:', elementsAtPosition);
    }
    
    // Check all modals
    const allModals = document.querySelectorAll('.modal');
    console.log('All modals found:', allModals.length);
    allModals.forEach((modal, index) => {
      console.log(`Modal ${index + 1}:`, {
        id: modal.id,
        display: window.getComputedStyle(modal).display,
        zIndex: window.getComputedStyle(modal).zIndex,
        classes: modal.className
      });
    });
  }
  
  window.debugModalState = debugModalState;

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
  window.debugZIndexConflicts = debugZIndexConflicts;
  window.emergencyCleanup = emergencyCleanup;
  
  // Function to properly close payment modal
  function closePaymentModal() {
    const paymentModalElement = document.getElementById('paymentModal');
    if (paymentModalElement) {
      closeCustomModal(paymentModalElement);
    }
  }
  
  window.closePaymentModal = closePaymentModal;

  function initStudentDashboard() {
    console.log('Student Dashboard initialized with complete functionality');
    
    // Load meetings data on page load
    loadMeetingsData();
    
    // Escape key modal closing - only for announcement modal
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeAnnouncementModal();
      }
    });
    
    // Custom modal protection - prevent other scripts from interfering
    document.addEventListener('click', function(e) {
      if (e.target && e.target.onclick && e.target.onclick.toString().includes('showPaymentModal')) {
        console.log('Payment button clicked - preparing custom modal');
        // Remove any existing Bootstrap backdrops that might interfere
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
          backdrop.remove();
        });
        document.body.classList.remove('modal-open');
      }
    });
    
    // Prevent Bootstrap modals from interfering with custom modal
    if (typeof bootstrap !== 'undefined') {
      document.addEventListener('show.bs.modal', function(e) {
        const paymentModal = document.getElementById('paymentModal');
        if (paymentModal && paymentModal.getAttribute('data-payment-modal-active') === 'true') {
          console.log('⚠️ Bootstrap modal trying to show while custom payment modal is active - preventing');
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStudentDashboard);
  } else { 
    initStudentDashboard(); 
  }
})();

window.__PageInits = window.__PageInits || {};
window.__PageInits['student-dashboard'] = () => { /* already initialized above */ };
