function fetchChatsSuccess(data) {
    if (data['data']['status_code'] == 200) {
        var users = data['data']['chats'];
        var usersWrapper = '';
        for (var userIndex = 0; userIndex < users.length; userIndex++) {
            activateChatMessageWebsocketChannel(users[userIndex]['id'], userAuthorizedId);
            if (users[userIndex]['count'] > 0) {
                usersWrapper += '<div class="direct-msg-user-wrapper">' +
                    '<button class="btn btn-link direct-msg-user" data-id="' + users[userIndex]['id'] + '" data-username="' + users[userIndex]['username'] + '"><i class="fas fa-circle"></i> ' + users[userIndex]['username'] + '</button>' +
                    '<div class="new-msg-notification" data-has-notification="true" style="display:block;">' + users[userIndex]['count'] + '</div>' +
                    '</div>';
            } else {
                usersWrapper += '<div class="direct-msg-user-wrapper">' +
                    '<button class="btn btn-link direct-msg-user" data-id="' + users[userIndex]['id'] + '" data-username="' + users[userIndex]['username'] + '"><i class="fas fa-circle"></i> ' + users[userIndex]['username'] + '</button>' +
                    '<div class="new-msg-notification" data-has-notification="false"></div>' +
                    '</div>';
            }
        }
        $('#users-wrapper').html(usersWrapper);
    }
}

function activateChatMessageWebsocketChannel(userId, recipientId) {
    Echo.private('chat.message.websocket.channel.' + userId + '.' + recipientId).listen('ChatMessageEvent', (e) => {

        $('#message-error-msg').text('');
        var newMessage = '<div class="media message-wrapper" style="margin-bottom:12px;">' +
            '<img src="' + e.message.user_avatar + '" data-user-id="' + e.message.user_id + '" class="align-self-start mr-3 user-avatar" alt="' + e.message.user_name + '">' +
            '<div class="media-body">' +
            '<div class="mt-0"><strong class="username">' + e.message.user_name + '</strong> ' + e.message.updated_at + '</div>' + e.message.message + '</div>' +
            '</div>';
        var status = $('#all-messages-wrapper').attr('data-status');
        var type = $('#all-messages-wrapper').attr('data-type');
        var msgFormActiveUserId = $('#msg-from :input[name="id_by_type"]').val();
        if (status == 'true' && type == 'chat' && userId == msgFormActiveUserId) {
            post('/chats/' + userId + '/messages/' + recipientId + '/seen');
            $('#all-messages-wrapper').append(newMessage);
            $("#workspace-main-content").animate({ scrollTop: $('#workspace-main-content').prop("scrollHeight") }, 500);
        }
        var noOfUnreadMessages = $('.direct-msg-user[data-id="' + userId + '"]:not(.active)').parent().find('.new-msg-notification').text();
        if (noOfUnreadMessages == 'NaN' || noOfUnreadMessages == '') {
            $('.direct-msg-user[data-id="' + userId + '"]:not(.active)').parent().find('.new-msg-notification').css('display', 'block').text(1);
        } else {
            $('.direct-msg-user[data-id="' + userId + '"]:not(.active)').parent().find('.new-msg-notification').text(parseInt(noOfUnreadMessages) + 1);
        }
    });
}

function fetchChats() {
    get('/chats/' + userAuthorizedId, fetchChatsSuccess);
}

function fetchRecipientsSuccess(data) {
    console.log('fd');
    $('#chat-invitation-modal-recipients-wrapper').empty();
    var usersTemplate = '';
    if (data['data']['status_code'] == 200 && data['data']['users'].length > 0) {
        var users = data['data']['users'];
        for (var i = 0; i < users.length; i++) {
            if (users[i]['already_created']) {
                usersTemplate += '<div class="form-row">' +
                    '<div class="col-md-11">' +
                    '<div>' + (users[i]['name'] ? users[i]['name'] + ' (' + users[i]['email'] + ')' : users[i]['email']) + '</div>' +
                    '<small class="error-msg" id="invitation-error-msg"></small>' +
                    '</div>' +
                    '<div class="col-md-1">' +
                    '<button type="button" data-toggle="tooltip" data-placement="left" title="A chatroom is already created for this user" class="btn btn-link green-check"><i class="fas fa-check"></i></button>' +
                    '</div>' +
                    '</div>';
            } else {
                usersTemplate += '<div class="form-row">' +
                    '<div class="col-md-11">' +
                    '<div>' + (users[i]['name'] ? users[i]['name'] + ' (' + users[i]['email'] + ')' : users[i]['email']) + '</div>' +
                    '<small class="error-msg" id="invitation-error-msg"></small>' +
                    '</div>' +
                    '<div class="col-md-1">' +
                    '<button type="button" data-toggle="tooltip" data-placement="left" title="Click to create a chatroom" class="btn btn-link create-chatroom" data-id="' + users[i]['id'] + '"><i class="fas fa-comments"></i></button>' +
                    '</div>' +
                    '</div>';
            }
        }
    } else {
        $('#chat-invitation-modal #chat-invitation-modal-body-txt').html('First you need to <a href="#" id="chat-invitation-modal-create-channel-btn" data-toggle="modal" data-target="#channel-modal" id="create-new-channel-btn">create a channel</a> and invite users to it then you can start chatting with invitees.');
    }
    $('#chat-invitation-modal-recipients-wrapper').append(usersTemplate);
    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover'
    });
}

