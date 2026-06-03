<h1>Dashboard</h1>

<h2>Users</h2>
@foreach ($users as $user)
    <p>User ID: {{ $user->id }}</p>
    <p>{{ $user->name }}</p>
    <p>{{ $user->email }}</p>
@endforeach

<hr>

<h2>Reports</h2>

@foreach ($reports as $report)
    <p>
        Date: {{ $report->report_date }} <br>
        Total seconds: {{ $report->total_tracked_seconds }} <br>
        Productive: {{ $report->productive_seconds }} <br>
        Distracted: {{ $report->distracted_seconds }} <br>
        Most used site: {{ $report->most_used_site }}
    </p>

    <hr>
@endforeach