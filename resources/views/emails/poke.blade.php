@component('mail::message')
# You have a new message

{{ $user_name }} has sent you a new private message in {{ config('app.name') }}. In order to show it, click on the following link

@component('mail::button', ['url' => $link])
Show message
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
