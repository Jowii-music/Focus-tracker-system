<!DOCTYPE html>
<html>
<head>
    <title>FocusTrack Dashboard</title>
    <link rel="stylesheet" href="/css/app_useage.css">
    <link rel="stylesheet" href="/css/control_panel.css">
    <link rel="stylesheet" href="/css/latest_notification.css">
    <link rel="stylesheet" href="/css/nav.css">
    <link rel="stylesheet" href="/css/stats.css">
    <link rel="stylesheet" href="/css/time_split.css">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>

<body>

    @include('components.nav')

    <main class="page">

        <div class="dashboard-grid">
            @include('components.time-split')
            @include('components.control-panel')
            @include('components.app-usage')
            @include('components.stats')
            @include('components.latest-notifications')
        </div>

    </main>

</body>
</html>