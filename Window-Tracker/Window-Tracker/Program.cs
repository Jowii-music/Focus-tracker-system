using System;
using System.Threading;
using System.Threading.Tasks;
using System.Diagnostics;
using System.Runtime.InteropServices;
using System.IO;
using System.Text.Json;
using System.Net.Http;
using System.Text;
using System.Collections.Generic;
using System.Windows.Forms;
using System.Drawing;

// MODELS
class SessionEvent
{
    public string BatchId { get; set; }
    public int UserId { get; set; }
    public string Source { get; set; } = "console";
    public string AppName { get; set; }

    public string SiteUrl { get; set; }
    public string SiteTitle { get; set; }

    public string Category { get; set; }

    public DateTime StartedAt { get; set; }
    public DateTime EndedAt { get; set; }
    public long DurationSeconds { get; set; }

    public bool Processed { get; set; }

    public DateTime ActivityDate { get; set; }
    public DateTime CreatedAt { get; set; }
}

// ===================== INPUT =====================
static class Input
{
    public static string GetActiveProcessName()
    {
        var hwnd = NativeMethods.GetForegroundWindow();
        if (hwnd == IntPtr.Zero)
            return null;

        NativeMethods.GetWindowThreadProcessId(hwnd, out uint pid);

        try
        {
            return Process.GetProcessById((int)pid).ProcessName;
        }
        catch
        {
            return null;
        }
    }
}

// ===================== STATE =====================
static class State
{
    public static HttpClient HttpClient = new HttpClient();

    public static string LastProcess;
    public static DateTime SessionStartTime;

    public static bool IsRunning = true;

    public static List<SessionEvent> Batch = new List<SessionEvent>();

    public static string Batch_id = Guid.NewGuid().ToString();

    public static double DistractionThresholdMinutes = 1;
}

// ===================== LOGIC =====================
static class Logic
{
    public static void HandleProcessChange(string activeProcess)
    {
        if (activeProcess == State.LastProcess)
            return;

        if (State.LastProcess != null)
        {
            var endTime = DateTime.Now;

            var session = Helpers.CreateSessionEvent(
                State.LastProcess,
                State.SessionStartTime,
                endTime
            );

            State.Batch.Add(session);

            Console.WriteLine(
                $"EVENT | {session.AppName} | {session.StartedAt:HH:mm:ss} → {session.EndedAt:HH:mm:ss} | {session.DurationSeconds}s | {session.Category}"
            );
        }

        State.LastProcess = activeProcess;
        State.SessionStartTime = DateTime.Now;
    }

    public static DateTime? distractionStart = null;

    public static void DetectNotification(string activeProcess)
    {
        var category = Helpers.GetCategory(activeProcess);

        if (category == "distracted")
        {
            if (distractionStart == null)
            {
                distractionStart = DateTime.Now;
            }
            var minutes = (DateTime.Now - distractionStart.Value).TotalMinutes;

            if (minutes > State.DistractionThresholdMinutes)
            {
                Console.WriteLine("⚠️ Distracted for 5+ minutes");

                //Later: GenerateNotification()

                distractionStart = DateTime.Now;
            }
        }
        else
        {
            distractionStart = null;
        }
    }
}

// ===================== HELPERS =====================
static class Helpers
{
    public static SessionEvent CreateSessionEvent(string appName, DateTime start, DateTime end)
    {
        return new SessionEvent
        {
            BatchId = State.Batch_id,
            UserId = 1,

            AppName = appName,

            Category = GetCategory(appName),

            StartedAt = start,
            EndedAt = end,
            DurationSeconds = (long)(end - start).TotalSeconds,

            ActivityDate = start.Date,
            CreatedAt = DateTime.Now
        };
    }

    public static string GetCategory(string app)
    {
        return app switch
        {
            "Code" => "productive",
            "WindowsTerminal" => "productive",
            "devenv" => "productive",
            "teams" => "productive",

            "chrome" => "distracted",
            "msedge" => "distracted",
            "firefox" => "distracted",

            "Discord" => "distracted",
            "WhatsApp.Root" => "distracted",

            _ => "unknown"
        };
    }
}

// ===================== OUTPUT =====================
static class Output
{
    public static async Task SendBatchToServer(List<SessionEvent> batch)
    {
        Console.WriteLine("Sending batch to server...");
        string batchCount = batch.Count.ToString();

        try
        {
            string json = JsonSerializer.Serialize(batch);

            var content = new StringContent(json, Encoding.UTF8, "application/json");

            Console.WriteLine($"POST http://localhost:3000/sessions/batch");

            var response = await State.HttpClient.PostAsync(
                "http://localhost:3000/sessions/batch",
                content
            );

            if (response.IsSuccessStatusCode)
            {
                //Console.WriteLine("Batch successfully sent to server.");
                InitNotify();
                notify.ShowBalloonTip(
                    3000,
                    "batch delivered",
                    "Your session batch has been successfully delivered to the server.",
                    ToolTipIcon.Info
                    );
            }
            else
            {
                Console.WriteLine($"Server error: {response.StatusCode}");
            }
        }
        catch (Exception ex)
        {
            Console.WriteLine($"Failed to send batch: {ex.Message}");
        }
    }

    static NotifyIcon notify;
    public static void InitNotify()
    {
        notify = new NotifyIcon
        {
            Icon = SystemIcons.Application,
            Visible = true,
            Text = "Batch has been deliverd!"
        };
    }

    public static void SaveBatchToFile(List<SessionEvent> batch)
    {
        File.WriteAllText(
            "sessions.json",
            System.Text.Json.JsonSerializer.Serialize(batch, new System.Text.Json.JsonSerializerOptions
            {
                WriteIndented = true
            })
        );
    }
}

// ===================== NATIVE METHODS =====================
static class NativeMethods
{
    [DllImport("user32.dll")]
    public static extern IntPtr GetForegroundWindow();

    [DllImport("user32.dll")]
    public static extern uint GetWindowThreadProcessId(
        IntPtr hWnd,
        out uint lpdwProcessId
    );
}

// ===================== CONTROL =====================
static class Control
{
    public static void StartStopThread()
    {
        new Thread(() =>
        {
            Console.ReadLine();
            State.IsRunning = false;
        }).Start();
    }

    public static void Shutdown()
    {
        Console.WriteLine("Tracker stopped.");
        Output.SendBatchToServer(State.Batch).Wait();
        Console.WriteLine(Directory.GetCurrentDirectory());
        Output.SaveBatchToFile(State.Batch);
    }
}


class Program
{
    static void Main(string[] args)
    {
        Control.StartStopThread();
        while (State.IsRunning)
        {
            var activeProcess = Input.GetActiveProcessName();

            Logic.HandleProcessChange(activeProcess);
            Logic.DetectNotification(activeProcess);

            Thread.Sleep(1000);
        }
        Control.Shutdown();
    }
}