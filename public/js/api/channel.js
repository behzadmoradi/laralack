function getChannelsSuccess(data) {
    if (data['data']['channels'].length > 0) {
        $('#workspace-main-content-container .header #channel-name').html('Choose a channel from the left sidebar to start');
        var channels = data['data']['channels'];
        var channelsWrapper = '';
        for (var i = 0; i < channels.length; i++) {
            channelsWrapper += '<button class="btn btn-link channel-title-sidebar" data-id="' + channels[i]['id'] + '" data-description="' + channels[i]['description'] + '" data-name="' + channels[i]['name'] + '" data-is-owner="' + (channels[i]['user_id'] == userAuthorizedId ? true : false) + '">#' + channels[i]['name'] + (channels[i]['user_id'] == userAuthorizedId ? ' (yours)' : '') + '</button>';
        }
        $('#channels-wrapper').html(channelsWrapper);
    } else {
        $('#workspace-main-content-container .header #channel-name').html('Click on the <i class="far fa-plus-square"></i> sign next to the "Channels" on the sidebar to create a new channel');
    }
}

function getChannelsByUserId() {
    get('/channels/' + userAuthorizedId, getChannelsSuccess);
}

function addEditChannelSuccess(data) {
    $('#submit-channel-btn .spinner-border').css('display', 'none');
    $("#channel-form :input").attr('disabled', true);
    $('#submit-channel-btn').attr('disabled', true);
    $('.error-msg').text('');
    $('#channel-modal-success-msg').text(data['data']['message']);
    if (data['data']['new_channel_is_added']) {
        $('#channels-wrapper').prepend('<button class="btn btn-link channel-title-sidebar" data-id="' + data['data']['channel_info']['id'] + '" data-description="' + data['data']['channel_info']['description'] + '" data-name="' + data['data']['channel_info']['name'] + '" data-is-owner="true">#' + data['data']['channel_info']['name'] + ' (yours)</button>');
        $('#submit-channel-btn').html('Added' + ' <i class="fas fa-check"></i>');
        $('#workspace-main-content-container .header #channel-name').html('Choose a channel from the left sidebar to start');
    }
    if (data['data']['channel_is_updated']) {
        $('#submit-channel-btn').html('Updated' + ' <i class="fas fa-check"></i>');
        $('.channel-title-sidebar[data-id="' + data['data']['channel_info']['id'] + '"]').text('#' + data['data']['channel_info']['name'] + ' (yours)');
        $('.channel-title-sidebar[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-name', data['data']['channel_info']['name']);
        $('.channel-title-sidebar[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-description', data['data']['channel_info']['description']);

        $('.channel-title[data-id="' + data['data']['channel_info']['id'] + '"]').html('#' + data['data']['channel_info']['name'] + ' <i class="fas fa-chevron-down"></i>');
        $('h4.channel-title[data-id="' + data['data']['channel_info']['id'] + '"]').html('#' + data['data']['channel_info']['name']);

        $('.channel-title[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-name', data['data']['channel_info']['name']);
        $('.channel-title-sidebar[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-description', data['data']['channel_info']['description']);
        $('.channel-title[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-description', data['data']['channel_info']['description']);
        $('.channel-description[data-id="' + data['data']['channel_info']['id'] + '"]').html('<b>Channel description:</b> ' + data['data']['channel_info']['description']);
        if (data['data']['channel_info']['description'] == null) {
            $('.channel-description[data-id="' + data['data']['channel_info']['id'] + '"]').text('You can add a description to your channel from the top menu "Edit channel" option');
            $('.channel-title-sidebar[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-description', '');
            $('.channel-title[data-id="' + data['data']['channel_info']['id'] + '"]').attr('data-description', '');
        }
    }
}

