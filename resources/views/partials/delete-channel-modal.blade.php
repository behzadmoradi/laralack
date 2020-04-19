<div class="modal fade" id="delete-channel-modal" tabindex="-1" role="dialog" aria-labelledby="delete-channel-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="delete-channel-modal-label">Delete channel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>If you delete this channel, all its messages will be gone and this action is <b>not</b> revertable. Are you sure you want to delete it?</div>
                <br>
                <button type="button" class="btn btn-primary" id="delete-channel-btn">
                    Yes
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </button>
                <small id="channel-modal-delete-success-msg"></small>
                <small id="channel-modal-delete-error-msg"></small>
            </div>
        </div>
    </div>
</div>