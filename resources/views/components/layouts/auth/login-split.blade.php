@php
    $content = $slot ?? '';
@endphp
@include('layouts.auth.login-split', ['content' => $content])
