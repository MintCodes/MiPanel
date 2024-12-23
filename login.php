<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPanel - Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@supabase/supabase-js@2"></script>
    <script src="https://accounts.google.com/gsi/client"></script>
    <script src="https://telegram.org/js/telegram-widget.js?22"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        const supabaseUrl = 'YOUR_SUPABASE_URL'
        const supabaseKey = 'YOUR_SUPABASE_ANON_KEY'
        const supabase = supabase.createClient(supabaseUrl, supabaseKey)

        let lastChecked = '';
        let onlineUsers = 0;
        let activeStreams = 0;
        const VERSION = '1.0.0';
        
        const translations = {
            en: {
                email: "📧 Email",
                password: "🔒 Password", 
                twoFactor: "🔑 2FA Code",
                signIn: "Sign in",
                resetPassword: "Reset Password",
                adminDashboard: "Admin Dashboard",
                back: "Back to Login",
                resetCredentials: "Reset Credentials",
                enterPin: "Enter PIN",
                loginWithPin: "Login with PIN"
            },
            lt: {
                email: "📧 El. paštas",
                password: "🔒 Slaptažodis",
                twoFactor: "🔑 2FA kodas", 
                signIn: "Prisijungti",
                resetPassword: "Atkurti slaptažodį",
                adminDashboard: "Administratoriaus skydelis",
                back: "Grįžti į prisijungimą",
                resetCredentials: "Atkurti kredencialus",
                enterPin: "Įveskite PIN",
                loginWithPin: "Prisijungti su PIN"
            },
            ru: {
                email: "📧 Эл. почта",
                password: "🔒 Пароль",
                twoFactor: "🔑 2FA код",
                signIn: "Войти", 
                resetPassword: "Сбросить пароль",
                adminDashboard: "Панель администратора",
                back: "Вернуться к входу",
                resetCredentials: "Сбросить учетные данные",
                enterPin: "Введите PIN",
                loginWithPin: "Войти с PIN"
            }
        };

        let currentLang = 'en';
        
        // Version check on load
        async function checkVersion() {
            try {
                const response = await fetch('https://raw.githubusercontent.com/MintCodes/MiPanel/main/version.json');
                const data = await response.json();
                lastChecked = new Date().toLocaleTimeString();
                
                document.getElementById('versionInfo').innerHTML = `<strong>Version ${VERSION}</strong> - Last checked: ${lastChecked}`;
                document.getElementById('lastChecked').innerHTML = `Last version check: ${lastChecked}`;
                
                if (data.version === VERSION) {
                    showNotification('✅ MiPanel is up to date!');
                } else {
                    showNotification('⚠️ New version available!');
                }
            } catch (error) {
                console.error('Version check failed:', error);
            }
        }

        async function updateOnlineUsers() {
            try {
                const { data, error } = await supabase
                    .from('active_sessions')
                    .select('count', { count: 'exact' });
                
                if (error) throw error;
                onlineUsers = data;
                document.getElementById('onlineUsers').textContent = onlineUsers;
            } catch (error) {
                console.error('Failed to fetch online users:', error);
            }
        }

        async function updateActiveStreams() {
            try {
                const { data, error } = await supabase
                    .from('active_streams')
                    .select('count', { count: 'exact' });
                
                if (error) throw error;
                activeStreams = data;
                document.getElementById('activeStreams').textContent = activeStreams;
            } catch (error) {
                console.error('Failed to fetch active streams:', error);
            }
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-md shadow-lg transition-opacity duration-500';
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 500);
            }, 2500);
        }

        window.onload = () => {
            checkVersion();
            updateOnlineUsers();
            updateActiveStreams();
            setInterval(checkVersion, 60000);
            setInterval(updateOnlineUsers, 60000);
            setInterval(updateActiveStreams, 60000);
            
            // Generate QR code for Telegram login
            QRCode.toCanvas(document.getElementById('telegram-qr'), 'https://t.me/YOUR_BOT_USERNAME', function (error) {
                if (error) console.error(error)
            })

            // Initialize particles
            particlesJS("particles-js", {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: "#3b82f6" },
                    opacity: { value: 0.1 },
                    size: { value: 3 },
                    line_linked: { enable: true, distance: 150, color: "#3b82f6", opacity: 0.1, width: 1 },
                    move: { enable: true, speed: 2, direction: "none", random: false, straight: false }
                }
            });
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(45deg, #0f172a, #1e3a8a, #1e40af, #0f172a);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite, pulse 2s ease-in-out infinite, glow 4s ease-in-out infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes pulse {
            0% { filter: brightness(1); }
            50% { filter: brightness(1.2); }
            100% { filter: brightness(1); }
        }

        @keyframes glow {
            0% { box-shadow: 0 0 50px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 100px rgba(59, 130, 246, 0.5); }
            100% { box-shadow: 0 0 50px rgba(59, 130, 246, 0.3); }
        }

        .grid-bg {
            background-image: 
                linear-gradient(rgba(62, 184, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(62, 184, 255, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .pin-input {
            width: 60px;
            height: 60px;
            text-align: center;
            margin: 0 6px;
            font-size: 24px;
        }

        .telegram-menu {
            position: fixed;
            right: -300px;
            top: 0;
            height: 100vh;
            width: 300px;
            transition: right 0.3s ease;
        }

        .telegram-menu.active {
            right: 0;
        }

        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .tooltip {
            position: relative;
        }

        .tooltip:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 4px 8px;
            background: rgba(0,0,0,0.8);
            color: white;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 5px;
        }

        .language-selector {
            position: fixed;
            bottom: 4px;
            right: -200px;
            transition: right 0.3s ease;
            z-index: 50;
        }

        .language-selector:hover {
            right: 0;
        }

        .language-dropdown {
            position: relative;
            display: inline-block;
        }

        .language-btn {
            padding: 0.5rem;
            background: rgba(30, 58, 138, 0.3);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: white;
            border-radius: 0.375rem;
            backdrop-filter: blur(4px);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .language-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: rgba(30, 58, 138, 0.9);
            backdrop-filter: blur(4px);
            min-width: 120px;
            border-radius: 0.375rem;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .language-dropdown-content button {
            color: white;
            padding: 0.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            transition: background 0.2s;
        }

        .language-dropdown-content button:hover {
            background: rgba(59, 130, 246, 0.5);
        }

        .language-dropdown:hover .language-dropdown-content {
            display: block;
        }

        .flag {
            width: 24px;
            height: 16px;
            border-radius: 2px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-black min-h-screen flex items-center justify-center p-4">
    <div id="particles-js"></div>
    <div class="flex w-[1000px] h-[600px] bg-gray-900/90 shadow-lg rounded-md overflow-hidden backdrop-blur-sm">
        <!-- Left Side - Preview -->
        <div class="w-1/2 border-r border-blue-900/30 relative overflow-hidden">
            <div class="absolute inset-0 gradient-bg opacity-30"></div>
            <div class="absolute inset-0 grid-bg"></div>
            
            <div class="relative h-full flex flex-col items-center justify-center p-8 space-y-8">
                <div class="flex flex-col items-center space-y-6">
                    <div class="w-24 h-24 bg-blue-500/10 rounded-md flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7h16M4 11h16M4 15h16M4 19h16M4 3h16"/>
                            <rect x="2" y="3" width="20" height="14" rx="2" stroke-width="1.5"/>
                            <path d="M12 17v4" stroke-width="1.5"/>
                            <path d="M8 21h8" stroke-width="1.5"/>
                        </svg>
                    </div>
                    
                    <div class="text-center space-y-2">
                        <h2 class="text-2xl font-bold text-blue-400 tracking-tight">📺 IPTV Control Center</h2>
                        <p class="text-blue-200/70 max-w-xs font-medium">Advanced IPTV management system with real-time monitoring and control</p>
                        <div class="text-blue-400 text-sm font-bold">Version 1.0.0</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 w-full max-w-md">
                    <div class="bg-blue-500/5 p-4 rounded-md border border-blue-500/20">
                        <div class="text-blue-400 text-sm font-medium">📺 Active Streams</div>
                        <div id="activeStreams" class="text-2xl font-bold text-blue-300 mt-1">0</div>
                    </div>
                    <div class="bg-blue-500/5 p-4 rounded-md border border-blue-500/20">
                        <div class="text-blue-400 text-sm font-medium">👥 Users Online</div>
                        <div id="onlineUsers" class="text-2xl font-bold text-blue-300 mt-1">0</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-1/2 p-12 flex flex-col">
            <div class="mb-12">
                <h1 class="text-3xl font-bold text-white tracking-tight">📺 MiPanel</h1>
                <p class="text-blue-200/70 mt-2 font-medium" id="adminDashboardText">Admin Dashboard</p>
            </div>
            
            <!-- Main Login Form -->
            <form id="loginForm" class="space-y-6 flex-1">
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2" id="emailLabel">📧 Email</label>
                    <div class="flex space-x-2">
                        <input type="email" id="email" required 
                            class="block flex-1 px-4 py-3 bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500 transition-colors duration-200">
                        <button type="button" id="resetBtn"
                            class="tooltip px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200 flex items-center"
                            onclick="toggleForm('resetForm')"
                            data-tooltip="Reset Password">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2" id="passwordLabel">🔒 Password</label>
                    <div class="flex space-x-2">
                        <input type="password" id="password" required 
                            class="block flex-1 px-4 py-3 bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500 transition-colors duration-200">
                        <button type="button" id="pinBtn"
                            class="tooltip px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200 flex items-center"
                            onclick="toggleForm('pinLoginForm')"
                            data-tooltip="PIN Login">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2" id="twoFactorLabel">🔑 2FA Code</label>
                    <div class="flex space-x-2">
                        <input type="text" id="2fa" required 
                            class="block flex-1 px-4 py-3 bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500 transition-colors duration-200">
                        <button type="button" id="telegramLogin"
                            class="tooltip px-4 py-3 bg-[#0088cc] hover:bg-[#0077b5] text-white font-medium rounded-md transition-colors duration-200 flex items-center"
                            onclick="document.querySelector('.telegram-menu').classList.toggle('active')"
                            data-tooltip="Telegram Login">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.161c-.18 1.897-.962 6.502-1.359 8.627-.168.9-.5 1.201-.82 1.23-.697.064-1.226-.461-1.901-.903-1.056-.692-1.653-1.123-2.678-1.799-1.185-.781-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.139-5.062 3.345-.479.329-.913.489-1.302.481-.428-.008-1.252-.241-1.865-.44-.752-.244-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.831-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635.099-.002.321.023.465.178.12.13.145.309.164.433-.001.133-.01.293-.019.413z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <button type="submit" 
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200" id="signInBtn">
                        Sign in
                    </button>
                </div>
            </form>

            <!-- PIN Login Form (Hidden by default) -->
            <form id="pinLoginForm" class="space-y-6 flex-1 hidden">
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2">
                        <span class="flex items-center" id="enterPinLabel">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Enter PIN
                        </span>
                    </label>
                    <div class="flex justify-center mt-4">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                        <input type="password" maxlength="1" class="pin-input bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <button type="submit" 
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200" id="loginWithPinBtn">
                        Login with PIN
                    </button>
                </div>
                <div>
                    <button type="button" onclick="toggleForm('loginForm')"
                        class="w-full py-3 px-4 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200" id="backBtn">
                        Back to Login
                    </button>
                </div>
            </form>

            <!-- Reset Form (Hidden by default) -->
            <form id="resetForm" class="space-y-6 flex-1 hidden">
                <div>
                    <label class="block text-sm font-medium text-blue-200 mb-2">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Email
                        </span>
                    </label>
                    <input type="email" id="resetEmail" required 
                        class="block w-full px-4 py-3 bg-gray-800 border border-gray-700 text-white rounded-md focus:outline-none focus:border-blue-500 transition-colors duration-200">
                </div>
                <div>
                    <button type="submit" 
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200" id="resetCredentialsBtn">
                        Reset Credentials
                    </button>
                </div>
                <div>
                    <button type="button" onclick="toggleForm('loginForm')"
                        class="w-full py-3 px-4 bg-gray-700 hover:bg-gray-600 text-white font-medium rounded-md transition-colors duration-200" id="backToLoginBtn">
                        Back to Login
                    </button>
                </div>
            </form>

            <div class="pt-6 text-center border-t border-gray-800 mt-auto">
                <div id="versionInfo" class="text-blue-200/50 text-xs mt-4"></div>
            </div>
        </div>
    </div>

    <!-- Telegram Login Menu -->
    <div class="telegram-menu bg-gray-900/95 p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-white font-bold">Telegram Login</h3>
            <button onclick="document.querySelector('.telegram-menu').classList.remove('active')"
                class="text-gray-400 hover:text-white">
                ✕
            </button>
        </div>
        <div class="text-center mb-4">
            <p class="text-white mb-2">Scan this QR code with your Telegram app to login</p>
            <canvas id="telegram-qr" class="mx-auto"></canvas>
            <p class="text-blue-400 mt-4 font-medium">Coming Soon!</p>
        </div>
    </div>

    <footer class="fixed bottom-4 text-center flex items-center space-x-4">
        <a href="https://github.com/yourusername/mipanel" target="_blank" rel="noopener noreferrer" 
           class="inline-flex items-center space-x-2 text-blue-400 hover:text-blue-300 transition-colors duration-200">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"></path>
            </svg>
            <span>View on GitHub</span>
        </a>
        <div class="language-selector">
            <div class="language-dropdown">
                <button class="language-btn">
                    <img src="https://flagcdn.com/w40/gb.png" alt="English" class="flag" id="currentFlag">
                    <span id="currentLang">English</span>
                </button>
                <div class="language-dropdown-content">
                    <button onclick="changeLanguage('en')">
                        <img src="https://flagcdn.com/w40/gb.png" alt="English" class="flag">
                        English
                    </button>
                    <button onclick="changeLanguage('lt')">
                        <img src="https://flagcdn.com/w40/lt.png" alt="Lithuanian" class="flag">
                        Lietuvių
                    </button>
                    <button onclick="changeLanguage('ru')">
        <div id="versionInfo" class="text-blue-200/50 text-sm"></div>
    </footer>

    <script>
        // Handle PIN input auto-focus
        document.querySelectorAll('.pin-input').forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value && index < 5) {
                    document.querySelectorAll('.pin-input')[index + 1].focus();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    document.querySelectorAll('.pin-input')[index - 1].focus();
                }
            });
        });

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault()
            
            const email = document.getElementById('email').value
            const password = document.getElementById('password').value
            const twoFactorCode = document.getElementById('2fa').value
            
            try {
                const { data, error } = await supabase.auth.signInWithPassword({
                    email: email,
                    password: password,
                    options: {
                        twoFactorToken: twoFactorCode
                    }
                })
                
                if (error) throw error
                
                window.location.href = '/dashboard.html'
            } catch (error) {
                alert('Error logging in: ' + error.message)
            }
        })

        document.getElementById('pinLoginForm').addEventListener('submit', async (e) => {
            e.preventDefault()
            const inputs = document.querySelectorAll('.pin-input');
            const pin = Array.from(inputs).map(input => input.value).join('');
            try {
                // Implement PIN verification logic here
                const { data, error } = await supabase.rpc('verify_pin', { pin });
                if (error) throw error;
                window.location.href = '/dashboard.html';
            } catch (error) {
                alert('Invalid PIN');
            }
        })

        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault()
            const email = document.getElementById('resetEmail').value
            
            try {
                const { error } = await supabase.auth.resetPasswordForEmail(email)
                if (error) throw error
                alert('Reset instructions sent to your email')
            } catch (error) {
                alert('Error: ' + error.message)
            }
        })

        function toggleForm(formId) {
            const forms = ['loginForm', 'pinLoginForm', 'resetForm'];
            forms.forEach(form => {
                document.getElementById(form).classList.add('hidden');
            });
            document.getElementById(formId).classList.remove('hidden');
        }

        function changeLanguage(lang) {
            document.querySelectorAll('.language-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.language-btn[onclick="changeLanguage('${lang}')"]`).classList.add('active');
            // Implement language change logic here
        }
    </script>
</body>
</html>
