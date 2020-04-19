<div class="modal fade" id="invite-people-modal" tabindex="-1" role="dialog" aria-labelledby="invite-people-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invite-people-modal-label">Invite people to <strong id="invitation-modal-channel-name">#copyrighting</strong> channel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="invite-people-modal-users-list"></div>
                <h5 id="invite-people-modal-add-new-users-heading">Add new users</h5>
                <form id="invitation-form">
                    <input type="hidden" name="id" value="">
                    <label>Email</label>
                    <div id="emails-wrapper"></div>
                    <button type="button" class="btn btn-link" id="add-another-email"><i class="far fa-plus-square"></i> Add another</button>
                    <button type="button" class="btn btn-primary" id="send-invitations-btn">
                        Send invitations
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    </button>
                    <small id="invite-people-modal-success-msg"></small>
                    <small id="invite-people-modal-error-msg"></small>
                </form>
            </div>
        </div>
    </div>
</div>