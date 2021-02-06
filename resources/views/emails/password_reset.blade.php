@extends('emails.mail_layout.layout')

@section('body')
    <h1>@isset($salutation){{$salutation}}@endisset</h1>
    <br/>
    <p>@isset($reset_password_info){{$reset_password_info}}@endisset</p>
    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <a href="{{ $reset_link }}" class="button button-primary" target="_blank" rel="noopener">{{ $button_label }}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p>
        @isset($reset_password_expire){{$reset_password_expire}}@endisset
    </p>
    <p>
        @isset($button_link_trouble){{$button_link_trouble}}@endisset
        <br/>
        <a href="{{ $reset_link }}" target="_blank" >{{ $reset_link }}</a>
    </p>
    <br/>
    <p>@isset($reset_password_did_not_reset){{$reset_password_did_not_reset}}@endisset</p>
    <br/>
    <p>@isset($ending_salutation){{$ending_salutation}}@endisset</p>
    <p>{{ config('app.name') }}</p>

@endsection
