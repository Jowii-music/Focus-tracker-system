@php
    $latestNotifications = \App\Models\Notification::where('user_id', session('user_id'))
        ->whereDate('created_at', today())
        ->latest('created_at')
        ->take(4)
        ->get();

    function notificationBadge($type) {
        if (str_contains($type, 'paused') || str_contains($type, 'break')) return ['Break', 'badge-blue'];
        if (str_contains($type, 'cancelled') || str_contains($type, 'bad')) return ['Alert', 'badge-red'];
        if (str_contains($type, 'distracted')) return ['Distracted', 'badge-orange'];
        return ['Focus', 'badge-green'];
    }
@endphp

<style>
.notification-row {
    display: grid;
    grid-template-columns: 55px 100px 1fr;
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
async function refreshLatestNotifications() {

    const response = await fetch('/latest-notifications');

    const notifications = await response.json();

    let html = '';

    notifications.forEach(notification => {

        let label = 'Focus';
        let badge = 'badge-green';

        if (notification.type.includes('paused') || notification.type.includes('break')) {
            label = 'Break';
            badge = 'badge-blue';
        }

        if (notification.type.includes('cancelled') || notification.type.includes('bad')) {
            label = 'Alert';
            badge = 'badge-red';
        }

        if (notification.type.includes('distracted')) {
            label = 'Distracted';
            badge = 'badge-orange';
        }

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