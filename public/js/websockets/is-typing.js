Echo.join('is.typing.websocket.channel').listenForWhisper('typing', response => {
    var type = response['type'];
    var senderName = response['sender_name'];
    var senderId = response['sender_id'];
    var idByType = response['id_by_type'];
    var recipientId = response['recipient_id'];
    var formType = $('#msg-from :input[name="type"]').val();
    var msgFormActiveUserId = $('#msg-from :input[name="id_by_type"]').val();
    var isTyping = $('#is-typing').attr('data-is-typing');
    var typingTimer = $('#is-typing').attr('data-typing-timer');
    if ((isTyping == 'false') && (type == formType)) {
        if (type == 'chat') {
            if ((recipientId == userAuthorizedId) && (msgFormActiveUserId == senderId)) {
                $('#message-error-msg').text('');
                $('#is-typing').css('display', 'block').append(senderName + ' is typing <span class="loader-dot">.</span><span class="loader-dot">.</span><span class="loader-dot">.</span>');
                $('#is-typing').attr('data-is-typing', true);
            }
        } else if (type == 'channel') {
            if (msgFormActiveUserId == idByType) {
                $('#message-error-msg').text('');
                $('#is-typing').css('display', 'block').append(senderName + ' is typing <span class="loader-dot">.</span><span class="loader-dot">.</span><span class="loader-dot">.</span>');
                $('#is-typing').attr('data-is-typing', true);
            }
        }
    }
    if (typingTimer > 0) {
        clearTimeout(typingTimer);
    }
    typingTimer = setTimeout(function () {
        $('#is-typing').attr('data-is-typing', false);
        $('#is-typing').css('display', 'none');
        $('#is-typing').empty();
    }, 1000);
    $('#is-typing').attr('data-typing-timer', typingTimer);
});