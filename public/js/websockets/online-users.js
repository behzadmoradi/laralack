Echo.join('online.users.websocket.channel').here(users => {
    for (var i = 0; i < users.length; i++) {
        $('.direct-msg-user[data-id="' + users[i]['id'] + '"]').addClass('online');
    }
}).joining(user => {
    $('.direct-msg-user[data-id="' + user['id'] + '"]').addClass('online');
}).leaving(user => {
    $('.direct-msg-user[data-id="' + user['id'] + '"]').removeClass('online');
});