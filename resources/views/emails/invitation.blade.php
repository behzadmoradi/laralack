@component('mail::message')
# Invitation to a new channel
{{ $user_name }} has invited you to a new channel in {{ config('app.name') }}. In order to accept it, click on the following link

@component('mail::button', ['url' => $link])
Accept invitation
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
