<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\FocusSession;
use App\Models\SessionEvent;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('user_id')) {
            return redirect('/login');
        }

        $reports = Report::where('userId', session('user_id'))->latest()->get();
        $latestReport = $reports->first();

        $activeSession = FocusSession::where('user_id', session('user_id'))
            ->whereIn('status', ['active', 'paused'])
            ->latest('started_at')
            ->first();

        return view('dashboard', compact('reports', 'latestReport', 'activeSession'));
    }

    public function startSession(Request $request)
    {
        $request->validate([
            'goal' => 'required|string|max:255',
            'target_seconds' => 'required|integer|min:1',
        ]);

        FocusSession::where('user_id', session('user_id'))
            ->whereIn('status', ['active', 'paused'])
            ->update([
                'status' => 'cancelled',
                'ended_at' => now(),
                'result' => 'cancelled',
            ]);

        $session = FocusSession::create([
            'user_id' => session('user_id'),
            'title' => $this->makeSessionTitle($request->goal),
            'goal' => $request->goal,
            'target_seconds' => $request->target_seconds,
            'actual_seconds' => 0,
            'status' => 'active',
            'started_at' => now(),
        ]);

        SessionEvent::create([
            'session_id' => $session->id,
            'user_id' => session('user_id'),
            'type' => 'focused',
            'started_at' => now(),
        ]);

        $this->createNotification($session, 'session_started', 'Session started.');

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
            'title' => $session->title,
            'goal' => $session->goal,
            'target_seconds' => $session->target_seconds,
            'started_at' => $session->started_at,
            'status' => $session->status,
        ]);
    }

    public function pauseSession(Request $request)
    {
        $session = FocusSession::where('id', $request->session_id)
            ->where('user_id', session('user_id'))
            ->where('status', 'active')
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No active session found.']);
        }

        $this->closeOpenEvent($session, 'focused');

        $session->update([
            'status' => 'paused',
            'break_count' => $session->break_count + 1,
        ]);

        SessionEvent::create([
            'session_id' => $session->id,
            'user_id' => session('user_id'),
            'type' => 'break',
            'started_at' => now(),
        ]);

        $this->createNotification($session, 'session_paused', 'Session paused. Break started.');

        return response()->json([
            'success' => true,
            'status' => 'paused',
            'title' => $session->title,
            'target_seconds' => $session->target_seconds,
            'started_at' => $session->started_at,
        ]);
    }

    public function resumeSession(Request $request)
    {
        $session = FocusSession::where('id', $request->session_id)
            ->where('user_id', session('user_id'))
            ->where('status', 'paused')
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No paused session found.']);
        }

        $breakSeconds = $this->closeOpenEvent($session, 'break');

        $session->update([
            'status' => 'active',
            'break_seconds' => $session->break_seconds + $breakSeconds,
        ]);

        SessionEvent::create([
            'session_id' => $session->id,
            'user_id' => session('user_id'),
            'type' => 'focused',
            'started_at' => now(),
        ]);

        $this->createNotification($session, 'session_resumed', 'Session resumed.');
        $this->createNotification($session, 'focused', 'Back in focus.');

        return response()->json([
            'success' => true,
            'status' => 'active',
            'title' => $session->title,
            'target_seconds' => $session->target_seconds,
            'started_at' => $session->started_at,
        ]);
    }

    public function endSession(Request $request)
    {
        $endType = $request->end_type ?? 'finish';

        $session = FocusSession::where('id', $request->session_id)
            ->where('user_id', session('user_id'))
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if (!$session) {
            return response()->json(['success' => false, 'message' => 'No session found.']);
        }

        if ($session->status === 'paused') {
            $breakSeconds = $this->closeOpenEvent($session, 'break');
            $session->break_seconds += $breakSeconds;
        } else {
            $this->closeOpenEvent($session, 'focused');
        }

        $remainingSeconds = max(0, (int) $request->remaining_seconds);
        $actualSeconds = max(1, $session->target_seconds - $remainingSeconds);

        if ($endType === 'cancel') {
            $session->update([
                'status' => 'cancelled',
                'ended_at' => now(),
                'actual_seconds' => $actualSeconds,
                'break_seconds' => $session->break_seconds,
                'result' => 'cancelled',
            ]);

            $this->createNotification($session, 'session_cancelled', 'Session cancelled.');

            return response()->json([
                'success' => true,
                'status' => 'cancelled',
                'result' => 'cancelled',
                'title' => $session->title,
                'target_seconds' => $session->target_seconds,
                'actual_seconds' => $actualSeconds,
            ]);
        }

        $result = $this->calculateResult($session);

        $session->update([
            'status' => 'completed',
            'ended_at' => now(),
            'actual_seconds' => $actualSeconds,
            'break_seconds' => $session->break_seconds,
            'result' => $result,
        ]);

        $this->createNotification($session, 'finished_' . $result, 'Session finished ' . $result . '.');

        return response()->json([
            'success' => true,
            'status' => 'completed',
            'result' => $result,
            'title' => $session->title,
            'target_seconds' => $session->target_seconds,
            'actual_seconds' => $actualSeconds,
        ]);
    }

    public function latestNotifications()
    {
        if (!session('user_id')) {
            return response()->json([]);
        }

        $notifications = Notification::where('user_id', session('user_id'))
            ->whereDate('created_at', today())
            ->latest('id')
            ->take(4)
            ->get()
            ->map(function ($notification) {
                return [
                    'time' => $notification->created_at
                        ? Carbon::parse($notification->created_at)->format('H:i')
                        : '--:--',
                    'type' => $notification->type,
                    'message' => $notification->message,
                ];
            });

        return response()->json($notifications);
    }

    public function notifications()
    {
        if (!session('user_id')) {
            return redirect('/login');
        }

        return view('notifications');
    }

    public function settings()
    {
        if (!session('user_id')) {
            return redirect('/login');
        }

        return view('settings');
    }

    private function closeOpenEvent($session, $type)
    {
        $event = SessionEvent::where('session_id', $session->id)
            ->where('user_id', session('user_id'))
            ->where('type', $type)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if (!$event) {
            return 0;
        }

        $seconds = max(1, Carbon::parse($event->started_at)->diffInSeconds(now()));

        $event->update([
            'ended_at' => now(),
            'duration_seconds' => $seconds,
        ]);

        return $seconds;
    }

    private function createNotification($session, $type, $message)
    {
        Notification::create([
            'user_id' => session('user_id'),
            'session_id' => $session->id,
            'session_title' => $session->title,
            'type' => $type,
            'message' => $message,
            'created_at' => now(),
        ]);
    }

    private function calculateResult($session)
    {
        if ($session->too_distracted_count >= 2) return 'bad';
        if ($session->distraction_count >= 4) return 'okay';
        if ($session->distraction_count >= 1) return 'good';

        return 'excellent';
    }

    private function makeSessionTitle($goal)
    {
        $clean = $this->cleanGoalText($goal);
        $lower = strtolower($clean);

        $project = $this->extractProjectName($clean);
        $subject = $this->detectSubject($lower);
        $action = $this->detectAction($lower);
        $tech = $this->detectTechnology($lower);

        if ($project && $subject === 'UI/UX') return $this->limitTitleWords($project . ' UI/UX');
        if ($project && $subject && $subject !== 'General') return $this->limitTitleWords($project . ' ' . $subject);
        if ($project && $action && $action !== 'Work') return $this->limitTitleWords($project . ' ' . $action);
        if ($subject && $action && $subject !== 'General' && $action !== 'Work') return $this->limitTitleWords($subject . ' ' . $action);
        if ($tech && $subject && $subject !== 'General') return $this->limitTitleWords($subject . ' ' . $tech);
        if ($tech && $action && $action !== 'Work') return $this->limitTitleWords($tech . ' ' . $action);
        if ($subject && $subject !== 'General') return $this->limitTitleWords($subject . ' Session');

        return $this->fallbackTitle($clean);
    }

    private function cleanGoalText($goal)
    {
        $goal = trim(preg_replace('/\s+/', ' ', $goal));
        $lower = strtolower($goal);

        $removePhrases = [
            'i want to', 'i need to', 'i have to', 'i am going to',
            'im going to', 'i will', 'working on', 'work on',
            'continue working on', 'start working on', 'try to',
            'need to', 'want to', 'have to', 'today i will',
            'today i want to',
        ];

        foreach ($removePhrases as $phrase) {
            if (str_starts_with($lower, $phrase)) {
                return trim(substr($goal, strlen($phrase)));
            }
        }

        return $goal;
    }

    private function extractProjectName($text)
    {
        $patterns = [
            '/(?:for|on|about|inside|within)\s+(?:the\s+)?([a-z0-9\s]+?)\s+(?:project|app|system|dashboard|website|platform)\b/i',
            '/(?:the\s+)?([a-z0-9\s]+?)\s+(?:project|app|system|dashboard|website|platform)\b/i',
            '/(?:project|app|system|dashboard|website|platform)\s+([a-z0-9\s]+)$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                return $this->titleCaseSmart(trim($match[1]));
            }
        }

        return null;
    }

    private function detectSubject($lower)
    {
        $subjects = [
            'Control Panel' => ['control panel'],
            'Notification Log' => ['notification log'],
            'Notifications' => ['notification', 'notifications', 'alert'],
            'Session Timer' => ['timer', 'countdown', 'time remaining'],
            'Stats Card' => ['stats card', 'stat card'],
            'Statistics' => ['statistics', 'stats', 'charts', 'chart'],
            'Dashboard' => ['dashboard'],
            'Settings' => ['settings'],
            'Authentication' => ['auth', 'authentication', 'login', 'register', 'password'],
            'Database' => ['database', 'mysql', 'table', 'sql'],
            'API' => ['api', 'endpoint', 'route'],
            'Backend' => ['backend', 'controller', 'model', 'server'],
            'Frontend' => ['frontend', 'front end', 'blade', 'html', 'css'],
            'UI/UX' => ['ui ux', 'ui/ux', 'ux', 'ui', 'interface', 'layout'],
            'JavaScript' => ['javascript', 'java script', 'js'],
            'Laravel' => ['laravel'],
            'Reports' => ['report', 'reports'],
            'Responsive UI' => ['responsive', 'mobile'],
        ];

        foreach ($subjects as $label => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) return $label;
            }
        }

        return 'General';
    }

    private function detectAction($lower)
    {
        $actions = [
            'Animation' => ['animation', 'animate', 'transition'],
            'Fix' => ['fix', 'bug', 'debug', 'repair', 'solve', 'issue', 'error'],
            'Refactor' => ['refactor', 'cleanup', 'clean up'],
            'Integration' => ['connect', 'connecting', 'integration', 'integrate'],
            'Design' => ['design', 'layout', 'style', 'visual'],
            'Development' => ['create', 'creating', 'build', 'building', 'make'],
            'Update' => ['update', 'change', 'modify', 'adjust', 'improve'],
            'Testing' => ['test', 'testing'],
            'Documentation' => ['documentation', 'document', 'docs'],
            'Optimization' => ['optimize', 'performance', 'speed'],
        ];

        foreach ($actions as $label => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) return $label;
            }
        }

        return 'Work';
    }

    private function detectTechnology($lower)
    {
        $techs = [
            'JavaScript' => ['javascript', 'java script', 'js'],
            'Laravel' => ['laravel'],
            'Blade' => ['blade'],
            'CSS' => ['css', 'style'],
            'MySQL' => ['mysql', 'sql'],
            'API' => ['api'],
            'ChartJS' => ['chartjs', 'chart.js'],
        ];

        foreach ($techs as $label => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lower, $keyword)) return $label;
            }
        }

        return null;
    }

    private function fallbackTitle($text)
    {
        $words = preg_split('/\s+/', $text);
        $blocked = ['the','a','an','and','or','to','for','of','in','on','with','from','by','my','this','that','today'];
        $important = [];

        foreach ($words as $word) {
            $cleanWord = preg_replace('/[^a-zA-Z0-9]/', '', $word);
            if (!$cleanWord || in_array(strtolower($cleanWord), $blocked)) continue;

            $important[] = $this->titleCaseSmart($cleanWord);
            if (count($important) >= 4) break;
        }

        return count($important) >= 2
            ? implode(' ', $important)
            : $this->titleCaseSmart(substr($text, 0, 35));
    }

    private function limitTitleWords($title)
    {
        $words = preg_split('/\s+/', trim($title));
        return count($words) <= 4 ? trim($title) : implode(' ', array_slice($words, 0, 4));
    }

    private function titleCaseSmart($text)
    {
        $special = [
            'api' => 'API', 'ui' => 'UI', 'ux' => 'UX', 'ui/ux' => 'UI/UX',
            'js' => 'JS', 'javascript' => 'JavaScript', 'mysql' => 'MySQL',
            'css' => 'CSS', 'html' => 'HTML', 'php' => 'PHP',
            'json' => 'JSON', 'laravel' => 'Laravel',
        ];

        $words = preg_split('/\s+/', trim($text));
        $final = [];

        foreach ($words as $word) {
            $clean = strtolower(trim($word));
            $final[] = $special[$clean] ?? ucfirst($clean);
        }

        return implode(' ', $final);
    }
}