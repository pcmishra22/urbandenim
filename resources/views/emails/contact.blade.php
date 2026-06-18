@extends('emails.layout', ['headerColor' => '#D19C97', 'headerSubtitle' => 'New Customer Enquiry'])

@section('content')
<h2>New Contact Form Submission</h2>
<p>You have received a new message via the Jeanzo contact form.</p>

<div class="info">
    <p><span class="label">Name</span>{{ $senderName }}</p>
    <p><span class="label">Email</span>{{ $senderEmail }}</p>
    <p><span class="label">Subject</span>{{ $contactSubject }}</p>
</div>

<p style="font-weight:600;margin-top:20px;margin-bottom:6px;">Message:</p>
<div style="background:#f9f5f5;border-left:4px solid #D19C97;padding:14px 18px;border-radius:4px;font-size:14px;color:#444;line-height:1.7;white-space:pre-wrap;">{{ $userMessage }}</div>

<hr class="divider">
<p style="font-size:12px;color:#aaa;text-align:center;">
    To reply, simply respond to this email — it will go directly to {{ $senderEmail }}.
</p>
@endsection
