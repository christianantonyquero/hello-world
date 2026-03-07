@extends('layouts.app')

@section('content')
    <section class="section">
        <div id="app"></div>
    </section>
@endsection


@push('extra_js')
<script src="{{ mix('js/test/remove-bg-test-app.js') }}"></script>
@endpush

