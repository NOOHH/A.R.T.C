

<?php $__env->startSection('title', 'Messages'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Messages</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="chat-sidebar">
                                <h6>Contacts</h6>
                                <div class="contact-list">
                                    <!-- Contacts will be loaded here -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="chat-window">
                                <div class="chat-messages" id="chatMessages">
                                    <div class="text-center text-muted p-5">
                                        Select a contact to start messaging
                                    </div>
                                </div>
                                <div class="chat-input">
                                    <form id="messageForm">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Type your message..." id="messageInput">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-sidebar {
    border-right: 1px solid #ddd;
    height: 500px;
    overflow-y: auto;
}

.chat-window {
    height: 500px;
    display: flex;
    flex-direction: column;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    border: 1px solid #ddd;
    border-bottom: none;
}

.chat-input {
    border: 1px solid #ddd;
    border-top: none;
    padding: 1rem;
}

.contact-list {
    padding: 0.5rem;
}

.contact-item {
    padding: 0.75rem;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.contact-item:hover {
    background-color: #f8f9fa;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('professor.professor-layouts.professor-layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views\professor\chat\index.blade.php ENDPATH**/ ?>