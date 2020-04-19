<div class="modal fade" id="delete-chat-modal" tabindex="-1" role="dialog" aria-labelledby="delete-chat-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-chat-modal-label">Delete chat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>Are you sure you want to delete this conversation?</div>
                <br>
                <button type="button" class="btn btn-primary" id="delete-chat-modal-btn">
                    Yes
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <small id="delete-chat-modal-success-msg"></small>
                <small id="delete-chat-modal-error-msg"></small>
            </div>
        </div>
    </div>
</div>