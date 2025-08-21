<!-- Enhanced PDF Viewer Component -->
<script>
function createBootstrapPDFViewer(fileUrl, fileName, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const fileExtension = fileName.split('.').pop().toLowerCase();
    
    let viewerHtml = '';
    
    if (fileExtension === 'pdf') {
        viewerHtml = `
            <div class="pdf-viewer-enhanced">
                <div class="pdf-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                    <div class="file-info">
                        <h6 class="mb-0"><i class="bi bi-file-pdf text-danger"></i> ${fileName}</h6>
                        <small class="text-muted">PDF Document</small>
                    </div>
                    <div class="pdf-controls">
                        <div class="btn-group btn-group-sm">
                            <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-fullscreen"></i> Full Screen
                            </a>
                            <a href="${fileUrl}" download class="btn btn-outline-success">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                <div class="pdf-content" style="height: 600px; overflow: auto;">
                    <iframe 
                        src="${fileUrl}#toolbar=1&navpanes=1&scrollbar=1" 
                        style="width: 100%; height: 100%; border: none;"
                        onload="handlePDFLoad(this)"
                        onerror="handlePDFError(this, '${fileUrl}', '${fileName}')">
                    </iframe>
                </div>
                <div class="pdf-fallback d-none">
                    <div class="text-center p-4">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">PDF Preview Not Available</h5>
                        <p class="text-muted">Your browser may not support inline PDF viewing.</p>
                        <div class="btn-group">
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
    } else if (['doc', 'docx', 'ppt', 'pptx'].includes(fileExtension)) {
        viewerHtml = `
            <div class="document-viewer-enhanced">
                <div class="document-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                    <div class="file-info">
                        <h6 class="mb-0"><i class="bi bi-file-earmark-text text-primary"></i> ${fileName}</h6>
                        <small class="text-muted">${fileExtension.toUpperCase()} Document</small>
                    </div>
                    <div class="document-controls">
                        <div class="btn-group btn-group-sm">
                            <button onclick="openInOfficeViewer('${fileUrl}')" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> View Online
                            </button>
                            <a href="${fileUrl}" download class="btn btn-outline-success">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                <div class="document-content" style="height: 600px;">
                    <iframe 
                        src="https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}" 
                        style="width: 100%; height: 100%; border: none;"
                        onload="handleDocumentLoad(this)"
                        onerror="handleDocumentError(this, '${fileUrl}', '${fileName}')">
                    </iframe>
                </div>
            </div>
        `;
    } else {
        viewerHtml = `
            <div class="file-viewer-enhanced">
                <div class="file-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                    <div class="file-info">
                        <h6 class="mb-0"><i class="bi bi-file-earmark text-secondary"></i> ${fileName}</h6>
                        <small class="text-muted">${fileExtension.toUpperCase()} File</small>
                    </div>
                    <div class="file-controls">
                        <a href="${fileUrl}" download class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                </div>
                <div class="file-content text-center p-4">
                    <i class="bi bi-file-earmark" style="font-size: 4rem; color: #6c757d;"></i>
                    <h5 class="mt-3">File Preview Not Available</h5>
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

function handlePDFLoad(iframe) {
    console.log('PDF loaded successfully');
}

function handlePDFError(iframe, fileUrl, fileName) {
    console.warn('PDF iframe failed to load, showing fallback');
    const fallback = iframe.closest('.pdf-viewer-enhanced').querySelector('.pdf-fallback');
    const content = iframe.closest('.pdf-content');
    if (fallback && content) {
        content.classList.add('d-none');
        fallback.classList.remove('d-none');
    }
}

function handleDocumentLoad(iframe) {
    console.log('Document loaded successfully');
}

function handleDocumentError(iframe, fileUrl, fileName) {
    console.warn('Document iframe failed to load, showing download option');
    const content = iframe.closest('.document-content');
    if (content) {
        content.innerHTML = `
            <div class="text-center p-4">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Document Preview Not Available</h5>
                <p class="text-muted">Unable to preview this document online.</p>
                <a href="${fileUrl}" download class="btn btn-primary">
                    <i class="bi bi-download"></i> Download ${fileName}
                </a>
            </div>
        `;
    }
}

function openInOfficeViewer(fileUrl) {
    const viewerUrl = `https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(window.location.origin + fileUrl)}`;
    window.open(viewerUrl, '_blank');
}
</script>

<style>
.pdf-viewer-enhanced,
.document-viewer-enhanced,
.file-viewer-enhanced {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    background: white;
}

.pdf-content,
.document-content {
    background: #f8f9fa;
}

.pdf-viewer-enhanced iframe,
.document-viewer-enhanced iframe {
    transition: opacity 0.3s ease;
}

.btn-group-sm .btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .pdf-header,
    .document-header,
    .file-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .pdf-content,
    .document-content {
        height: 400px !important;
    }
}
</style>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\components\enhanced-pdf-viewer.blade.php ENDPATH**/ ?>