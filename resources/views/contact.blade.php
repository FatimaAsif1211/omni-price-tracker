{{-- resources/views/contact.blade.php --}}
@extends('layout')
@section('title','Contact Us — OmniPrice')
@section('content')
<div style="padding:70px 0 20px">
<div class="container" style="max-width:680px">
    <h1 class="section-title mb-2">Get in <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Touch</span></h1>
    <p style="color:var(--brand-muted);margin-bottom:40px">Have a question, found a bug, or want to suggest a store? We'd love to hear from you.</p>

    <div class="product-card p-4 mb-4">
        <div id="contactMsg" class="d-none mb-3"></div>
        <div class="mb-3">
            <label style="font-size:.85rem;color:var(--brand-muted);margin-bottom:6px;display:block">Your Name</label>
            <input type="text" id="cName" class="form-control" style="background:var(--brand-border);border-color:var(--brand-border);color:var(--brand-text)" placeholder="Ali Hassan">
        </div>
        <div class="mb-3">
            <label style="font-size:.85rem;color:var(--brand-muted);margin-bottom:6px;display:block">Email Address</label>
            <input type="email" id="cEmail" class="form-control" style="background:var(--brand-border);border-color:var(--brand-border);color:var(--brand-text)" placeholder="ali@example.com">
        </div>
        <div class="mb-3">
            <label style="font-size:.85rem;color:var(--brand-muted);margin-bottom:6px;display:block">Subject</label>
            <select id="cSubject" class="form-select" style="background:var(--brand-border);border-color:var(--brand-border);color:var(--brand-text)">
                <option>Wrong price shown</option>
                <option>Missing product</option>
                <option>Suggest a store</option>
                <option>General question</option>
                <option>Other</option>
            </select>
        </div>
        <div class="mb-4">
            <label style="font-size:.85rem;color:var(--brand-muted);margin-bottom:6px;display:block">Message</label>
            <textarea id="cMsg" class="form-control" rows="5" style="background:var(--brand-border);border-color:var(--brand-border);color:var(--brand-text);resize:none" placeholder="Tell us more…"></textarea>
        </div>
        <button class="btn btn-brand w-100 py-3" onclick="submitContact()">Send Message</button>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script>
function submitContact() {
    const name    = document.getElementById('cName').value.trim();
    const email   = document.getElementById('cEmail').value.trim();
    const message = document.getElementById('cMsg').value.trim();
    const msgDiv  = document.getElementById('contactMsg');

    if (!name || !email || !message) {
        msgDiv.innerHTML = '<div class="alert alert-warning">Please fill in all fields.</div>';
        msgDiv.className = 'mb-3';
        return;
    }
    // In production: send via axios/fetch to a backend route
    msgDiv.innerHTML = '<div class="alert alert-success">✅ Message sent! We\'ll get back to you within 24 hours.</div>';
    msgDiv.className = 'mb-3';
    ['cName','cEmail','cMsg'].forEach(id => document.getElementById(id).value = '');
}
</script>
@endpush