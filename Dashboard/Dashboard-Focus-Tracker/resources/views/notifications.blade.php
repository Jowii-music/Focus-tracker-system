@php
    $notificationGroups = \App\Models\Notification::where('user_id', session('user_id'))
        ->whereDate('created_at', today())
        ->latest('created_at')
        ->get()
        ->groupBy('session_title');

    function notificationMeta($type) {
        $map = [
            'session_started' => ['Session Started', 'badge-green'],
            'focus_achieved' => ['Focus Achieved', 'badge-green'],
            'focus_streak' => ['Focus Streak', 'badge-green'],
            'break_reminder' => ['Break Reminder', 'badge-blue'],
            'session_paused' => ['Session Paused', 'badge-blue'],
            'session_resumed' => ['Session Resumed', 'badge-green'],
            'distracted' => ['Focus Interrupted', 'badge-orange'],
            'too_distracted' => ['Focus Lost', 'badge-red'],
            'finished_excellent' => ['Excellent Session', 'badge-green'],
            'finished_good' => ['Good Session', 'badge-green'],
            'finished_okay' => ['Okay Session', 'badge-orange'],
            'finished_bad' => ['Bad Session', 'badge-red'],
            'session_cancelled' => ['Session Cancelled', 'badge-red'],
        ];

        return $map[$type] ?? ['Notification', 'badge-blue'];
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Notifications</title>

    <link rel="stylesheet" href="/css/notifications.css">
    <link rel="stylesheet" href="/css/nav.css">
</head>

<body>

@include('components.nav')

<main class="page">
    <section class="notification-page-card">

        <h1 class="page-title">Notification Log</h1>

        <p class="muted">
            Notifications are grouped by focus session.
        </p>

        @forelse($notificationGroups as $sessionTitle => $notifications)
            <div class="session-block">
                <div class="session-title">
                    {{ $sessionTitle ?: 'Untitled Session' }}
                </div>

                @foreach($notifications as $notification)
                    @php
                        [$label, $badgeClass] = notificationMeta($notification->type);
                    @endphp

                    <div class="notification-row">
                        <span class="muted">
                            {{ \Carbon\Carbon::parse($notification->created_at)->format('H:i') }}
                        </span>

                        <span class="badge {{ $badgeClass }}">
                            {{ $label }}
                        </span>

                        <span>{{ $notification->message }}</span>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="session-block">
                <p class="muted">No notifications yet today.</p>
            </div>
        @endforelse

        <div class="reset-note">
            Notifications are automatically reset at the end of each day.
        </div>

    </section>
</main>

</body>
</html>