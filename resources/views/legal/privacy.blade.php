<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Privacy Policy for TheTradeVisor - How we collect, use, and protect your data">
    <title>Privacy Policy - TheTradeVisor</title>
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
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
            <p class="text-sm text-gray-500 mb-8">Last Updated: {{ date('F d, Y') }}</p>

            <div class="prose prose-lg max-w-none">
                <!-- Introduction -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Introduction</h2>
                    <p class="text-gray-700 mb-4">
                        TheTradeVisor ("we," "us," or "our") is committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your data when you use our trading analytics platform ("the Service").
                    </p>
                    <p class="text-gray-700 mb-4">
                        We are fully compliant with the General Data Protection Regulation (GDPR), California Consumer Privacy Act (CCPA), and other applicable data protection laws. This policy applies to all users of TheTradeVisor, regardless of location.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>By using the Service, you consent to the data practices described in this policy.</strong>
                    </p>
                </section>

                <!-- 1. Information We Collect -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Information We Collect</h2>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">1.1 Information You Provide Directly</h3>
                    <p class="text-gray-700 mb-4">
                        When you register for an account, you provide:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li><strong>Account Information:</strong> Name, email address, password (encrypted)</li>
                        <li><strong>Profile Information:</strong> Optional profile details, preferences, settings</li>
                        <li><strong>Communication Data:</strong> Messages sent through contact forms or support tickets</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">1.2 Trading Data Collected from Your MT4/MT5 Accounts</h3>
                    <p class="text-gray-700 mb-4">
                        When you connect your MetaTrader accounts, we collect:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li><strong>Account Details:</strong> Account number, broker name, server, account currency, leverage</li>
                        <li><strong>Account Metrics:</strong> Balance, equity, margin, free margin, profit/loss</li>
                        <li><strong>Trading History:</strong> Positions, orders, deals, transaction history</li>
                        <li><strong>Trade Details:</strong> Symbol, volume, open/close prices, profit/loss, commission, swap</li>
                        <li><strong>Platform Information:</strong> MT4/MT5 version, platform build, account type (netting/hedging)</li>
                    </ul>
                    <p class="text-gray-700 mb-4 font-semibold">
                        Important: All trading data is collected ONLY through authorized API connections that you explicitly establish. We do NOT scrape, parse, or access broker websites or systems without authorization.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">1.3 Automatically Collected Information</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li><strong>Technical Data:</strong> IP address, browser type, device information, operating system</li>
                        <li><strong>Usage Data:</strong> Pages visited, features used, time spent, click patterns</li>
                        <li><strong>Connection Data:</strong> Login timestamps, session duration, API connection logs</li>
                        <li><strong>Geolocation Data:</strong> Country, city, timezone (derived from IP address)</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">1.4 Cookies and Tracking Technologies</h3>
                    <p class="text-gray-700 mb-4">
                        We use cookies and similar technologies to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Maintain your login session</li>
                        <li>Remember your preferences and settings</li>
                        <li>Analyze usage patterns and improve the Service</li>
                        <li>Provide security features and fraud prevention</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        You can control cookies through your browser settings. However, disabling cookies may limit your ability to use certain features of the Service.
                    </p>
                </section>

                <!-- 2. How We Use Your Information -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">2. How We Use Your Information</h2>
                    <p class="text-gray-700 mb-4">
                        We use your information for the following purposes:
                    </p>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.1 To Provide the Service</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Display your trading analytics and performance metrics</li>
                        <li>Generate reports, charts, and visualizations</li>
                        <li>Sync and update your trading data in real-time</li>
                        <li>Provide personalized insights and recommendations</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.2 To Create Aggregated Analytics</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Generate anonymized market insights and statistics</li>
                        <li>Display public broker analytics (fully anonymized)</li>
                        <li>Analyze trading patterns and market sentiment</li>
                        <li>Provide industry benchmarks and comparisons</li>
                    </ul>
                    <p class="text-gray-700 mb-4 font-semibold">
                        Note: Aggregated data is completely anonymized and cannot be traced back to individual users. We combine data from multiple users to ensure privacy.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.3 To Improve and Optimize the Service</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Analyze usage patterns and user behavior</li>
                        <li>Identify and fix technical issues</li>
                        <li>Develop new features and functionality</li>
                        <li>Optimize performance and user experience</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.4 For Security and Fraud Prevention</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Detect and prevent unauthorized access</li>
                        <li>Monitor for suspicious activity</li>
                        <li>Enforce our Terms of Service</li>
                        <li>Protect against fraud, abuse, and security threats</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">2.5 For Communication</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Send service-related notifications and updates</li>
                        <li>Respond to your inquiries and support requests</li>
                        <li>Send important security alerts</li>
                        <li>Provide marketing communications (with your consent)</li>
                    </ul>
                </section>

                <!-- 3. Data Sharing and Disclosure -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">3. Data Sharing and Disclosure</h2>
                    <p class="text-gray-700 mb-4 font-semibold">
                        We do NOT sell, rent, or trade your personal information to third parties. Period.
                    </p>
                    <p class="text-gray-700 mb-4">
                        We may share your information only in the following limited circumstances:
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">3.1 Public Display (Anonymized Only)</h3>
                    <p class="text-gray-700 mb-4">
                        Aggregated, anonymized trading statistics may be displayed publicly on broker analytics pages. This data:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Contains NO personally identifiable information</li>
                        <li>Is combined from multiple users (minimum thresholds applied)</li>
                        <li>Cannot be traced back to individual accounts</li>
                        <li>Shows only statistical summaries (e.g., "146 trades," "54% win rate")</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">3.2 Service Providers</h3>
                    <p class="text-gray-700 mb-4">
                        We may share data with trusted third-party service providers who assist us in operating the Service:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Cloud hosting providers (AWS, DigitalOcean, etc.)</li>
                        <li>Database and storage services</li>
                        <li>Email service providers</li>
                        <li>Analytics and monitoring tools</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        These providers are contractually obligated to protect your data and use it only for the purposes we specify.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">3.3 Legal Requirements</h3>
                    <p class="text-gray-700 mb-4">
                        We may disclose your information if required by law or in response to:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Valid legal process (subpoena, court order, warrant)</li>
                        <li>Government or regulatory requests</li>
                        <li>Protection of our rights, property, or safety</li>
                        <li>Emergency situations involving danger to persons</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">3.4 Business Transfers</h3>
                    <p class="text-gray-700 mb-4">
                        In the event of a merger, acquisition, or sale of assets, your information may be transferred to the acquiring entity. You will be notified of any such change.
                    </p>
                </section>

                <!-- 4. Data Security -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">4. Data Security</h2>
                    <p class="text-gray-700 mb-4">
                        We implement industry-leading security measures to protect your data:
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">4.1 Encryption</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li><strong>In Transit:</strong> All data transmitted between your device and our servers is encrypted using TLS 1.3 (SSL) encryption</li>
                        <li><strong>At Rest:</strong> Sensitive data is encrypted in our databases using AES-256 encryption</li>
                        <li><strong>Passwords:</strong> All passwords are hashed using bcrypt with salt</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">4.2 Access Controls</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Multi-factor authentication for administrative access</li>
                        <li>Role-based access control (RBAC)</li>
                        <li>Regular access audits and reviews</li>
                        <li>Principle of least privilege</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">4.3 Infrastructure Security</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Firewalls and intrusion detection systems</li>
                        <li>Regular security patches and updates</li>
                        <li>DDoS protection and rate limiting</li>
                        <li>Automated backups with encryption</li>
                        <li>24/7 monitoring and alerting</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">4.4 Security Practices</h3>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Regular security audits and penetration testing</li>
                        <li>Secure development lifecycle (SDLC)</li>
                        <li>Employee security training</li>
                        <li>Incident response plan</li>
                    </ul>

                    <p class="text-gray-700 mb-4">
                        <strong>Data Breach Notification:</strong> In the unlikely event of a data breach, we will notify affected users and relevant authorities within 72 hours as required by GDPR.
                    </p>
                </section>

                <!-- 5. Your Rights (GDPR) -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">5. Your Privacy Rights</h2>
                    <p class="text-gray-700 mb-4">
                        Under GDPR, CCPA, and other privacy laws, you have the following rights:
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.1 Right to Access</h3>
                    <p class="text-gray-700 mb-4">
                        You have the right to request a copy of all personal data we hold about you. We will provide this in a structured, commonly used, machine-readable format.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.2 Right to Rectification</h3>
                    <p class="text-gray-700 mb-4">
                        You can request correction of inaccurate or incomplete personal data.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.3 Right to Erasure ("Right to be Forgotten")</h3>
                    <p class="text-gray-700 mb-4">
                        You can request deletion of your personal data. We will comply unless we have a legal obligation to retain it. Note: Aggregated, anonymized data cannot be deleted as it cannot be traced back to you.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.4 Right to Restrict Processing</h3>
                    <p class="text-gray-700 mb-4">
                        You can request that we limit how we use your data in certain circumstances.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.5 Right to Data Portability</h3>
                    <p class="text-gray-700 mb-4">
                        You can request your data in a portable format to transfer to another service.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.6 Right to Object</h3>
                    <p class="text-gray-700 mb-4">
                        You can object to processing of your data for direct marketing or other purposes.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.7 Right to Withdraw Consent</h3>
                    <p class="text-gray-700 mb-4">
                        You can withdraw your consent to data processing at any time. This will not affect the lawfulness of processing before withdrawal.
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mb-3">5.8 Right to Lodge a Complaint</h3>
                    <p class="text-gray-700 mb-4">
                        You have the right to lodge a complaint with a supervisory authority (e.g., your local data protection authority).
                    </p>

                    <p class="text-gray-700 mb-4 font-semibold">
                        To exercise any of these rights, please contact us at <a href="mailto:privacy@thetradevisor.com" class="text-indigo-600 hover:text-indigo-800">privacy@thetradevisor.com</a>. We will respond within 30 days.
                    </p>
                </section>

                <!-- 6. Data Retention -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">6. Data Retention</h2>
                    <p class="text-gray-700 mb-4">
                        We retain your data for as long as necessary to provide the Service and comply with legal obligations:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li><strong>Active Accounts:</strong> Data is retained while your account is active</li>
                        <li><strong>Inactive Accounts:</strong> Data may be deleted after 2 years of inactivity (with prior notice)</li>
                        <li><strong>Deleted Accounts:</strong> Personal data is deleted within 30 days of account deletion</li>
                        <li><strong>Aggregated Data:</strong> Anonymized, aggregated data may be retained indefinitely for statistical purposes</li>
                        <li><strong>Legal Requirements:</strong> Some data may be retained longer if required by law (e.g., financial records)</li>
                    </ul>
                </section>

                <!-- 7. International Data Transfers -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">7. International Data Transfers</h2>
                    <p class="text-gray-700 mb-4">
                        Your data may be transferred to and processed in countries outside your country of residence. We ensure adequate protection through:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Standard Contractual Clauses (SCCs) approved by the European Commission</li>
                        <li>Adequacy decisions for countries with equivalent data protection</li>
                        <li>Binding Corporate Rules (BCRs) where applicable</li>
                    </ul>
                </section>

                <!-- 8. Children's Privacy -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">8. Children's Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        The Service is not intended for individuals under the age of 18. We do not knowingly collect personal information from children. If we discover that we have collected data from a child, we will delete it immediately.
                    </p>
                </section>

                <!-- 9. Third-Party Links -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">9. Third-Party Links</h2>
                    <p class="text-gray-700 mb-4">
                        The Service may contain links to third-party websites. We are not responsible for the privacy practices of these sites. We encourage you to read their privacy policies.
                    </p>
                </section>

                <!-- 10. Changes to This Policy -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">10. Changes to This Privacy Policy</h2>
                    <p class="text-gray-700 mb-4">
                        We may update this Privacy Policy from time to time. We will notify you of significant changes by:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-4 space-y-2">
                        <li>Posting the updated policy on this page</li>
                        <li>Updating the "Last Updated" date</li>
                        <li>Sending an email notification (for material changes)</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        Your continued use of the Service after changes constitutes acceptance of the updated policy.
                    </p>
                </section>

                <!-- Contact -->
                <section class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">11. Contact Us</h2>
                    <p class="text-gray-700 mb-4">
                        If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                    </p>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <p class="text-gray-700 mb-2">
                            <strong>Data Protection Officer:</strong><br>
                            Email: <a href="mailto:privacy@thetradevisor.com" class="text-indigo-600 hover:text-indigo-800">privacy@thetradevisor.com</a>
                        </p>
                        <p class="text-gray-700 mb-2">
                            <strong>General Inquiries:</strong><br>
                            Email: <a href="mailto:support@thetradevisor.com" class="text-indigo-600 hover:text-indigo-800">support@thetradevisor.com</a>
                        </p>
                        <p class="text-gray-700">
                            <strong>Website:</strong><br>
                            <a href="https://thetradevisor.com/contact" class="text-indigo-600 hover:text-indigo-800">https://thetradevisor.com/contact</a>
                        </p>
                    </div>
                </section>

                <div class="bg-green-50 border-l-4 border-green-600 p-6 mt-8">
                    <p class="text-sm text-green-900">
                        <strong>Our Commitment:</strong> TheTradeVisor is committed to transparency, security, and compliance with all applicable data protection laws. We will never sell your data, and we employ industry-leading security practices to keep your information safe. Your privacy is our priority.
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
