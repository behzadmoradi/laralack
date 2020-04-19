function getInvitedUsersSuccess(data) {
    $('#invite-people-modal-users-list').empty();
    if (data['data']['users'].length > 0) {
        var users = data['data']['users'];
        var usersTemplate = '';
        $('#invite-people-modal-users-list').append('<h5>List of invitees</h5><div id="invitee-list-empty">Nobody has access to this channel.</div>');
        for (var i = 0; i < users.length; i++) {
            usersTemplate += '<div class="form-row">' +
                '<div class="col-md-11">' +
                '<div>' + (users[i]['name'] ? users[i]['name'] + ' (' + users[i]['email'] + ')' : users[i]['email']) + '</div>' +
                '<small class="error-msg" id="invitation-error-msg"></small>' +
                '</div>' +
                '<div class="col-md-1 invitee-delete-btn-wrapper">' +
                '<button type="button" data-toggle="tooltip" data-placement="left" title="Are you sure you want to delete this user?" class="btn btn-link remove-invitee" data-id="' + users[i]['id'] + '" data-channel-id="' + data['data']['channel_id'] + '"><i class="far fa-times-circle"></i></button>' +
                '</div>' +
                '</div>';
        }
        $('#invite-people-modal-users-list').append(usersTemplate);
        $('[data-toggle="tooltip"]').tooltip();
    }
}

var invitationEmailCounter = 0;
$('body').on('click', '#invite-people-btn', function () {
    invitationEmailCounter = 0;
    $('#emails-wrapper').empty();
    var channelId = $(this).parent().parent().find('.channel-title').attr('data-id');
    var channelName = $(this).parent().parent().find('.channel-title').attr('data-name');
    $('#invite-people-modal #invitation-modal-channel-name').text('#' + channelName);
    $('#invite-people-modal #invitation-form :input[name="id"]').val(channelId);
    var firstEmailField = '<div class="form-row first-row">' +
        '<div class="form-group col-md-11">' +
        '<input type="email" class="form-control" name="email[]" placeholder="name@example.com">' +
        '<small class="error-msg" id="invitation-error-msg-' + invitationEmailCounter + '"></small>' +
        '</div>' +
        '</div>';
    $('#emails-wrapper').append(firstEmailField);
    $("#send-invitations-btn").attr('disabled', false);
    $('#add-another-email').css('display', 'block');
    $('#invite-people-modal #invite-people-modal-success-msg').text('');
    $('#invite-people-modal #invite-people-modal-error-msg').text('');
    $('#send-invitations-btn').html('Send invitations' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    get('/channels/' + channelId + '/users', getInvitedUsersSuccess);
});

$('#add-another-email').click(function () {
    invitationEmailCounter++;
    var newEmail = '<div class="form-row">' +
        '<div class="form-group col-md-11">' +
        '<input type="email" class="form-control" name="email[]" placeholder="name@example.com">' +
        '<small class="error-msg" id="invitation-error-msg-' + invitationEmailCounter + '"></small>' +
        '</div>' +
        '<div class="form-group col-md-1 email-delete-btn-wrapper">' +
        '<button type="button" class="btn btn-link remove-email"><i class="far fa-times-circle"></i></button>' +
        '</div>' +
        '</div>';
    $('#emails-wrapper').append(newEmail);
    var deleteBtn = '<div class="form-group col-md-1 email-delete-btn-wrapper">' +
        '<button type = "button" class="btn btn-link remove-email"> <i class="far fa-times-circle"></i></button>' +
        '</div>';
    $("#emails-wrapper .first-row:first-child").append(deleteBtn).removeClass('first-row');
});

$('body').on('click', '.remove-email', function () {
    $(this).parent().parent().remove();
    $("#emails-wrapper .form-row:only-child").find('.email-delete-btn-wrapper').remove();
    $("#emails-wrapper .form-row:only-child").addClass('first-row');
});

function addInvitationSuccess(data) {
    if (data['data']['invitation_sent']) {
        $('#invite-people-modal-error-msg').text('');
        $('#send-invitations-btn').attr('disabled', true);
        $('#send-invitations-btn').html('Invitations sent' + ' <i class="fas fa-check"></i>');
        $('#invite-people-modal-success-msg').text(data['data']['message']);
        $('#add-another-email').css('display', 'none');
        $('.error-msg').text('');
    }
}

function addInvitationError(data) {
    if (data['responseJSON'] && data['status'] == 422) {
        $('#send-invitations-btn .spinner-border').css('display', 'none');
        var errors = data['responseJSON']['errors'];
        var invitationEmailErrorsCount = 100;
        for (var i = 0; i < invitationEmailErrorsCount; i++) {
            if (errors['email.' + i]) {
                $('#invitation-error-msg-' + i).text(errors['email.' + i]);
            } else {
                $('#invitation-error-msg-' + i).text('');
            }
        }
    } else if (data['responseJSON'] && data['status'] == 406) {
        $('#invite-people-modal-error-msg').text(data['responseJSON']['data']['message']);
        $('.error-msg').text('');
        $('#send-invitations-btn .spinner-border').css('display', 'none');
    } else {
        $('.error-msg').text('');
    }
}

$('#send-invitations-btn').click(function () {
    $('#send-invitations-btn .spinner-border').css('display', 'inline-block');
    post('/invitations', addInvitationSuccess, addInvitationError, $("#invite-people-modal #invitation-form").serialize());
});

function deleteUserSuccess(data) {
    if (data['data']['success']) {
        $('.remove-invitee[data-id="' + data['data']['deleted_user_id'] + '"]').parent().parent().remove();
        if (data['data']['users'].length == 0) {
            $('#invitee-list-empty').css('display', 'block');
        }
    }
}

$('body').on('click', '.remove-invitee', function () {
    var userId = $(this).attr('data-id');
    var channelId = $(this).attr('data-channel-id');
    $('.remove-invitee').tooltip('hide');
    $(this).parent().append('<div class="spinner-border spinner-border-sm text-primary" role="status"></div>');
    $(this).css('display', 'none');
    del('/channels/' + channelId + '/users/' + userId, deleteUserSuccess);
});