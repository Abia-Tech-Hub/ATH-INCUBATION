@php
    $errCode = session('error');
    $sent = session('sent');
    $done = session('done');
    $errorText = $errors->any()
        ? $errors->first()
        : \App\Support\FlashText::error(is_string($errCode) ? $errCode : null);
    $okText = \App\Support\FlashText::success(
        is_string($sent) ? $sent : null,
        is_string($done) ? $done : null,
    );
@endphp
@if ($errorText)
    <p class="ac-flash ac-flash-err" role="alert">{{ $errorText }}</p>
@elseif ($okText)
    <p class="ac-flash" role="status">{{ $okText }}</p>
@endif
