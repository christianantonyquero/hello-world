@extends('layouts.admin')

@section('title', 'Store Line')

@section('header-buttons')

@endsection

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card"><div class="card-body"><h5>Currently</h5> <h2>{{ $total }}</h2> <h5>Users in Line</h5></div></div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @empty($store->lineOpenings)
                <p>No line openings. All users will be allowed in at open time.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th># of Users</th>
                        </tr>
                    </thead>
                    @foreach($store->lineOpenings as $opening)
                        <tr>
                            <td>{{ $opening->time }}</td>
                            <td>{{ $opening->users }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var store_id = {{ $store->id }};
    </script>
@endpush
