function updateProfileSuccess(data) {
    $(document).click(function (event) {
        if (!$(event.target).closest(".modal-content").length) {
            $('#update-profile-modal').modal('hide');
        }
    });
    $('#close-multiplication-sign').css('display', 'block');
    $('#update-profile-btn .spinner-border').css('display', 'none');
    $("#update-profile-form :input").attr('disabled', true);
    $('#update-profile-btn').html('Updated' + ' <i class="fas fa-check"></i>');
    $('#update-profile-btn').attr('disabled', true);
    $('.error-msg').text('');
    $('#update-form-success-msg').text(data['data']['message']);
}

function updateProfileError(data) {
    if (data['responseJSON']['errors']) {
        $('#update-profile-btn .spinner-border').css('display', 'none');
        var errors = data['responseJSON']['errors'];
        if (errors) {
            if (errors['name']) {
                $('#name-error-msg').text(errors['name'][0]);
            } else {
                $('#name-error-msg').text('');
            }
            if (errors['username']) {
                $('#username-error-msg').text(errors['username'][0]);
            } else {
                $('#username-error-msg').text('');
            }
            if (errors['email']) {
                $('#email-error-msg').text(errors['email'][0]);
            } else {
                $('#email-error-msg').text('');
            }
            if (errors['password']) {
                $('#password-error-msg').text(errors['password'][0]);
            } else {
                $('#password-error-msg').text('');
            }
        }
    }
}

function updateProfile() {
    $('#update-profile-btn').click(function () {
        $('#update-profile-btn .spinner-border').css('display', 'inline-block');
        put('/profile', updateProfileSuccess, updateProfileError, $("#update-profile-modal #update-profile-form").serialize());
    });
}

$('#update-profile-modal-btn').click(function () {
    $('#update-form-success-msg').text('');
    $('.error-msg').text('');
    $('#avatar-update-success-msg').text('');
    $('#avatar-update-error-msg').text('');
    $("#profile-avatar-selector").replaceWith($("#profile-avatar-selector").val('').clone(true));
    $("#update-profile-form :input").attr('disabled', false);
    $('#update-profile-btn .fa-check').css('display', 'none');
    $('#update-profile-btn ').html('Update' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});

$('#profile-avatar-selector').change(function () {
    var formData = new FormData();
    var files = $('#profile-avatar-selector')[0].files[0];
    formData.append('avatar', files);
    $.ajax({
        url: baseUrl + '/profile/avatar',
        type: "POST",
        contentType: false,
        processData: false,
        data: formData,
        dataType: 'json',
        headers: {
            'Accept': 'application/json',
            "Authorization": "Bearer " + securityToken
        },
        success: function (data) {
            if (data['data']['profile_is_updated']) {
                $('#avatar-update-error-msg').text('');
                $('#profile-avatar-wrapper').attr('src', data['data']['avatar_path']);
                $('.user-avatar[data-user-id="' + userAuthorizedId + '"]').attr('src', data['data']['avatar_path']);
                $('#avatar-update-success-msg').text(data['data']['message']);
            }
        },
        error: function (data) {
            if (data['responseJSON']['errors']['avatar']) {
                $('#avatar-update-success-msg').text('');
                $('#avatar-update-error-msg').text(data['responseJSON']['errors']['avatar']['0']);
            }
        }
    });
});