$('body').on('click', '#chat-invitation-modal-create-channel-btn', function () {
    $('#chat-invitation-modal').modal('hide');
});

$('#chat-invitation-btn').click(function () {
    get('/chats/' + userAuthorizedId + '/recipients', fetchRecipientsSuccess);
});

function createChatroomSuccess(data) {
    if (data['data']['status_code'] == 200) {
        $('.create-chatroom[data-id="' + data['data']['recipient_info']['id'] + '"]').parent().find('.spinner-border').remove();
        $('.create-chatroom[data-id="' + data['data']['recipient_info']['id'] + '"]').css('display', 'block').html('<i class="fas fa-check"></i>').addClass('green-check');
        $('#direct-msg-container #users-wrapper').append('<div class="direct-msg-user-wrapper"><button class="btn btn-link direct-msg-user" data-id="' + data['data']['recipient_info']['id'] + '" data-username="' + data['data']['recipient_info']['username'] + '"><i class="fas fa-circle"></i> ' + data['data']['recipient_info']['username'] + '</button><div class="new-msg-notification">12</div></div>');
    }
}

$('body').on('click', '.create-chatroom', function () {
    var userId = $(this).attr('data-id');
    $(this).parent().append('<div class="spinner-border spinner-border-sm text-primary" role="status"></div>');
    $(this).css('display', 'none');
    post('/chats/' + userAuthorizedId + '/recipients', createChatroomSuccess, null, { 'user_id': userId });

});

function getMessagesByRecipientIdSuccess(data) {
    if (data['data']['status_code'] == 200) {
        $('#guide-wrapper').css('display', 'none');
        $('#workspace-main-content #all-messages-wrapper').css('display', 'block');
        $('#all-messages-wrapper').attr('data-status', true);
        $('#all-messages-wrapper').attr('data-type', 'chat');
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
        $('#workspace-main-content #all-messages-wrapper').css('margin-top', '20px').empty().append(template);
    }
}

$('body').on('click', '.direct-msg-user', function () {
    var recipientId = $(this).attr('data-id');
    $('.direct-msg-user[data-id="' + recipientId + '"]').parent().find('.new-msg-notification').css('display', 'none').text('');
    var noOfUnreadMessages = $('.direct-msg-user').not(this).parent().find('.new-msg-notification').text();
    if (noOfUnreadMessages.length > 0) {
        $('.direct-msg-user[data-has-notification="true"]').not(this).parent().find('.new-msg-notification').css('display', 'block');
    }
    var userName = $(this).attr('data-username');
    $(this).addClass('active');
    $('.channel-title-sidebar').removeClass('active');
    $('.direct-msg-user').not(this).removeClass('active');
    var dropdownTemplate = '<a href="#" data-toggle="dropdown" id="chat-info-dropdown">#' + userName + ' <i class="fas fa-chevron-down"></i></a>' +
        '<div class="dropdown-menu" aria-labelledby="chat-info-dropdown">' +
        '<button class="dropdown-item" id="poke-user-btn">Poke user <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></button>' +
        '<div class="dropdown-divider"></div>' +
        '<a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete-chat-modal" id="delete-chat-btn">Delete chat</a></div>';
    $('#workspace-main-content-container #channel-name').html(dropdownTemplate);
    $('#workspace-main-content-container #chat-info-dropdown').attr('data-id', recipientId);
    $('#workspace-main-content-container #chat-info-dropdown').attr('data-name', userName);
    $('#msg-from').css('display', 'block');
    $('#msg-from :input[name="id_by_type"]').val(recipientId);
    $('#msg-from :input[name="type"]').val('chat');
    get('/chats/' + userAuthorizedId + '/messages/' + recipientId, getMessagesByRecipientIdSuccess);
    $("#workspace-main-content").animate({ scrollTop: $('#workspace-main-content').prop("scrollHeight") }, 500);
});

