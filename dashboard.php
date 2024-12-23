<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPanel - IPTV Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@supabase/supabase-js@2"></script>
    <script src="https://accounts.google.com/gsi/client"></script>
    <script src="https://telegram.org/js/telegram-widget.js?22"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }

        .dashboard-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.5rem;
            padding: 1rem;
            backdrop-filter: blur(8px);
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            background: rgba(30, 41, 59, 0.8);
            transform: scale(1.02);
        }

        .sidebar {
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
        }

        .sidebar.collapsed {
            width: 5rem;
            transform: translateX(0);
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            visibility: hidden;
        }

        .main-content {
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 5rem;
        }

        .shortcut-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .shortcut-card {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .shortcut-card:hover {
            background: rgba(59, 130, 246, 0.3);
            transform: translateY(-3px);
        }

        .chart-container {
            position: relative;
            margin: 20px 0;
            padding: 15px;
            border-radius: 10px;
            background: rgba(30, 41, 59, 0.3);
            transition: transform 0.3s ease;
        }

        .chart-container:hover {
            transform: scale(1.02);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 1rem;
        }

        .movie-card {
            background: rgba(30, 41, 59, 0.7);
            border-radius: 0.5rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .server-status {
            padding: 1rem;
            border-radius: 0.5rem;
            background: rgba(30, 41, 59, 0.7);
            margin-bottom: 1rem;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-good {
            background-color: #10B981;
        }

        .status-warning {
            background-color: #F59E0B;
        }

        .status-error {
            background-color: #EF4444;
        }

        .server-metric {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
        }

        .metric-bar {
            flex-grow: 1;
            height: 8px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin: 0 1rem;
        }

        .metric-fill {
            height: 100%;
            background: rgb(59, 130, 246);
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-black min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 bg-gray-900 border-r border-blue-900/30">
        <div class="flex justify-between items-center p-4">
            <div>
                <h1 class="text-2xl font-bold text-white logo-text animate__animated animate__fadeIn">📺 MiPanel</h1>
                <p class="text-blue-400 text-sm logo-text">IPTV Admin</p>
            </div>
            <button onclick="toggleSidebar()" class="text-white hover:bg-gray-800 p-2 rounded transition-all duration-300 transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>

        <nav class="mt-8">
            <div class="px-4 mb-2 text-xs font-semibold text-gray-400 uppercase nav-text">IPTV Management</div>
            <a href="#dashboard" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="ml-3 nav-text">Dashboard</span>
            </a>
            <a href="#movies" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <span class="ml-3 nav-text">Movies</span>
            </a>
            <a href="#streams" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="ml-3 nav-text">Live Streams</span>
            </a>
            <a href="#vod" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <span class="ml-3 nav-text">VOD Content</span>
            </a>
            <a href="#epg" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="ml-3 nav-text">EPG Manager</span>
            </a>

            <div class="px-4 mt-8 mb-2 text-xs font-semibold text-gray-400 uppercase nav-text">System</div>
            <a href="#users" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="ml-3 nav-text">Users</span>
            </a>
            <a href="#billing" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="ml-3 nav-text">Billing</span>
            </a>
            <a href="#settings" class="flex items-center px-4 py-3 text-white hover:bg-blue-600/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="ml-3 nav-text">Settings</span>
            </a>
        </nav>
    </div>    <!-- Main Content -->
    <div id="mainContent" class="main-content ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white animate__animated animate__fadeIn">IPTV Dashboard</h1>
                <p class="text-blue-400">Welcome back, <span id="adminName">Loading...</span></p>
            </div>
            <div class="flex items-center space-x-4">
                <select class="bg-gray-800 text-white px-4 py-2 rounded-md">
                    <option value="en">English</option>
                    <option value="ru">Русский</option>
                    <option value="lt">Lietuvių</option>
                </select>
                <button onclick="refreshStats()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                    Refresh Stats
                </button>
            </div>
        </div>

        <!-- Server Status Section -->
        <div class="dashboard-card mb-8">
            <h3 class="text-lg font-semibold text-white mb-4">Server Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="server-status">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-white">Main Server</span>
                        <div id="mainServerStatus">
                            <span class="status-indicator status-good"></span>
                            <span class="text-green-400">Operational</span>
                        </div>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">CPU</span>
                        <div class="metric-bar">
                            <div id="cpuUsage" class="metric-fill" style="width: 45%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="cpuPercent">45%</span>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">Memory</span>
                        <div class="metric-bar">
                            <div id="memoryUsage" class="metric-fill" style="width: 60%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="memoryPercent">60%</span>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">Disk</span>
                        <div class="metric-bar">
                            <div id="diskUsage" class="metric-fill" style="width: 75%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="diskPercent">75%</span>
                    </div>
                </div>
                <div class="server-status">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-white">Backup Server</span>
                        <div id="backupServerStatus">
                            <span class="status-indicator status-good"></span>
                            <span class="text-green-400">Operational</span>
                        </div>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">CPU</span>
                        <div class="metric-bar">
                            <div id="backupCpuUsage" class="metric-fill" style="width: 30%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="backupCpuPercent">30%</span>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">Memory</span>
                        <div class="metric-bar">
                            <div id="backupMemoryUsage" class="metric-fill" style="width: 45%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="backupMemoryPercent">45%</span>
                    </div>
                    <div class="server-metric">
                        <span class="text-gray-300 w-24">Disk</span>
                        <div class="metric-bar">
                            <div id="backupDiskUsage" class="metric-fill" style="width: 50%"></div>
                        </div>
                        <span class="text-gray-300 w-16" id="backupDiskPercent">50%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shortcuts -->
        <div class="shortcut-grid animate__animated animate__fadeIn">
            <div class="shortcut-card">
                <svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span class="text-white">Add Stream</span>
            </div>
            <div class="shortcut-card">
                <svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="text-white">Add Movie</span>
            </div>
            <div class="shortcut-card">
                <svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="text-white">Add User</span>
            </div>
            <div class="shortcut-card">
                <svg class="w-8 h-8 mx-auto mb-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-white">Analytics</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card animate__animated animate__fadeIn">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Active Users</h3>
                    <span class="text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                </div>
                <div id="activeUsers" class="text-3xl font-bold text-white">Loading...</div>
                <div id="usersTrend" class="text-green-400 text-sm">Calculating...</div>
            </div>

            <div class="dashboard-card animate__animated animate__fadeIn" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Live Channels</h3>
                    <span class="text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </span>
                </div>
                <div id="liveChannels" class="text-3xl font-bold text-white">Loading...</div>
                <div id="channelStatus" class="text-blue-400 text-sm">Checking status...</div>
            </div>

            <div class="dashboard-card animate__animated animate__fadeIn" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Bandwidth Usage</h3>
                    <span class="text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </span>
                </div>
                <div id="bandwidth" class="text-3xl font-bold text-white">Loading...</div>
                <div id="bandwidthTrend" class="text-yellow-400 text-sm">Monitoring...</div>
            </div>

            <div class="dashboard-card animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">Monthly Revenue</h3>
                    <span class="text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <div id="revenue" class="text-3xl font-bold text-white">Loading...</div>
                <div id="revenueTrend" class="text-green-400 text-sm">Calculating...</div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Viewer Statistics</h3>
                <canvas id="viewerChart" height="200"></canvas>
            </div>
            <div class="dashboard-card">
                <h3 class="text-lg font-semibold text-white mb-4">Popular Channels</h3>
                <canvas id="channelChart" height="200"></canvas>
            </div>
        </div>

        <!-- System Status -->
        <div class="dashboard-card mb-8">
            <h3 class="text-lg font-semibold text-white mb-4">System Status</h3>
            <div id="systemStatus" class="space-y-4">
                <!-- Status items will be dynamically inserted here -->
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center text-gray-400 py-4 border-t border-gray-800">
            <p>© 2024 MiPanel IPTV Management System</p>
            <p class="text-sm">Version 2.0.1 | <a href="#" class="text-blue-400 hover:text-blue-300">Documentation</a> | <a href="#" class="text-blue-400 hover:text-blue-300">Support</a></p>
        </footer>
    </div>

    <script>
        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }

        // Initialize charts and load data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            loadDashboardData();
            setInterval(loadDashboardData, 30000); // Refresh every 30 seconds
        });

        function initializeCharts() {
            // Viewer Statistics Chart
            const viewerCtx = document.getElementById('viewerChart').getContext('2d');
            new Chart(viewerCtx, {
                type: 'line',
                data: {
                    labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
                    datasets: [{
                        label: 'Active Viewers',
                        data: [120, 190, 300, 250, 400, 380],
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' }
                        },
                        x: {
                            ticks: { color: 'white' }
                        }
                    }
                }
            });

            // Popular Channels Chart
            const channelCtx = document.getElementById('channelChart').getContext('2d');
            new Chart(channelCtx, {
                type: 'bar',
                data: {
                    labels: ['Sports 1', 'Movies HD', 'News 24/7', 'Kids TV', 'Music Hits'],
                    datasets: [{
                        label: 'Viewers',
                        data: [400, 350, 300, 250, 200],
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: 'white'
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: { color: 'white' }
                        },
                        x: {
                            ticks: { color: 'white' }
                        }
                    }
                }
            });
        }

        async function loadDashboardData() {
            try {
                // Simulate API call - replace with actual API endpoints
                const response = await fetch('/api/dashboard/stats');
                const data = await response.json();
                
                // Update dashboard elements
                updateStats(data);
                updateSystemStatus(data.systemStatus);
            } catch (error) {
                console.error('Error loading dashboard data:', error);
            }
        }

        function updateStats(data) {
            // Update statistics with real data
            document.getElementById('adminName').textContent = data?.adminName || 'Admin';
            document.getElementById('activeUsers').textContent = data?.activeUsers || '0';
            document.getElementById('liveChannels').textContent = data?.liveChannels || '0';
            document.getElementById('bandwidth').textContent = data?.bandwidth || '0 Mbps';
            document.getElementById('revenue').textContent = data?.revenue || '$0';
        }

        function updateSystemStatus(status) {
            const statusContainer = document.getElementById('systemStatus');
            statusContainer.innerHTML = ''; // Clear existing status items
            
            // Add status items dynamically
            const statusItems = [
                { name: 'Streaming Servers', status: 'Operational', color: 'green' },
                { name: 'Database', status: 'Operational', color: 'green' },
                { name: 'CDN', status: 'Degraded', color: 'yellow' },
                { name: 'API Services', status: 'Operational', color: 'green' }
            ];

            statusItems.forEach(item => {
                const statusHtml = `
                    <div class="flex items-center justify-between p-4 bg-gray-800/50 rounded-md">
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 rounded-full bg-${item.color}-500"></div>
                            <span class="text-white">${item.name}</span>
                        </div>
                        <span class="text-${item.color}-400">${item.status}</span>
                    </div>
                `;
                statusContainer.innerHTML += statusHtml;
            });
        }
    </script>
</body>
</html>

