@component('mail::message')
    # Jar1ax.com
    Hello. its a pity that you decided to leave us.
    We hope you had best experience using our app.

    @component('mail::button',['url' => ''])
        Stay with us
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
