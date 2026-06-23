@php
    $latestNotifications = \App\Models\Notification::where('user_id', session('user_id'))
        ->whereDate('created_at', today())
        ->latest('created_at')
        ->take(4)
        ->get();

    function notificationBadge($type) {
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

<style>
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

.muted {
    color: rgba(255,255,255,0.55);
}

.notification-card {
    height: 100%;
}
</style>

<section class="card notification-card area-notifications" id="latestNotificationsCard">

    <div class="card-title">Latest notifications</div>

    <p class="muted">
        Only the newest alerts are shown here.
    </p>

    <br><br>

    <div id="latestNotificationsBody">
        @forelse($latestNotifications as $notification)
            @php
                [$label, $badgeClass] = notificationBadge($notification->type);
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
        @empty
            <p class="muted">
                No notifications yet today.
            </p>
        @endforelse
    </div>

</section>

<script>
function getNotificationMeta(type) {
    const map = {
        session_started: ['Session Started', 'badge-green'],
        focus_achieved: ['Focus Achieved', 'badge-green'],
        focus_streak: ['Focus Streak', 'badge-green'],
        break_reminder: ['Break Reminder', 'badge-blue'],
        session_paused: ['Session Paused', 'badge-blue'],
        session_resumed: ['Session Resumed', 'badge-green'],
        distracted: ['Focus Interrupted', 'badge-orange'],
        too_distracted: ['Focus Lost', 'badge-red'],
        finished_excellent: ['Excellent Session', 'badge-green'],
        finished_good: ['Good Session', 'badge-green'],
        finished_okay: ['Okay Session', 'badge-orange'],
        finished_bad: ['Bad Session', 'badge-red'],
        session_cancelled: ['Session Cancelled', 'badge-red']
    };

    return map[type] || ['Notification', 'badge-blue'];
}

async function refreshLatestNotifications() {
    const response = await fetch('/latest-notifications');
    const notifications = await response.json();

    let html = '';

    if (notifications.length === 0) {
        html = '<p class="muted">No notifications yet today.</p>';
    }

    notifications.forEach(notification => {
        const [label, badge] = getNotificationMeta(notification.type);

        html += `
            <div class="notification-row">
                <span class="muted">${notification.time}</span>
                <span class="badge ${badge}">${label}</span>
                <span>${notification.message}</span>
            </div>
        `;
    });

    document.getElementById('latestNotificationsBody').innerHTML = html;
}

setInterval(refreshLatestNotifications, 3000);
</script>