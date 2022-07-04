@component('mail::message')
<img src="{{ url('images/logo.png') }}" class="logo center" alt="Laravel Logo">
<p style="font-size: 1rem; font-weight: bold; text-align: center">{{ __('your code verifications') }}</p>

<p style="font-size: 3rem; text-align: center"> {!! $otp !!}</p>
<p style="text-align: center ">
{{ __('Please enter this code to verify your account') }}
</p>

<p style="text-align: center">Jika Anda tidak mengirimkan permohonan permintaan kode, mohon <a href="{{ url('login') }}">ganti passwod akun</a> Anda</p>

@slot('subcopy')
<span class="break-all" style="text-align: center; display: block">Dikirim oleh <a href="{{ url('/') }}">Rumah Berkat Yayasan Bersama</a></span>
@endslot
@endcomponent
