/* Student Course Layout and PDF Viewer Fixes */

// Enhanced PDF viewer for student course
function createStudentPDFViewer(fileUrl, fileName, containerId) {
    const container = document.getElementById(containerId) || document.querySelector('.content-viewer');
    if (!container) return;
    
    const fileExtension = fileName ? fileName.split('.').pop().toLowerCase() : 'pdf';
    
    let viewerHtml = `
        <div class="student-pdf-viewer">
            <div class="pdf-header d-flex justify-content-between align-items-center p-3 bg-primary text-white">
                <div class="file-info">
                    <h6 class="mb-0"><i class="bi bi-file-pdf"></i> ${fileName || 'Document'}</h6>
                    <small class="opacity-75">PDF Document</small>
                </div>
                <div class="pdf-controls">
                    <div class="btn-group btn-group-sm">
                        <a href="${fileUrl}" target="_blank" class="btn btn-light btn-sm">
                            <i class="bi bi-arrows-fullscreen"></i> Full Screen
                        </a>
                        <a href="${fileUrl}" download class="btn btn-light btn-sm">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                </div>
            </div>
            <div class="pdf-content-wrapper" style="height: 70vh; position: relative; overflow: hidden;">
                <iframe 
                    class="pdf-frame w-100 h-100"
                    src="${fileUrl}#toolbar=1&navpanes=0&scrollbar=1&view=FitH" 
                    style="border: none;"
                    onload="handleStudentPDFLoad(this)"
                    onerror="handleStudentPDFError(this, '${fileUrl}', '${fileName}')">
                </iframe>
                <div class="pdf-loading position-absolute top-50 start-50 translate-middle text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Loading PDF...</p>
                </div>
            </div>
            <div class="pdf-fallback d-none">
                <div class="text-center p-5">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">PDF Preview Not Available</h5>
                    <p class="text-muted">Your browser may not support inline PDF viewing.</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                        </a>
                        <a href="${fileUrl}" download class="btn btn-outline-primary">
                            <i class="bi bi-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = viewerHtml;
    
    // Hide loading after a timeout
    setTimeout(() => {
        const loading = container.querySelector('.pdf-loading');
        if (loading) loading.style.display = 'none';
    }, 3000);
}

function handleStudentPDFLoad(iframe) {
    console.log('Student PDF loaded successfully');
    const loading = iframe.closest('.pdf-content-wrapper').querySelector('.pdf-loading');
    if (loading) loading.style.display = 'none';
}

function handleStudentPDFError(iframe, fileUrl, fileName) {
    console.warn('Student PDF failed to load, showing fallback');
    const wrapper = iframe.closest('.pdf-content-wrapper');
    const fallback = iframe.closest('.student-pdf-viewer').querySelector('.pdf-fallback');
    
    if (wrapper && fallback) {
        wrapper.classList.add('d-none');
        fallback.classList.remove('d-none');
    }
}

// Enhanced layout fixes for student course
function fixStudentCourseLayout() {
    // Add improved CSS for student course layout
    const styleElement = document.createElement('style');
    styleElement.id = 'student-course-layout-fixes';
    styleElement.textContent = `
        /* Student Course Layout Fixes */
        .student-course-container {
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .course-header {
            flex-shrink: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .course-content-area {
            flex: 1;
            overflow: hidden;
            display: flex;
        }
        
        .course-sidebar {
            width: 300px;
            flex-shrink: 0;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
            height: 100%;
        }
        
        .course-main-content {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .content-viewer {
            flex: 1;
            overflow: auto;
            background: white;
            padding: 0;
        }
        
        .module-list {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
            padding: 1rem;
        }
        
        .module-item {
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            border-radius: 0.5rem;
            overflow: hidden;
            background: white;
        }
        
        .module-header {
            background: linear-gradient(45deg, #6c5ce7, #74b9ff);
            color: white;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .module-header:hover {
            background: linear-gradient(45deg, #5a4fcf, #5ca7e8);
        }
        
        .course-list {
            background: #f8f9fa;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .course-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        
        .course-item:hover {
            background: #e9ecef;
        }
        
        .content-list {
            background: white;
            border-left: 3px solid #007bff;
        }
        
        .content-item {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .content-item:hover {
            background: #f8f9fa;
            padding-left: 1.5rem;
        }
        
        .content-item.active {
            background: #e3f2fd;
            border-left: 3px solid #2196f3;
            font-weight: 500;
        }
        
        .student-pdf-viewer {
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
        }
        
        .pdf-header {
            flex-shrink: 0;
            z-index: 10;
        }
        
        .pdf-content-wrapper {
            flex: 1;
            position: relative;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .course-content-area {
                flex-direction: column;
            }
            
            .course-sidebar {
                width: 100%;
                height: 250px;
                order: 2;
            }
            
            .course-main-content {
                order: 1;
                height: calc(100vh - 350px);
            }
            
            .module-list {
                max-height: 200px;
                padding: 0.5rem;
            }
            
            .pdf-content-wrapper {
                height: 50vh !important;
            }
        }
        
        /* Scrollbar styling */
        .module-list::-webkit-scrollbar,
        .course-list::-webkit-scrollbar,
        .content-viewer::-webkit-scrollbar {
            width: 6px;
        }
        
        .module-list::-webkit-scrollbar-track,
        .course-list::-webkit-scrollbar-track,
        .content-viewer::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .module-list::-webkit-scrollbar-thumb,
        .course-list::-webkit-scrollbar-thumb,
        .content-viewer::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        
        .module-list::-webkit-scrollbar-thumb:hover,
        .course-list::-webkit-scrollbar-thumb:hover,
        .content-viewer::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }
    `;
    
    document.head.appendChild(styleElement);
}

// Initialize student course fixes
document.addEventListener('DOMContentLoaded', function() {
    fixStudentCourseLayout();
    console.log('Student course layout fixes applied');
});
