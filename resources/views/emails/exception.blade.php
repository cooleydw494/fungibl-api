<p><strong>An exception occurred:</strong></p>
<p>{{ $exception->getMessage() }}</p>
<br>

<p>
    <strong>User info:</strong>
    @if ($userInfo) {{ json_encode($userInfo) }} @else No logged in user. @endif
</p>
<p><strong>IP address:</strong> {{ $ipAddress }}</p>
<p><strong>X-Forwarded-For:</strong> {{ $xForwardedFor }}</p>
<br>

<p><strong>Stack trace:</strong></p>
<p>{{ $exception->getTraceAsString() }}</p>