function addEditChannelError(data) {
    $('#submit-channel-btn .spinner-border').css('display', 'none');
    if (data['responseJSON']['errors']) {
        var errors = data['responseJSON']['errors'];
        if (errors) {
            if (errors['name']) {
                $('#channel-name-error-msg').text(errors['name'][0]);
            } else {
                $('#channel-name-error-msg').text('');
            }
            if (errors['description']) {
                $('#channel-description-error-msg').text(errors['description'][0]);
            } else {
                $('#channel-description-error-msg').text('');
            }
        }
    } else if (data['responseJSON']['data']['channel_name_exists']) {
        $('#channel-name-error-msg').text(data['responseJSON']['data']['message']);
    } else if (data['responseJSON']['data']['not_belongs']) {
        $('#channel-name-error-msg').text(data['responseJSON']['data']['message']);
    }
}

function addEditChannel() {
    $("#channel-form").bind("keypress", function (e) {
        if (e.keyCode == 13) {
            return false;
        }
    });
    $('#submit-channel-btn').click(function () {
        var type = $(this).parent().find(':input[name="type"]').val();
        $('#submit-channel-btn .spinner-border').css('display', 'inline-block');
        if (type == 'add') {
            post('/channels', addEditChannelSuccess, addEditChannelError, $("#channel-modal #channel-form").serialize());
        } else if (type == 'edit') {
            var channelId = $('#channel-form :input[name="id"]').val();
            put('/channels/' + channelId, addEditChannelSuccess, addEditChannelError, $("#channel-modal #channel-form").serialize());
        }
    });
}

