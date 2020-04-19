import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    // encrypted: true, 
    wsHost: window.location.hostname,
    wsPort: 6001,
    disableStats: true,
}); 

 