function pokeUserSuccess(data) {
    if (data['data']['is_poked']) {
        $('#poke-user-btn .spinner-border').css('display', 'none');
        $('#poke-user-btn').html('User poked' + ' <i class="fas fa-check"></i>').addClass('disabled');
    }
}

$('body').on('click', '.dropdown-menu #poke-user-btn', function (e) {
    e.stopPropagation();
    var recipientId = $(this).parent().parent().find('#chat-info-dropdown').attr('data-id');
    $('#poke-user-btn .spinner-border').css('display', 'inline-block');
    post('/chats/' + recipientId, pokeUserSuccess);

});

function addChatMessageSuccess(data) {
    if (data['data']['new_message_is_added']) {
        var msg = data['data']['message_info'];
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

function addChatMessageError(data) {
    if (data['responseJSON']['errors']) {
        var errors = data['responseJSON']['errors'];
        if (errors['message']) {
            $('#message-error-msg').text(errors['message'][0]);
        } else {
            $('#message-error-msg').text('');
        }
    }
}

function addChatMessage(message, recipientId, type) {
    post('/chats/' + userAuthorizedId + '/messages/' + recipientId, addChatMessageSuccess, addChatMessageError, { 'message': message, 'type': type });
}

$('body').on('click', '#delete-chat-btn', function () {
    var recipientId = $(this).parent().parent().find('#chat-info-dropdown').attr('data-id');
    $('#delete-chat-modal-success-msg').text('');
    $('#delete-chat-modal-error-msg').text('');
    $('#delete-chat-modal-btn').attr('disabled', false);
    $('#delete-chat-modal-btn').html('Yes' + ' <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    $('#delete-chat-modal #delete-chat-modal-btn').attr('data-id', recipientId);
});

function deleteChatByUserIdSuccess(data) {
    if (data['data']['chat_is_deleted']) {
        $('.direct-msg-user[data-id="' + data['data']['recipient_id'] + '"]').parent().remove();
        $('#delete-chat-modal-success-msg').text(data['data']['message']);
        $('#delete-chat-modal-btn').attr('disabled', true);
        $('#delete-chat-modal-btn').html('Deleted' + ' <i class="fas fa-check"></i>');
        $('#guide-wrapper').css('display', 'block');
        $('#workspace-main-content #all-messages-wrapper').css('display', 'none');
        $('#workspace-main-content-container .header #channel-name').html('Choose a channel from the left sidebar to start');
        $('#msg-from').css('display', 'none');
        if (data['data']['channels'].length == 0) {
            $('#workspace-main-content-container .header #channel-name').html('Click on the <i class="far fa-plus-square"></i> sign next to the "Channels" on the sidebar to create a new channel');
        }
    }
}

function deleteChatByUserIdError(data) {
    var errors = data['responseJSON']['data'];
    if (errors['not_belongs']) {
        $('#channel-modal-delete-error-msg').text(errors['message']);
        $('#delete-channel-btn').attr('disabled', true);
    }
}

$('#delete-chat-modal-btn').click(function () {
    var recipientId = $(this).attr('data-id');
    $('#delete-chat-modal-btn .spinner-border').css('display', 'inline-block');
    del('/chats/' + userAuthorizedId + '/messages/' + recipientId, deleteChatByUserIdSuccess, deleteChatByUserIdError);
});

Echo.join('start.chat.websocket.channel').listen('StartChatEvent', (info) => {
    if (info['recipient_id'] == userAuthorizedId) {
        $('#users-wrapper').html('<div class="direct-msg-user-wrapper"><button class="btn btn-link direct-msg-user online" data-id="' + info['user']['id'] + '" data-username="' + info['user']['username'] + '"><i class="fas fa-circle"></i> ' + info['user']['username'] + '</button><div class="new-msg-notification"></div></div>');
        Echo.join('start.chat.websocket.channel').whisper('recently-joined', userAuthorizedId);
    }
}).listenForWhisper('recently-joined', userId => {
    $('.direct-msg-user[data-id="' + userId + '"]').addClass('online');
});