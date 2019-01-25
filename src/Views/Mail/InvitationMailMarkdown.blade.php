@component('mail::message')
# Hello!

You have been invited to join {{ config('app.name') }}.

Your invitation code is __{{$invite->code}}__ and is valid from {{$invite->valid_from}}.

@if($invite->valid_upto)
Your invitation expires at {{$invite->valid_upto}}.
@endif

@component('mail::button', ['url' => $url])
Register Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
