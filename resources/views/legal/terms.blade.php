<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Terms of Service for TheTradeVisor - Trading analytics platform">
    <title>Terms of Service - TheTradeVisor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        TheTradeVisor
                    </a>
                </div>
                <nav class="flex gap-6">
                    <a href="/" class="text-gray-600 hover:text-indigo-600">Home</a>
                    <a href="/features" class="text-gray-600 hover:text-indigo-600">Features</a>
                    <a href="/pricing" class="text-gray-600 hover:text-indigo-600">Pricing</a>
                    @auth
                        <a href="/dashboard" class="text-indigo-600 font-medium">Dashboard</a>
                    @else
                        <a href="/login" class="text-indigo-600 font-medium">Login</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-lg p-8 md:p-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Terms of Service</h1>
            <p class="text-sm text-gray-500 mb-8">Last Updated: {{ date('F d, Y') }}</p>

            <div class="prose prose-lg max-w-none">
                <!-- 1. Acceptance of Terms -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
                    <p class="text-gray-700 mb-4">
                        By accessing or using TheTradeVisor ("the Service," "we," "us," or "our"), you ("User," "you," or "your") agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, you must not access or use the Service.
                    </p>
                    <p class="text-gray-700 mb-4">
                        These Terms constitute a legally binding agreement between you and TheTradeVisor. We reserve the right to modify these Terms at any time. Your continued use of the Service after changes constitutes acceptance of the modified Terms.
                    </p>
                </section>

                <!-- 2. Description of Service -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. Description of Service</h2>
                    <p class="text-gray-700 mb-4">
                        TheTradeVisor is a trading analytics and performance tracking platform that enables users to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Connect their MetaTrader 4 (MT4) and MetaTrader 5 (MT5) trading accounts</li>
                        <li>Monitor and analyze their trading performance in real-time</li>
                        <li>View aggregated market insights and trading statistics</li>
                        <li>Access broker analytics based on aggregated user data</li>
                        <li>Generate reports and visualizations of trading activity</li>
                    </ul>
                    <p class="text-gray-700 mb-4 font-semibold">
                        Important: TheTradeVisor does NOT:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Scrape, parse, or unlawfully access any broker websites or systems</li>
                        <li>Collect data without explicit user consent and authorization</li>
                        <li>Execute trades or manage trading accounts on behalf of users</li>
                        <li>Provide investment advice, financial advice, or trading signals</li>
                        <li>Guarantee trading profits or specific outcomes</li>
                    </ul>
                </section>

                <!-- 3. User Data and Privacy -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. User Data and Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>3.1 Data Collection:</strong> By connecting your trading account(s) to the Service, you explicitly authorize us to collect, process, and store your trading data, including but not limited to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Account information (account number, broker name, server, currency)</li>
                        <li>Trading history (positions, orders, deals, transactions)</li>
                        <li>Account performance metrics (balance, equity, profit/loss)</li>
                        <li>Technical metadata (IP address, connection timestamps, platform version)</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>3.2 Data Source:</strong> All trading data is provided directly by you through authorized API connections to your MetaTrader terminals. We do not scrape, parse, or access any third-party systems without authorization.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>3.3 Data Usage:</strong> Your data is used to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Provide personalized analytics and performance tracking</li>
                        <li>Generate aggregated, anonymized market insights</li>
                        <li>Display public broker statistics (anonymized and aggregated)</li>
                        <li>Improve and optimize the Service</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>3.4 Data Anonymization:</strong> When displaying aggregated statistics (e.g., broker analytics), all personally identifiable information is removed. Public pages show only aggregated metrics from multiple users.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>3.5 Data Sharing:</strong> We do NOT sell, rent, or share your personal trading data with third parties, except as required by law or with your explicit consent. Aggregated, anonymized data may be displayed publicly.
                    </p>
                </section>

                <!-- 4. User Responsibilities -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. User Responsibilities</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>4.1 Account Security:</strong> You are responsible for maintaining the confidentiality of your account credentials and API keys. You agree to notify us immediately of any unauthorized access.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>4.2 Accurate Information:</strong> You agree to provide accurate, current, and complete information during registration and to update such information as necessary.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>4.3 Lawful Use:</strong> You agree to use the Service only for lawful purposes and in accordance with these Terms. You will not:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Attempt to gain unauthorized access to any part of the Service</li>
                        <li>Use the Service to violate any applicable laws or regulations</li>
                        <li>Interfere with or disrupt the Service or servers</li>
                        <li>Use automated systems (bots, scrapers) to access the Service without permission</li>
                        <li>Reverse engineer, decompile, or disassemble any part of the Service</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>4.4 Trading Decisions:</strong> You acknowledge that all trading decisions are your own responsibility. The Service provides analytics and information only; it does not constitute financial or investment advice.
                    </p>
                </section>

                <!-- 5. Disclaimers and Limitations -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Disclaimers and Limitations of Liability</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>5.1 "AS-IS" Service:</strong> THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>5.2 No Guarantee of Accuracy:</strong> While we strive for accuracy, we do not guarantee that the data, analytics, or information provided through the Service is accurate, complete, reliable, current, or error-free. Trading data may be delayed, incomplete, or contain errors.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>5.3 No Financial Advice:</strong> The Service does not provide financial, investment, trading, or legal advice. All information is for informational purposes only. You should consult with qualified professionals before making any trading or investment decisions.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>5.4 Trading Risks:</strong> Trading foreign exchange, contracts for difference (CFDs), and other leveraged products carries a high level of risk and may not be suitable for all investors. You may lose more than your initial investment.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>5.5 Limitation of Liability:</strong> TO THE MAXIMUM EXTENT PERMITTED BY LAW, THETRADEVISOR SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES RESULTING FROM:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Your use or inability to use the Service</li>
                        <li>Any unauthorized access to or use of your data</li>
                        <li>Any errors, mistakes, or inaccuracies in the Service</li>
                        <li>Trading losses or financial damages of any kind</li>
                        <li>Service interruptions, downtime, or technical failures</li>
                    </ul>
                </section>

                <!-- 6. Intellectual Property -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Intellectual Property Rights</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>6.1 Ownership:</strong> The Service, including all content, features, functionality, software, code, design, graphics, and trademarks, is owned by TheTradeVisor and is protected by international copyright, trademark, and other intellectual property laws.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>6.2 License:</strong> Subject to these Terms, we grant you a limited, non-exclusive, non-transferable, revocable license to access and use the Service for your personal, non-commercial use.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>6.3 User Data:</strong> You retain all rights to your trading data. By using the Service, you grant us a worldwide, non-exclusive, royalty-free license to use, process, and display your data as necessary to provide the Service and display aggregated, anonymized statistics.
                    </p>
                </section>

                <!-- 7. Data Security and GDPR Compliance -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. Data Security and GDPR Compliance</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>7.1 Security Measures:</strong> We implement industry-standard security measures to protect your data:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>SSL/TLS encryption for all data transmission</li>
                        <li>Encrypted storage of sensitive information</li>
                        <li>Secure authentication and authorization mechanisms</li>
                        <li>Regular security audits and updates</li>
                        <li>Access controls and monitoring systems</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>7.2 GDPR Compliance:</strong> For users in the European Economic Area (EEA), we comply with the General Data Protection Regulation (GDPR). You have the right to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Access your personal data</li>
                        <li>Rectify inaccurate data</li>
                        <li>Request deletion of your data ("right to be forgotten")</li>
                        <li>Object to data processing</li>
                        <li>Data portability</li>
                        <li>Withdraw consent at any time</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        <strong>7.3 Data Breach Notification:</strong> In the event of a data breach that affects your personal information, we will notify you and relevant authorities within 72 hours as required by GDPR.
                    </p>
                </section>

                <!-- 8. Termination -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Termination</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>8.1 By You:</strong> You may terminate your account at any time by contacting us or using the account deletion feature. Upon termination, your personal data will be deleted in accordance with our Privacy Policy.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>8.2 By Us:</strong> We reserve the right to suspend or terminate your access to the Service at any time, with or without notice, for any reason, including violation of these Terms.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>8.3 Effect of Termination:</strong> Upon termination, your right to use the Service will immediately cease. Aggregated, anonymized data derived from your account may be retained for statistical purposes.
                    </p>
                </section>

                <!-- 9. Indemnification -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Indemnification</h2>
                    <p class="text-gray-700 mb-4">
                        You agree to indemnify, defend, and hold harmless TheTradeVisor, its officers, directors, employees, agents, and affiliates from and against any claims, liabilities, damages, losses, costs, or expenses (including reasonable attorneys' fees) arising out of or related to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Your use or misuse of the Service</li>
                        <li>Your violation of these Terms</li>
                        <li>Your violation of any rights of another party</li>
                        <li>Your trading activities and decisions</li>
                    </ul>
                </section>

                <!-- 10. Governing Law -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Governing Law and Dispute Resolution</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>10.1 Governing Law:</strong> These Terms shall be governed by and construed in accordance with the laws of the jurisdiction in which TheTradeVisor operates, without regard to its conflict of law provisions.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>10.2 Dispute Resolution:</strong> Any disputes arising out of or relating to these Terms or the Service shall be resolved through binding arbitration, except that either party may seek injunctive relief in court.
                    </p>
                </section>

                <!-- 11. Miscellaneous -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Miscellaneous</h2>
                    <p class="text-gray-700 mb-4">
                        <strong>11.1 Entire Agreement:</strong> These Terms constitute the entire agreement between you and TheTradeVisor regarding the Service and supersede all prior agreements.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>11.2 Severability:</strong> If any provision of these Terms is found to be unenforceable, the remaining provisions will remain in full force and effect.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>11.3 Waiver:</strong> No waiver of any term of these Terms shall be deemed a further or continuing waiver of such term or any other term.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>11.4 Assignment:</strong> You may not assign or transfer these Terms or your rights hereunder without our prior written consent. We may assign these Terms without restriction.
                    </p>
                </section>

                <!-- Contact -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">12. Contact Information</h2>
                    <p class="text-gray-700 mb-4">
                        If you have any questions about these Terms of Service, please contact us at:
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Email:</strong> <a href="mailto:legal@thetradevisor.com" class="text-indigo-600 hover:text-indigo-800">legal@thetradevisor.com</a><br>
                        <strong>Website:</strong> <a href="https://thetradevisor.com/contact" class="text-indigo-600 hover:text-indigo-800">https://thetradevisor.com/contact</a>
                    </p>
                </section>

                <div class="bg-indigo-50 border-l-4 border-indigo-600 p-6 mt-8">
                    <p class="text-sm text-indigo-900">
                        <strong>Important Notice:</strong> By using TheTradeVisor, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. Trading involves substantial risk of loss and is not suitable for all investors. Past performance is not indicative of future results.
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} TheTradeVisor. All rights reserved.</p>
                <div class="mt-4 flex justify-center gap-6">
                    <a href="/about" class="hover:text-white">About</a>
                    <a href="/contact" class="hover:text-white">Contact</a>
                    <a href="/terms" class="hover:text-white font-semibold">Terms of Service</a>
                    <a href="/privacy" class="hover:text-white font-semibold">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