$('#create-new-channel-btn').click(function () {
    $('#channel-modal-success-msg').text('');
    $('.error-msg').text('');
    $("#channel-form :input").attr('disabled', false);
    $('#submit-channel-btn .fa-check').css('display', 'none');
    $('#channel-form #channel-name').val('');
    $('#channel-form #channel-description').val('');
    $('#channel-form :input[name="type"]').val('add');
    $('#channel-modal-label').text('Add a new channel');
    $('#channel-modal .modal-body #channel-modal-body-txt').html('Organize your channels around a topic based on what your communications are about; for example <strong>#copyrighting</strong>.');
    $('#submit-channel-btn').html('Create' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});

$('body').on('click', '#edit-channel-btn', function () {
    var channelId = $(this).parent().parent().find('.channel-title').attr('data-id');
    var channelDescription = $(this).parent().parent().find('.channel-title').attr('data-description');
    var channelTitle = $(this).parent().parent().find('.channel-title').attr('data-name');
    $('#channel-form :input[name="type"]').val('edit');
    $('#channel-form :input[name="name"]').attr('value', channelTitle);
    if (channelDescription != 'null') {
        $('#channel-form textarea#channel-description').text(channelDescription);
    }
    $('#channel-form :input[name="id"]').val(channelId);
    $('#channel-modal-success-msg').text('');
    $('.error-msg').text('');
    $("#channel-form :input").attr('disabled', false);
    $('#submit-channel-btn .fa-check').css('display', 'none');
    $('#channel-form')[0].reset();
    $('#channel-modal-label').text('Update channel');
    $('#channel-modal .modal-body #channel-modal-body-txt').text('');
    $('#submit-channel-btn').html('Update' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
});

$('body').on('click', '#del-channel-btn', function () {
    var channelId = $(this).parent().parent().find('.channel-title').attr('data-id');
    $('#channel-modal-delete-success-msg').text('');
    $('#channel-modal-delete-error-msg').text('');
    $('#delete-channel-btn').attr('disabled', false);
    $('#delete-channel-btn').html('Yes' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    $('#delete-channel-modal #delete-channel-btn').attr('data-id', channelId);
});

function deleteChannelByIdSuccess(data) {
    if (data['data']['channel_is_deleted']) {
        $('.channel-title-sidebar[data-id="' + data['data']['channel_id'] + '"]').remove();
        $('#channel-modal-delete-success-msg').text('Channel is deleted successfully.');
        $('#delete-channel-btn').attr('disabled', true);
        $('#delete-channel-btn').html('Deleted' + ' <i class="fas fa-check"></i>');
        $('#guide-wrapper').css('display', 'block');
        $('#workspace-main-content #all-messages-wrapper').css('display', 'none');
        $('#workspace-main-content-container .header #channel-name').html('Choose a channel from the left sidebar to start');
        $('#msg-from').css('display', 'none');
        if (data['data']['channels'].length == 0) {
            $('#workspace-main-content-container .header #channel-name').html('Click on the <i class="far fa-plus-square"></i> sign next to the "Channels" on the sidebar to create a new channel');
        }
    }
}

function deleteChannelByIdError(data) {
    var errors = data['responseJSON']['data'];
    if (errors['not_belongs']) {
        $('#channel-modal-delete-error-msg').text(errors['message']);
        $('#delete-channel-btn').attr('disabled', true);
    }
}

$('#delete-channel-btn').click(function () {
    var channelId = $(this).attr('data-id');
    $('#delete-channel-btn .spinner-border').css('display', 'inline-block');
    del('/channels/' + channelId, deleteChannelByIdSuccess, deleteChannelByIdError);
});

// message stuff
function getMessagesByChannelIdSuccess(data) {
    if (data['data']['status_code'] == 200) {
        $('#all-messages-wrapper').attr('data-status', true);
        $('#all-messages-wrapper').attr('data-type', 'channel');
        var channelInfo = data['data']['channel_info'];
        $('#guide-wrapper').css('display', 'none');
        $('#workspace-main-content #all-messages-wrapper').css('display', 'block');
        var channelInfoWrapper = '<div id="channel-info-wrapper"><h4 class="channel-title" data-id="' + channelInfo['id'] + '">#' + channelInfo['name'] + '</h4> (' + (channelInfo['is_owner'] == true ? 'You created this channel ' + channelInfo['created_at'] : 'This channel was created ' + channelInfo['created_at'] + ' by ' + channelInfo['creator'] + ' and you have been invited to it.') + ')';
        if (channelInfo['description']) {
            channelInfoWrapper += '<div><span class="channel-description" data-id="' + channelInfo['id'] + '"><b>Channel description:</b> ' + channelInfo['description'] + '</span></div>';
        } else {
            if (channelInfo['is_owner']) {
                channelInfoWrapper += '<div><span class="channel-description" data-id="' + channelInfo['id'] + '">You can add a description to your channel from the top menu "Edit channel" option</span></div>';
            }
        }
        $('#workspace-main-content #all-messages-wrapper').html(channelInfoWrapper);
        var template = '';
        for (var i = 0; i < data['data']['messages'].length; i++) {
            var msg = data['data']['messages'];
            template += '<div class="media message-wrapper">' +
                '<img src="' + msg[i]['user_avatar'] + '" data-user-id="' + msg[i]['user_id'] + '" class="align-self-start mr-3 user-avatar" alt="' + msg[i]['user_name'] + '">' +
                '<div class="media-body">' +
                '<div class="mt-0"><strong class="username">' + msg[i]['user_name'] + '</strong> ' + msg[i]['updated_at'] + '</div>' +
                '<p>' + msg[i]['message'] + '</p>' +
                '</div>' +
                '</div>';
        }
        $('#workspace-main-content #all-messages-wrapper').append(template);
    }
}

$('body').on('click', '.channel-title-sidebar', function () {
    var channelId = $(this).attr('data-id');
    var oldChannelId = $('#msg-from :input[name="id_by_type"]').val();
    activeMessageChannel(channelId, oldChannelId);
    var channelDescription = $(this).attr('data-description');
    var channelTitle = $(this).attr('data-name');
    var isOwner = $(this).attr('data-is-owner');
    $(this).addClass('active');
    $('.channel-title-sidebar').not(this).removeClass('active');
    $('.direct-msg-user').removeClass('active');
    if (isOwner == 'true') {
        var dropdownTemplate = '<a href="#" class="channel-title" data-toggle="dropdown" id="channel-info-dropdown">#' + channelTitle + ' <i class="fas fa-chevron-down"></i></a>' +
            '<div class="dropdown-menu" aria-labelledby="channel-info-dropdown">' +
            '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#channel-modal" id="edit-channel-btn">Edit channel</a>' +
            '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#invite-people-modal" id="invite-people-btn">Invite people</a>' +
            '<div class="dropdown-divider"></div>' +
            '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete-channel-modal" id="del-channel-btn">Delete channel</a></div>';
        $('#workspace-main-content-container #channel-name').html(dropdownTemplate);
        $('#workspace-main-content-container #channel-info-dropdown').attr('data-id', channelId);
        $('#workspace-main-content-container #channel-info-dropdown').attr('data-description', channelDescription);
        $('#workspace-main-content-container #channel-info-dropdown').attr('data-name', channelTitle);
    } else {
        $('#workspace-main-content-container #channel-name').html('#' + channelTitle);
        $('#workspace-main-content-container #channel-name').css('color', 'gray');
    }
    $('#msg-from').css('display', 'block');
    $('#msg-from :input[name="id_by_type"]').val(channelId);
    $('#msg-from :input[name="type"]').val('channel');
    get('/channels/' + userAuthorizedId + '/channel/' + channelId + '/messages', getMessagesByChannelIdSuccess);
    $("#workspace-main-content").animate({ scrollTop: $('#workspace-main-content').prop("scrollHeight") }, 500);
});

function activeMessageChannel(channelId, oldChannelId) {
    if (oldChannelId.length != '') {
        Echo.leave('message.channel.' + oldChannelId);
    }
    Echo.private('message.channel.' + channelId).listen('ChannelMessageEvent', (e) => {
        $('#message-error-msg').text('');
        var newMessage = '<div class="media message-wrapper" style="margin-bottom:12px;">' +
            '<img src="' + e.message.user_avatar + '" data-user-id="' + e.message.user_id + '" class="align-self-start mr-3 user-avatar" alt="' + e.message.user_name + '">' +
            '<div class="media-body">' +
            '<div class="mt-0"><strong class="username">' + e.message.user_name + '</strong> ' + e.message.updated_at + '</div>' + e.message.message + '</div>' +
            '</div>';
        var status = $('#all-messages-wrapper').attr('data-status');
        var type = $('#all-messages-wrapper').attr('data-type');
        if (status == 'true' && type == 'channel') {
            $('#all-messages-wrapper').append(newMessage);
            $("#workspace-main-content").animate({ scrollTop: $('#workspace-main-content').prop("scrollHeight") }, 500);
        }
    });
}

function addChannelMessageSuccess(data) {
    if (data['data']['new_message_is_added']) {
        var formActiveChannelId = $('#msg-from :input[name="id_by_type"]').val();
        var msg = data['data']['message_info'];
        if (formActiveChannelId == msg['channel_id']) {
            $('#message-error-msg').text('');
            var newMessage = '<div class="media message-wrapper" style="margin-bottom:12px;">' +
                '<img src="' + msg['user_avatar'] + '" data-user-id="' + msg['user_id'] + '" class="align-self-start mr-3 user-avatar" alt="' + msg['user_name'] + '">' +
                '<div class="media-body">' +
                '<div class="mt-0"><strong class="username">' + msg['user_name'] + '</strong> ' + msg['updated_at'] + '</div>' + msg['message'] + '</div>' +
                '</div>';
            $('#all-messages-wrapper').append(newMessage);
            $("#workspace-main-content").animate({ scrollTop: $('#workspace-main-content').prop("scrollHeight") }, 500);
        }
    }
}

function addChannelMessageError(data) {
    if (data['responseJSON']['errors']) {
        var errors = data['responseJSON']['errors'];
        if (errors['message']) {
            $('#message-error-msg').text(errors['message'][0]);
        } else {
            $('#message-error-msg').text('');
        }
    }
}

function addChannelMessage(message, channelId, type) {
    post('/channels/' + userAuthorizedId + '/channel/' + channelId + '/messages', addChannelMessageSuccess, addChannelMessageError, { 'message': message, 'type': type });
}

Echo.join('channel.deletion.channel').listen('ChannelDeletionEvent', (data) => {
    $('#workspace-main-content-container .header #channel-name').html('Click on the <i class="far fa-plus-square"></i> sign next to the "Channels" on the sidebar to create a new channel');
    $('.channel-title-sidebar[data-id="' + data['channel_id'] + '"]').remove();
});
