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

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            background: linear-gradient(135deg, #0F0C29, #1A1042, #302B63, #24243e);
            overflow-x: hidden;
        }

        .page { padding: 24px; }

        .notification-page-card {
            max-width: 1150px;
            margin: 0 auto;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 18px;
            padding: 20px;
            backdrop-filter: blur(18px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.22);
        }

        .page-title {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .muted { color: rgba(255,255,255,0.55); }

        .session-block { margin-top: 28px; }

        .session-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 14px;
            color: white;
        }

        .notification-row {
            display: grid;
            grid-template-columns: 55px 160px 1fr;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            font-size: 14px;
        }

        .notification-row:last-child { border-bottom: none; }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            width: fit-content;
            white-space: nowrap;
        }

        .badge-red {
            background: rgba(248,113,113,0.18);
            color: #f87171;
        }

        .badge-blue {
            background: rgba(96,165,250,0.18);
            color: #60a5fa;
        }

        .badge-orange {
            background: rgba(251,146,60,0.18);
            color: #fb923c;
        }

        .badge-green {
            background: rgba(52,211,153,0.18);
            color: #34d399;
        }

        .reset-note {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,0.08);
            text-align: center;
            color: rgba(255,255,255,0.55);
            font-size: 14px;
        }
    </style>
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