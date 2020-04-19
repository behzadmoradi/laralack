@extends('layouts.workspace')
@section('content')
    @include('partials.update-profile-modal')
    @include('partials.channel-modal')
    @include('partials.delete-channel-modal')
    @include('partials.invite-people-modal')
    @include('partials.chat-invitation-modal')
    @include('partials.delete-chat-modal')
    <div class="workspace-column" id="workspace-left-sidebar">
        <div class="header">
            <div id="username-container">
                <a href="#" id="user-info-dropdown" data-toggle="dropdown">
                    <i class="fas fa-circle"></i>
                    <span class="username" id="username">
                        {!! Auth::user()->name !!}
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </a>
                <div class="dropdown-menu" aria-labelledby="user-info-dropdown">
                    <a class="dropdown-item" id="update-profile-modal-btn" href="#" data-toggle="modal" data-target="#update-profile-modal">Update profile</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form> 
                </div>
            </div>
        </div>
        <div class="content custom-scrollbar" id="workspace-left-sidebar-content">
            <div id="channels-container">
                <div id="title-container">
                    <div>Channels</div>
                    <div>
                        <a href="#" data-toggle="modal" data-target="#channel-modal" id="create-new-channel-btn"><i class="far fa-plus-square"></i></a>
                    </div>
                </div>
                <div id="channels-wrapper"></div>
            </div>
            <div id="direct-msg-container">
                <div id="title-container">
                    <div>Direct Messages</div>
                    <div>
                        <a href="#" data-toggle="modal" data-target="#chat-invitation-modal" id="chat-invitation-btn"><i class="far fa-plus-square"></i></a>
                    </div>
                </div>
                <div id="users-wrapper"></div>
            </div>
        </div>
    </div>
    <div class="workspace-column" id="workspace-main-content-container">
        <div class="header">
            <div id="channel-name"></div>
        </div>
        <div class="content custom-scrollbar" id="workspace-main-content">
            <div id="guide-wrapper">
                <h5>Some guidance on how to use this</h5>
                <div id="guide-description">
                    You can use the following links to get to know how to get most out of this system:
                </div>
                <ul>
                    <li><a href="https://lab.lepture.com/editor/markdown" rel="nofollow" target="_blank">How to use markdown</a></li>
                    <li><a href="#" target="_blank">How to create a new channel</a></li>
                    <li><a href="#" target="_blank">How to add users to a channel</a></li>
                </ul>
            </div>
            <div id="all-messages-wrapper" data-status="false"></div>
        </div>	
        <div class="mt-auto py-3">
            <form id="msg-from">
                <input type="hidden" name="type" value="">
                <input type="hidden" name="id_by_type" value="">
                <div class="form-group">
                    <textarea class="form-control" id="form-message-input" placeholder="Enter you message in here"></textarea>
                    <div id="messages-wrapper">
                        <small id="is-typing" data-is-typing="false"></small>
                        <small class="error-msg" id="message-error-msg"></small>
                    </div>
                </div>
            </form>
        </div>
    </div>  
    <?= "<script>var authName = '" . Auth::user()->name . "';</script>"; ?>
    <?= "<script>var authUsername = '" . Auth::user()->username . "';</script>"; ?>
    <script>
        $(document).ready(function() {
            // file path: js/api/api.js
            setApiUriAndToken('<?= config('app.api_url') ?>', '<?= Auth::user() ?>');

            if ((authName == '') || (authUsername == '')) {
                $('#update-profile-modal').modal();
            }

            // https://github.com/lepture/editor another one: https://simplemde.com/
            var editor = new Editor({
                element: document.getElementById('form-message-input'),
                status: false,
            });
            editor.render();
            $(".CodeMirror").bind("keypress", function (e) {
                var type = $('#msg-from :input[name="type"]').val();
                var idByType = $('#msg-from :input[name="id_by_type"]').val();
                var message = editor.codemirror.getValue();
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 13) {
                    if (type == 'channel') {
                        // file path: js/api/channel.js
                        addChannelMessage(message, idByType, type);
                    } else if (type == 'chat') {
                        // file path: js/api/chat.js
                        addChatMessage(message, idByType, type);                        
                    }
                    editor.codemirror.setValue('');
                    return false;
                }
                // the listener path: js/websockets/is-typing.js
                Echo.join('is.typing.websocket.channel').whisper('typing', {
                    'sender_name': '<?= Auth::user()->name ?>', 
                    'sender_id':  '<?= Auth::user()->id ?>',
                    'type': type,
                    'id_by_type': idByType,
                    'recipient_id': $('#chat-info-dropdown').attr('data-id')
                });
            
            });

            // file path: js/api/profile.js
            updateProfile();
            
            // file path: js/api/channel.js
            getChannelsByUserId();
            addEditChannel();
            
            // file path: js/api/chat.js
            fetchChats();
        });
    </script>
@endsection