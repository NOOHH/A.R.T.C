/* Enhanced PDF Viewer Integration Script */

// Add enhanced PDF viewer functions to admin modules
function updateAdminPDFViewer() {
    // Include the enhanced PDF viewer component
    const head = document.head || document.getElementsByTagName('head')[0];
    const existingComponent = document.getElementById('enhanced-pdf-viewer-styles');
    
    if (!existingComponent) {
        const styleElement = document.createElement('style');
        styleElement.id = 'enhanced-pdf-viewer-styles';
        styleElement.textContent = `
            .pdf-viewer-enhanced,
            .document-viewer-enhanced,
            .file-viewer-enhanced {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                overflow: hidden;
                background: white;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .pdf-content,
            .document-content {
                background: #f8f9fa;
            }

            .pdf-viewer-enhanced iframe,
            .document-viewer-enhanced iframe {
                transition: opacity 0.3s ease;
            }

            .content-frame-enhanced {
                width: 100%;
                height: 600px;
                border: none;
                border-radius: 0 0 0.375rem 0.375rem;
            }

            .viewer-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 1rem;
                border-radius: 0.375rem 0.375rem 0 0;
            }

            .viewer-controls {
                background: #f8f9fa;
                padding: 0.75rem;
                border-bottom: 1px solid #dee2e6;
            }

            @media (max-width: 768px) {
                .viewer-header {
                    padding: 0.75rem;
                }
                
                .content-frame-enhanced {
                    height: 400px;
                }
                
                .btn-group .btn {
                    font-size: 0.8rem;
                    padding: 0.375rem 0.75rem;
                }
            }
        `;
        head.appendChild(styleElement);
    }
}

// Enhanced createBootstrapPDFViewer function for admin module
function createEnhancedAdminPDFViewer(fileUrl, fileName, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const fileExtension = fileName.split('.').pop().toLowerCase();
    
    let viewerHtml = '';
    
    if (fileExtension === 'pdf') {
        viewerHtml = `
            <div class="pdf-viewer-enhanced">
                <div class="viewer-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="bi bi-file-pdf"></i> ${fileName}</h6>
                            <small class="opacity-75">PDF Document</small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <a href="${fileUrl}" target="_blank" class="btn btn-light btn-sm">
                                <i class="bi bi-arrows-fullscreen"></i>
                            </a>
                            <a href="${fileUrl}" download class="btn btn-light btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="viewer-controls">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Use mouse wheel to zoom, drag to pan</small>
                        <div class="btn-group btn-group-sm">
                            <button onclick="refreshPDFViewer('${containerId}')" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pdf-content">
                    <iframe 
                        id="pdf-frame-${containerId}"
                        class="content-frame-enhanced"
                        src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1&view=FitH" 
                        onload="handlePDFLoadSuccess(this)"
                        onerror="handlePDFLoadError(this, '${fileUrl}', '${fileName}', '${containerId}')">
                        <p>Loading PDF...</p>
                    </iframe>
                </div>
                <div id="pdf-fallback-${containerId}" class="d-none">
                    <div class="text-center p-5">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">PDF Preview Unavailable</h5>
                        <p class="text-muted">Your browser may not support inline PDF viewing.</p>
                        <div class="btn-group">
                            <a href="${fileUrl}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-box-arrow-up-right"></i> Open in New Tab
                            </a>
                            <a href="${fileUrl}" download class="btn btn-outline-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else if (['doc', 'docx', 'ppt', 'pptx'].includes(fileExtension)) {
        const docType = ['ppt', 'pptx'].includes(fileExtension) ? 'Presentation' : 'Document';
        viewerHtml = `
            <div class="document-viewer-enhanced">
                <div class="viewer-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="bi bi-file-earmark-text"></i> ${fileName}</h6>
                            <small class="opacity-75">${fileExtension.toUpperCase()} ${docType}</small>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button onclick="openInOfficeViewer('${fileUrl}')" class="btn btn-light btn-sm">
                                <i class="bi bi-eye"></i>
                            </button>
                            <a href="${fileUrl}" download class="btn btn-light btn-sm">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="document-content">
                    <iframe 
                        class="content-frame-enhanced"
                        src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}" 
                        onload="handleDocumentLoadSuccess(this)"
                        onerror="handleDocumentLoadError(this, '${fileUrl}', '${fileName}')">
                        <p>Loading document...</p>
                    </iframe>
                </div>
            </div>
        `;
    } else {
        viewerHtml = `
            <div class="file-viewer-enhanced">
                <div class="viewer-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><i class="bi bi-file-earmark"></i> ${fileName}</h6>
                            <small class="opacity-75">${fileExtension.toUpperCase()} File</small>
                        </div>
                        <a href="${fileUrl}" download class="btn btn-light btn-sm">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
                <div class="text-center p-5">
                    <i class="bi bi-file-earmark" style="font-size: 4rem; color: #6c757d;"></i>
                    <h5 class="mt-3">Preview Not Available</h5>
                    <p class="text-muted">This file type cannot be previewed in the browser.</p>
                    <a href="${fileUrl}" download class="btn btn-primary">
                        <i class="bi bi-download"></i> Download ${fileName}
                    </a>
                </div>
            </div>
        `;
    }
    
    container.innerHTML = viewerHtml;
}

// Helper functions
function handlePDFLoadSuccess(iframe) {
    console.log('PDF loaded successfully');
    iframe.style.opacity = '1';
}

function handlePDFLoadError(iframe, fileUrl, fileName, containerId) {
    console.warn('PDF iframe failed to load, showing fallback');
    const fallback = document.getElementById(`pdf-fallback-${containerId}`);
    const content = iframe.closest('.pdf-content');
    if (fallback && content) {
        content.classList.add('d-none');
        fallback.classList.remove('d-none');
    }
}

function handleDocumentLoadSuccess(iframe) {
    console.log('Document loaded successfully');
    iframe.style.opacity = '1';
}

function handleDocumentLoadError(iframe, fileUrl, fileName) {
    console.warn('Document iframe failed to load');
    const content = iframe.closest('.document-content');
    if (content) {
        content.innerHTML = `
            <div class="text-center p-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Document Preview Unavailable</h5>
                <p class="text-muted">Unable to preview this document online.</p>
                <a href="${fileUrl}" download class="btn btn-primary">
                    <i class="bi bi-download"></i> Download ${fileName}
                </a>
            </div>
        `;
    }
}

function refreshPDFViewer(containerId) {
    const iframe = document.getElementById(`pdf-frame-${containerId}`);
    if (iframe) {
        iframe.src = iframe.src; // Reload the iframe
    }
}

function openInOfficeViewer(fileUrl) {
    const viewerUrl = `https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}`;
    window.open(viewerUrl, '_blank');
}

// Initialize enhanced PDF viewer on page load
document.addEventListener('DOMContentLoaded', function() {
    updateAdminPDFViewer();
    console.log('Enhanced PDF viewer initialized');
});
