# Focus Tracker System 🎯
 
A productivity tracking system designed to help users understand how they spend their time on their computer.
 
The system monitors desktop applications and browser tabs, processes the collected data, and presents it through a modern Laravel dashboard with statistics and insights.
 
---
 
## Features
 
- 📊 Productivity dashboard
- 🖥️ Desktop application tracking
- 🌐 Chrome tab tracking
- 📈 Application usage statistics
- ⏱️ Time tracking
- 🔔 Notification system
- 💾 Database integration
- 👤 User authentication
- 📱 Responsive dashboard interface
 
---
 
# System Architecture
 
The project consists of four components working together.
 
## Dashboard
 
The Laravel dashboard serves as the central interface where users can:
 
- View tracked activity
- Monitor application usage
- Analyze productivity statistics
- Manage their account
- Receive notifications
 
**Technologies**
 
- Laravel
- PHP
- SQL
- HTML
- CSS
- JavaScript
 
---
 
## Server
 
The server acts as the communication layer between the tracking applications and the dashboard.
 
It receives activity data, processes it, and stores it in the database for visualization inside the dashboard.
 
---
 
## Window Tracker
 
A desktop application that monitors which Windows applications are currently active.
 
Examples include:
 
- Visual Studio
- VS Code
- Chrome
- Discord
- Steam
- Spotify
 
The collected activity is sent to the server.
 
---
 
## Tab Tracker
 
A custom Chrome Extension that tracks individual browser tabs instead of treating Google Chrome as a single application.
 
This provides much more detailed productivity information by identifying which websites are actually being used.
 
Examples:
 
- GitHub
- YouTube
- Stack Overflow
- ChatGPT
- Google Docs
 
---
 
# Technologies
 
- Laravel
- PHP
- SQL
- HTML
- CSS
- JavaScript
- Chrome Extension API
- C#
- REST Communication
 
---
 
# Project Structure
 
```
Focus-Tracker-System
│
├── Dashboard
├── Server
├── Tab-Tracker
├── Window-Tracker
├── focus_tracker.sql
└── notifications.sql
```
 
---
 
# Purpose
 
This project was developed to provide detailed productivity tracking by combining desktop monitoring, browser tracking, backend processing, and a modern web dashboard into one integrated system.
 
---
 
# Developers
 
- Abdullah Zenaty (AL-ZENATY)
 
- Joey Steehouwer (Jowii-music)
 
- Abdulrahman Saee (Abdulrahman7170)
