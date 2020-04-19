<div class="modal fade" id="channel-modal" tabindex="-1" role="dialog" aria-labelledby="channel-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="channel-modal-label">Add a new channel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="channel-modal-body-txt">Organize your channels around a topic based on what your communications are about; for example <strong>#copyrighting</strong>.</div>
                <form id="channel-form">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="type" value="">
                    <div class="form-group">
                        <label for="channel-name" class="col-form-label">Channel name</label>
                        <input type="text" class="form-control" name="name" id="channel-name">
                        <small class="error-msg" id="channel-name-error-msg"></small>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Description <span class="optional-text">(optional)</span></label>
                        <textarea class="form-control" name="description" id="channel-description"></textarea>
                        <small class="error-msg" id="channel-description-error-msg"></small>
                    </div>
                    <button type="button" class="btn btn-primary" id="submit-channel-btn">
                        Create
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                    <small id="channel-modal-success-msg"></small>
                </form>
            </div>
        </div>
    </div>
</div>