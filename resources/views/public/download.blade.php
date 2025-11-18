<x-public-layout>
    <x-slot name="title">Download TradeVisor EA - Automatic Installation | TheTradeVisor</x-slot>
    <x-slot name="description">Download the TradeVisor Expert Advisor with automatic installation for MT4 and MT5. One-click setup for all your MetaTrader terminals.</x-slot>

    <section class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-gray-900 mb-4">
                    Download TradeVisor EA
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Automatic installation for MetaTrader 4 & 5. One installer detects all your terminals and sets everything up for you.
                </p>
            </div>

            {{-- Download Card --}}
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden mb-12">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold mb-2">TradeVisor Setup</h2>
                            <p class="text-indigo-100">Version 1.4.0 - Automatic Installer</p>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-bold">FREE</div>
                            <div class="text-indigo-100 text-sm">Windows 7/8/10/11</div>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    {{-- Download Button --}}
                    <div class="text-center mb-8">
                        <a href="{{ route('download.setup') }}" 
                           onclick="gtag('event', 'download', {
                               'event_category': 'EA',
                               'event_label': 'TradeVisor Setup',
                               'value': 1
                           });"
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg font-bold rounded-lg hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download TradeVisor-Setup.exe
                        </a>
                        <p class="mt-3 text-sm text-gray-500">File size: ~2 MB | Instant download</p>
                    </div>

                    {{-- Features --}}
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-green-600 text-3xl mb-2">✓</div>
                            <h3 class="font-semibold text-gray-900 mb-1">Auto-Detection</h3>
                            <p class="text-sm text-gray-600">Finds all MT4/MT5 installations automatically</p>
                        </div>
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-blue-600 text-3xl mb-2">⚡</div>
                            <h3 class="font-semibold text-gray-900 mb-1">One-Click Install</h3>
                            <p class="text-sm text-gray-600">Installs to all terminals in seconds</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-purple-600 text-3xl mb-2">🛡️</div>
                            <h3 class="font-semibold text-gray-900 mb-1">Safe & Secure</h3>
                            <p class="text-sm text-gray-600">No malware, no adware, just the EA</p>
                        </div>
                    </div>

                    {{-- What's Included --}}
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="font-bold text-gray-900 mb-3">📦 What's Included</h3>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>TradeVisor.ex4</strong> - Expert Advisor for MetaTrader 4</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>TradeVisor.ex5</strong> - Expert Advisor for MetaTrader 5</span>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>Automatic Installer</strong> - Detects and installs to all MT4/MT5 terminals</span>
                            </li>
                        </ul>
                    </div>

                    {{-- System Requirements --}}
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-bold text-gray-900 mb-3">💻 System Requirements</h3>
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div>
                                <strong>Operating System:</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>• Windows 7 or later</li>
                                    <li>• Windows Server 2012 or later</li>
                                </ul>
                            </div>
                            <div>
                                <strong>Required Software:</strong>
                                <ul class="ml-4 mt-1 space-y-1">
                                    <li>• MetaTrader 4 and/or 5</li>
                                    <li>• Active internet connection</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Installation Instructions --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6 text-white">
                    <h2 class="text-2xl font-bold">📖 Installation Instructions</h2>
                </div>

                <div class="p-8">
                    <div class="space-y-8">
                        {{-- Step 1 --}}
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg">
                                    1
                                </div>
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Download and Run Setup</h3>
                                <p class="text-gray-600 mb-3">
                                    Click the download button above to get <code class="bg-gray-100 px-2 py-1 rounded text-sm">TradeVisor-Setup.exe</code>. 
                                    Once downloaded, double-click to run the installer.
                                </p>
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                                    <p class="text-sm text-yellow-800">
                                        <strong>⚠️ Windows SmartScreen:</strong> You may see a warning. Click "More info" → "Run anyway". 
                                        This is normal for new software not yet widely distributed.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg">
                                    2
                                </div>
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Automatic Installation</h3>
                                <p class="text-gray-600 mb-3">
                                    The installer will automatically:
                                </p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Scan your computer for all MT4 and MT5 installations</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Copy the appropriate EA file to each terminal's Expert Advisors folder</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Show you a summary of all installations completed</span>
                                    </li>
                                </ul>
                                <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                    <p class="text-sm text-blue-800">
                                        <strong>💡 Pro Tip:</strong> The installer searches in AppData, Program Files, and common broker locations. 
                                        It will find installations from XM, Exness, FXCM, Pepperstone, Equiti, IC Markets, and more.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg">
                                    3
                                </div>
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Configure in MetaTrader</h3>
                                <p class="text-gray-600 mb-3">
                                    After installation, open your MetaTrader terminal and configure the EA:
                                </p>
                                <ol class="space-y-2 text-gray-700 list-decimal list-inside">
                                    <li>Restart your MT4/MT5 terminal if it was running</li>
                                    <li>Open the <strong>Navigator</strong> panel (Ctrl+N)</li>
                                    <li>Expand <strong>Expert Advisors</strong> section</li>
                                    <li>Find <strong>TradeVisor</strong> in the list</li>
                                    <li>Drag and drop it onto any chart</li>
                                    <li>In the settings window:
                                        <ul class="ml-6 mt-2 space-y-1 list-disc">
                                            <li>Check <strong>"Allow DLL imports"</strong></li>
                                            <li>Check <strong>"Allow WebRequest for listed URLs"</strong></li>
                                            <li>Enter your <strong>API Key</strong> (get it from <a href="{{ route('settings.api-key') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">Settings → API Key</a>)</li>
                                        </ul>
                                    </li>
                                    <li>Click <strong>OK</strong></li>
                                </ol>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 font-bold text-lg">
                                    4
                                </div>
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Enable WebRequest</h3>
                                <p class="text-gray-600 mb-3">
                                    The EA needs permission to send data to our servers:
                                </p>
                                <ol class="space-y-2 text-gray-700 list-decimal list-inside">
                                    <li>Go to <strong>Tools → Options</strong> (or press Ctrl+O)</li>
                                    <li>Click the <strong>Expert Advisors</strong> tab</li>
                                    <li>Check <strong>"Allow WebRequest for listed URL"</strong></li>
                                    <li>Click <strong>"Add"</strong> and enter: <code class="bg-gray-100 px-2 py-1 rounded text-sm">https://api.thetradevisor.com</code></li>
                                    <li>Click <strong>OK</strong> to save</li>
                                </ol>
                                <div class="mt-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                                    <p class="text-sm text-red-800">
                                        <strong>⚠️ Critical:</strong> Without WebRequest permission, the EA cannot send data and will show connection errors.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-100 text-green-600 font-bold text-lg">
                                    ✓
                                </div>
                            </div>
                            <div class="ml-6 flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Verify Connection</h3>
                                <p class="text-gray-600 mb-3">
                                    Check that everything is working:
                                </p>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Look for a <strong>smiley face icon</strong> in the top-right corner of your chart</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Check the <strong>Experts</strong> tab for "Connected to TheTradeVisor API" message</span>
                                    </li>
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Visit your <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">dashboard</a> - your account should appear within 60 seconds</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manual Installation (Fallback) --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-6 text-white">
                    <h2 class="text-2xl font-bold">🔧 Manual Installation (If Automatic Fails)</h2>
                </div>

                <div class="p-8">
                    <p class="text-gray-600 mb-6">
                        If the automatic installer doesn't detect your MetaTrader installation, you can install manually:
                    </p>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">For MetaTrader 4:</h3>
                            <ol class="space-y-2 text-gray-700 list-decimal list-inside ml-4">
                                <li>Open MT4 terminal</li>
                                <li>Go to <strong>File → Open Data Folder</strong></li>
                                <li>Navigate to <code class="bg-gray-100 px-2 py-1 rounded text-sm">MQL4\Experts\Advisors</code></li>
                                <li>Copy <code class="bg-gray-100 px-2 py-1 rounded text-sm">TradeVisor.ex4</code> from the installation folder to this directory</li>
                                <li>Restart MT4</li>
                            </ol>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">For MetaTrader 5:</h3>
                            <ol class="space-y-2 text-gray-700 list-decimal list-inside ml-4">
                                <li>Open MT5 terminal</li>
                                <li>Go to <strong>File → Open Data Folder</strong></li>
                                <li>Navigate to <code class="bg-gray-100 px-2 py-1 rounded text-sm">MQL5\Experts\Advisors</code></li>
                                <li>Copy <code class="bg-gray-100 px-2 py-1 rounded text-sm">TradeVisor.ex5</code> from the installation folder to this directory</li>
                                <li>Restart MT5</li>
                            </ol>
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <p class="text-sm text-blue-800">
                                <strong>💡 Finding the Installation Folder:</strong> By default, the installer places files in 
                                <code class="bg-blue-100 px-2 py-1 rounded text-xs">C:\Program Files\TradeVisor</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Troubleshooting --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-12">
                <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6 text-white">
                    <h2 class="text-2xl font-bold">🔍 Troubleshooting</h2>
                </div>

                <div class="p-8">
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">EA Not Showing in Navigator</h3>
                            <ul class="space-y-1 text-gray-700 ml-4 list-disc">
                                <li>Restart MetaTrader terminal</li>
                                <li>Press F4 to open MetaEditor and compile the EA</li>
                                <li>Check that the file is in the correct Experts\Advisors folder</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Connection Errors</h3>
                            <ul class="space-y-1 text-gray-700 ml-4 list-disc">
                                <li>Verify WebRequest is enabled for <code class="bg-gray-100 px-2 py-1 rounded text-sm">https://api.thetradevisor.com</code></li>
                                <li>Check your API key is correct</li>
                                <li>Ensure AutoTrading is enabled (green button in toolbar)</li>
                                <li>Check your firewall isn't blocking MetaTrader</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Data Not Appearing in Dashboard</h3>
                            <ul class="space-y-1 text-gray-700 ml-4 list-disc">
                                <li>Wait 60 seconds - data syncs every minute</li>
                                <li>Check the Experts tab for error messages</li>
                                <li>Verify your account is active in <a href="{{ route('accounts.index') }}" class="text-indigo-600 hover:text-indigo-700">Account Management</a></li>
                                <li>Make sure you have at least one open or closed trade</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Still Need Help?</h3>
                        <div class="flex gap-4">
                            <a href="{{ route('faq') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition-colors">
                                View FAQ
                            </a>
                            <a href="{{ route('docs') }}" class="px-6 py-3 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 font-semibold transition-colors">
                                Read Documentation
                            </a>
                            <a href="{{ route('contact') }}" class="px-6 py-3 bg-gray-100 text-gray-900 rounded-lg hover:bg-gray-200 font-semibold transition-colors">
                                Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Resources --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-white text-center">
                <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
                <p class="text-indigo-100 mb-6 max-w-2xl mx-auto">
                    Download the installer, run it, and start tracking your trading performance in minutes.
                </p>
                <div class="flex justify-center gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 font-bold transition-colors">
                            Create Free Account
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 font-bold transition-colors">
                            Go to Dashboard
                        </a>
                    @endguest
                    <a href="{{ route('docs') }}" class="px-8 py-3 bg-indigo-700 text-white rounded-lg hover:bg-indigo-800 font-bold transition-colors">
                        View Documentation
                    </a>
                </div>
            </div>

        </div>
    </section>

</x-public-layout>
