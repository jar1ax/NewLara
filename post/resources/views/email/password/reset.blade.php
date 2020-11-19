@component('mail::message')
# Introduction
Hello! This is your Token: **{{$token}}**

@component('mail::button', ['url' =>$token])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
