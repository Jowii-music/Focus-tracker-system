<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Notifications</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: white;
            background: linear-gradient(135deg, #0F0C29, #1A1042, #302B63, #24243e);
            overflow-x: hidden;
        }

        .page {
            padding: 24px;
        }

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

        .muted {
            color: rgba(255,255,255,0.55);
        }

        .session-block {
            margin-top: 28px;
        }

        .session-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 14px;
            color: white;
        }

        .notification-row {
            display: grid;
            grid-template-columns: 55px 100px 1fr;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            font-size: 14px;
        }

        .notification-row:last-child {
            border-bottom: none;
        }

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

        <div class="session-block">
            <div class="session-title">
                Connect Laravel Dashboard To API
            </div>

            <div class="notification-row">
                <span class="muted">09:42</span>
                <span class="badge badge-green">Focused</span>
                <span>Good focus streak started in VS Code.</span>
            </div>

            <div class="notification-row">
                <span class="muted">10:18</span>
                <span class="badge badge-orange">Distracted</span>
                <span>YouTube was open for 6 min.</span>
            </div>

            <div class="notification-row">
                <span class="muted">11:05</span>
                <span class="badge badge-blue">Break</span>
                <span>Time to take a short break.</span>
            </div>
        </div>

        <div class="session-block">
            <div class="session-title">
                Database Integration
            </div>

            <div class="notification-row">
                <span class="muted">13:42</span>
                <span class="badge badge-red">Too Long</span>
                <span>Lost focus for 18 min — YouTube & Reddit.</span>
            </div>

            <div class="notification-row">
                <span class="muted">14:21</span>
                <span class="badge badge-orange">Distracted</span>
                <span>Instagram open for 4 min.</span>
            </div>

            <div class="notification-row">
                <span class="muted">15:10</span>
                <span class="badge badge-green">Back</span>
                <span>You returned to work.</span>
            </div>
        </div>

        <div class="session-block">
            <div class="session-title">
                Documentation
            </div>

            <div class="notification-row">
                <span class="muted">16:02</span>
                <span class="badge badge-green">Focused</span>
                <span>Stayed productive for 45 minutes.</span>
            </div>

            <div class="notification-row">
                <span class="muted">16:48</span>
                <span class="badge badge-blue">Break</span>
                <span>Break reminder triggered.</span>
            </div>
        </div>

        <div class="reset-note">
            Notifications are automatically reset at the end of each day.
        </div>

    </section>

</main>

</body>
</html>