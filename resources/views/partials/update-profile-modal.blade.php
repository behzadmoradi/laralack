<div class="modal fade" id="update-profile-modal" tabindex="-1" role="dialog" aria-labelledby="update-profile-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="update-profile-modal-label">Edit your profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="update-profile-form" method="POST" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="channel-name" class="col-form-label">Full name</label> <span class="optional-text">(required)</span>
                                    <input type="text" class="form-control" name="name" id="name" value="{!! Auth::user()->name !!}">
                                    <small class="error-msg" id="name-error-msg"></small>
                                </div>
                                <div class="form-group">
                                    <label for="channel-name" class="col-form-label">Username</label> <span class="optional-text">(required)</span>
                                    <input type="text" class="form-control" name="username" id="username" value="{!! Auth::user()->username !!}">
                                    <small class="error-msg" id="username-error-msg"></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php if (Auth::user()->avatar) { ?>
                                    <img src="{{ Auth::user()->avatar }}" id="profile-avatar-wrapper">
                                <?php } else { ?>
                                        <img src="/img/avatars/default-avatar.png" id="profile-avatar-wrapper">
                                <?php } ?>
                                <div class="custom-file form-group">
                                    <input type="file" class="custom-file-input" name="avatar" id="profile-avatar-selector">
                                    <label class="custom-file-label" for="profile-avatar-selector">Choose file</label>
                                    <small id="avatar-update-success-msg"></small>
                                    <small class="error-msg" id="avatar-update-error-msg"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="channel-name" class="col-form-label">Email</label> <span class="optional-text">(required)</span>
                                    <input type="email" class="form-control" name="email" id="email" value="{{ Auth::user()->email }}">
                                    <small class="error-msg" id="email-error-msg"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="channel-name" class="col-form-label">New password</label>
                                    <input type="password" class="form-control" name="password" id="password" placeholder="Leave this empty if you don`t want to update" autocomplete="new-password">
                                    <small class="error-msg" id="password-error-msg"></small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="update-profile-btn">
                                    Update
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                </button>
                                <small id="update-form-success-msg"></small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>