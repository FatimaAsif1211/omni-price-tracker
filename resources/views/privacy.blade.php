{{-- resources/views/privacy.blade.php --}}
@extends('layout')
@section('title','Privacy Policy — OmniPrice')
@section('content')
<div style="padding:70px 0 20px">
<div class="container" style="max-width:780px">
    <h1 class="section-title mb-2">Privacy <span style="background:var(--gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent">Policy</span></h1>
    <p style="color:var(--brand-muted);margin-bottom:40px">Last updated: {{ date('F Y') }}</p>

    @php $sections = [
        ['No Personal Data Collected','OmniPrice does not require account creation, login, or any personally identifiable information. Your searches are not tied to your identity.'],
        ['Search Queries','Search terms you enter may be temporarily logged for debugging purposes only. We do not sell, share, or analyse these logs for advertising.'],
        ['Third-Party Stores','When you click "View Product" you leave OmniPrice and are subject to that store\'s own privacy policy (PriceOye, Daraz, or Naheed). We have no control over their data practices.'],
        ['Cookies','OmniPrice uses only functional cookies required for the website to operate. No advertising or tracking cookies are used.'],
        ['Price Data','Product prices and details are fetched live from partner stores. We do not guarantee price accuracy and recommend verifying on the store before purchasing.'],
        ['Contact','For any privacy concerns email us via the Contact page.'],
    ]; @endphp

    @foreach($sections as $i => $s)
    <div class="product-card p-4 mb-3">
        <h5 style="font-family:var(--font-head);margin-bottom:10px">{{ ($i+1) }}. {{ $s[0] }}</h5>
        <p style="color:var(--brand-muted);margin:0">{{ $s[1] }}</p>
    </div>
    @endforeach
</div>
</div>
@endsection