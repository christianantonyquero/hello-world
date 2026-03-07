@if(session('flash_notification'))
<div class="flash-alert-wrapper" id="flash-alert-wrapper">
    @foreach (session('flash_notification', collect())->toArray() as $message)
        @if ($message['overlay'])
            @include('flash::modal', [
                'modalClass' => 'flash-modal',
                'title'      => $message['title'],
                'body'       => $message['message']
            ])
        @else
            <div class="flash-alert alert
                        alert-{{ $message['level'] }}
                        {{ $message['important'] ? 'alert-important' : '' }}" role="alert">

                <p class="alert-text">{!! $message['message'] !!}</p>
            </div>
        @endif
    @endforeach
</div>
@endif
{{ session()->forget('flash_notification') }}
