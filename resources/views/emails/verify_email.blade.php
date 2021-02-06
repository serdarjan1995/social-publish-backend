@extends('emails.mail_layout.layout')

@section('body')
    <h1>@isset($salutation){{$salutation}}@endisset</h1>
    <br/>
    <p>@isset($verify_message){{$verify_message}}@endisset</p>
    <table class="action" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <a href="{{ $verification_url }}" class="button button-primary" target="_blank" rel="noopener">{{ $button_label }}</a>
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
        @isset($button_link_trouble){{$button_link_trouble}}@endisset
        <br/>
            <a href="{{ $verification_url }}" target="_blank" >{{ $verification_url }}</a>
    </p>
    <br/>
    <p>@isset($verify_email_did_not_create){{$verify_email_did_not_create}}@endisset</p>
    <br/>
    <p>@isset($ending_salutation){{$ending_salutation}}@endisset</p>
    <p>{{ config('app.name') }}</p>

@endsection
