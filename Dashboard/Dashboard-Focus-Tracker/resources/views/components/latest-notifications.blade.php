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
            'finished_excellent' => ['Excellent Session', 'badge-logo'],
            'finished_good' => ['Good Session', 'badge-green'],
            'finished_okay' => ['Okay Session', 'badge-orange'],
            'finished_bad' => ['Bad Session', 'badge-red'],
            'session_cancelled' => ['Session Cancelled', 'badge-red'],
        ];

        return $map[$type] ?? ['Notification', 'badge-blue'];
    }
@endphp

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
                    <span class="badge-text">{{ $label }}</span>
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
        finished_excellent: ['Excellent Session', 'badge-logo'],
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
                <span class="badge ${badge}">
                    <span class="badge-text">${label}</span>
                </span>
                <span>${notification.message}</span>
            </div>
        `;
    });

    document.getElementById('latestNotificationsBody').innerHTML = html;
}

setInterval(refreshLatestNotifications, 3000);
</script>