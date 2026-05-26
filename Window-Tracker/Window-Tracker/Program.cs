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

class Program
{
    [DllImport("user32.dll")]
    static extern IntPtr GetForegroundWindow();

    [DllImport("user32.dll")]
    static extern uint GetWindowThreadProcessId(IntPtr hWnd, out uint lpdwProcessId);

    static HttpClient httpClient = new HttpClient();

    static string GetActiveProcessName()
    {
        IntPtr hwnd = GetForegroundWindow();
        if (hwnd == IntPtr.Zero)
            return null;

        uint pid;
        GetWindowThreadProcessId(hwnd, out pid);

        try
        {
            Process p = Process.GetProcessById((int)pid);
            return p.ProcessName;
        }
        catch
        {
            return null;
        }
    }

    static string lastProcess = null;
    static DateTime sessionStartTime;

    static bool isRunning = true;

    static List<SessionEvent> batch = new List<SessionEvent>();

    static void Main(string[] args)
    {
        Thread stopThread = new Thread(() =>
        {
            Console.ReadLine();
            isRunning = false;
        });

        stopThread.Start();

        while (isRunning)
        {
            string activeProcess = GetActiveProcessName();

            if (activeProcess != lastProcess)
            {
                if (lastProcess != null)
                {
                    DateTime endTime = DateTime.Now;

                    var session = CreateSessionEvent(lastProcess, sessionStartTime, endTime);
                    batch.Add(session);

                    Console.WriteLine(
                        $"EVENT | {session.AppName} | {session.StartedAt:HH:mm:ss} → {session.EndedAt:HH:mm:ss} | {session.DurationSeconds}s | {session.Category}"
                    );
                }

                lastProcess = activeProcess;
                sessionStartTime = DateTime.Now;
            }

            Thread.Sleep(1000);
        }

        Console.WriteLine("Tracker stopped.");
        SendBatchToServer(batch).Wait();
        Console.WriteLine(Directory.GetCurrentDirectory());

        File.WriteAllText(
            "sessions.json",
            System.Text.Json.JsonSerializer.Serialize(batch, new System.Text.Json.JsonSerializerOptions
            {
                WriteIndented = true
            })
        );

        
    }

    static string Batch_id = Guid.NewGuid().ToString();

    static SessionEvent CreateSessionEvent(string appName, DateTime start, DateTime end)
    {
        return new SessionEvent
        {
            BatchId = Batch_id,
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

    static string GetCategory(string app)
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

    static async Task SendBatchToServer(List<SessionEvent> batch)
    {
        Console.WriteLine("Sending batch to server...");
        string batchCount = batch.Count.ToString();

        try
        {
            string json = JsonSerializer.Serialize(batch);

            var content = new StringContent(json, Encoding.UTF8, "application/json");

            Console.WriteLine($"POST http://localhost:3000/sessions/batch");

            var response = await httpClient.PostAsync(
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
    static void InitNotify()
    {
        notify = new NotifyIcon
        {
            Icon= SystemIcons.Application,
            Visible = true,
            Text = "Batch has been deliverd!"
        };
    }
}