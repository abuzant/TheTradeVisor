<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We're Sorry to See You Go - TheTradeVisor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZGPZK0T9NE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ZGPZK0T9NE');
        
        // Track uninstall page view
        gtag('event', 'page_view', {
            page_title: 'Uninstall Page',
            page_location: '/uninstalled',
            custom_parameter: 'uninstall_page_view'
        });
        
        // Track time on page
        let startTime = new Date().getTime();
        window.addEventListener('beforeunload', function() {
            let timeSpent = Math.round((new Date().getTime() - startTime) / 1000);
            gtag('event', 'time_on_page', {
                custom_parameter: 'uninstall_page',
                time_spent_seconds: timeSpent
            });
        });
        
        // Track scroll depth
        let maxScroll = 0;
        window.addEventListener('scroll', function() {
            let scrollPercent = Math.round((window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100);
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                gtag('event', 'scroll_depth', {
                    custom_parameter: 'uninstall_page',
                    scroll_depth_percent: maxScroll
                });
            }
        });
    </script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white py-6 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i data-lucide="trending-up" class="w-8 h-8"></i>
                    <h1 class="text-2xl font-bold">TheTradeVisor</h1>
                </div>
                <div class="text-sm opacity-90">
                    Advanced Trading Analytics
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-12">
        <!-- Hero Section -->
        <section class="text-center mb-12">
            <div class="float-animation mb-8">
                <i data-lucide="heart-crack" class="w-24 h-24 text-purple-600 mx-auto"></i>
            </div>
            <h2 class="text-4xl font-bold text-gray-900 mb-4">We're Sorry to See You Go!</h2>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Your trading analytics journey matters to us. Help us understand what went wrong so we can improve and potentially win you back.
            </p>
        </section>

        <!-- Special Offers Section -->
        <section class="mb-12">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 text-white text-center">
                <h3 class="text-2xl font-bold mb-4">🎁 Before You Go - Special Offer Just for You!</h3>
                <div class="grid md:grid-cols-1 gap-6 mt-8">
                    <div class="card-hover bg-white/20 backdrop-blur-lg rounded-xl p-6 cursor-pointer" onclick="trackOfferClick('personal_demo')">
                        <i data-lucide="user-check" class="w-12 h-12 mx-auto mb-3"></i>
                        <h4 class="font-bold text-lg mb-2">Personal Demo</h4>
                        <p class="text-sm opacity-90">1-on-1 setup assistance</p>
                        <button class="mt-4 bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-100 transition">
                            Email Us
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feedback Form -->
        <section class="bg-white rounded-2xl shadow-xl p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                💬 Your Feedback Helps Us Improve
            </h3>
            
            <form id="feedbackForm" class="space-y-6">
                <!-- Reason for Leaving -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        What's the main reason you're uninstalling?
                    </label>
                    <select name="reason" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Please select a reason...</option>
                        <option value="too_expensive">Too expensive</option>
                        <option value="not_enough_features">Not enough features</option>
                        <option value="technical_issues">Technical issues</option>
                        <option value="difficult_to_use">Difficult to use</option>
                        <option value="found_alternative">Found a better alternative</option>
                        <option value="no_longer_trading">No longer trading</option>
                        <option value="data_quality_issues">Data quality issues</option>
                        <option value="customer_support">Customer support problems</option>
                        <option value="other">Other reason</option>
                    </select>
                </div>

                <!-- Experience Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        How would you rate your overall experience?
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex-1">
                            <input type="radio" name="experience" value="very_poor" required class="sr-only peer">
                            <div class="text-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                                <span class="text-2xl">😞</span>
                                <p class="text-sm mt-1">Very Poor</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="experience" value="poor" required class="sr-only peer">
                            <div class="text-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                                <span class="text-2xl">😕</span>
                                <p class="text-sm mt-1">Poor</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="experience" value="average" required class="sr-only peer">
                            <div class="text-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50">
                                <span class="text-2xl">😐</span>
                                <p class="text-sm mt-1">Average</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="experience" value="good" required class="sr-only peer">
                            <div class="text-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                                <span class="text-2xl">😊</span>
                                <p class="text-sm mt-1">Good</p>
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="experience" value="excellent" required class="sr-only peer">
                            <div class="text-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                                <span class="text-2xl">😃</span>
                                <p class="text-sm mt-1">Excellent</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Would Return -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Would you consider using TheTradeVisor again in the future?
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex-1">
                            <input type="radio" name="would_return" value="definitely" required class="sr-only peer">
                            <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                                👍 Definitely
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="would_return" value="maybe" required class="sr-only peer">
                            <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50">
                                🤔 Maybe
                            </div>
                        </label>
                        <label class="flex-1">
                            <input type="radio" name="would_return" value="probably_not" required class="sr-only peer">
                            <div class="text-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                                👎 Probably Not
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Email (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email (optional - if you'd like us to follow up)
                    </label>
                    <input type="email" name="email" placeholder="your@email.com" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Comments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Any additional comments or suggestions?
                    </label>
                    <textarea name="comments" rows="4" placeholder="Tell us more about your experience..." 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                </div>

                <!-- reCAPTCHA -->
                <div class="mb-6">
                    <div class="g-recaptcha" data-sitekey="6LdbKwssAAAAANvWRniOD6J3QJYEYbtq62qUIehx"></div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition transform hover:scale-105">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </section>

        <!-- Alternative Solutions -->
        <section class="bg-white rounded-2xl shadow-xl p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                🛠️ Having Issues? We Can Help!
            </h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex items-start space-x-4">
                    <i data-lucide="message-circle" class="w-8 h-8 text-blue-600 flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-2">24/7 Live Support</h4>
                        <p class="text-gray-600 mb-3">Chat with our team anytime - we're here to help solve your problems.</p>
                        <button onclick="trackOfferClick('live_support')" class="text-blue-600 font-semibold hover:text-blue-700">
                            Start Live Chat →
                        </button>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <i data-lucide="book-open" class="w-8 h-8 text-green-600 flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-2">Video Tutorials</h4>
                        <p class="text-gray-600 mb-3">Step-by-step guides to help you get the most out of TheTradeVisor.</p>
                        <button onclick="trackOfferClick('tutorials')" class="text-green-600 font-semibold hover:text-green-700">
                            Watch Tutorials →
                        </button>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <i data-lucide="phone" class="w-8 h-8 text-purple-600 flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-2">Schedule a Call</h4>
                        <p class="text-gray-600 mb-3">Personal 1-on-1 assistance to set up your account perfectly.</p>
                        <button onclick="trackOfferClick('schedule_call')" class="text-purple-600 font-semibold hover:text-purple-700">
                            Book a Call →
                        </button>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <i data-lucide="settings" class="w-8 h-8 text-orange-600 flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="font-bold text-lg mb-2">Technical Help</h4>
                        <p class="text-gray-600 mb-3">Having technical issues? Our experts can fix it remotely.</p>
                        <button onclick="trackOfferClick('technical_help')" class="text-orange-600 font-semibold hover:text-orange-700">
                            Get Help →
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final Appeal -->
        <section class="text-center bg-gradient-to-r from-purple-100 to-blue-100 rounded-2xl p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">
                🌟 Give Us One More Chance!
            </h3>
            <p class="text-lg text-gray-700 mb-6 max-w-2xl mx-auto">
                We're constantly improving based on user feedback. Many users who reconsider find that our latest updates address their exact concerns.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.location.href='/download'" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition">
                    Try TheTradeVisor Again
                </button>
                <button onclick="window.location.href='https://github.com/abuzant/TheTradeVisor'" class="bg-white text-purple-600 border-2 border-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50 transition">
                    Give Us Another Chance
                </button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.public-footer')

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
            <i data-lucide="check-circle" class="w-16 h-16 text-green-500 mx-auto mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h3>
            <p class="text-gray-600 mb-6">Your feedback helps us improve. We'll use it to make TheTradeVisor better for everyone.</p>
            <button onclick="closeModal()" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-purple-700 transition">
                Close
            </button>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Track offer clicks
        function trackOfferClick(offerType) {
            gtag('event', 'offer_click', {
                custom_parameter: 'uninstall_page',
                offer_type: offerType
            });

            // Send to backend
            fetch('/api/uninstalled/track-offer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ offer: offerType })
            });

            // Track form field interactions
            gtag('event', 'form_field_interaction', {
                custom_parameter: 'uninstall_page',
                field_type: 'offer_click',
                offer_type: offerType
            });
        }

        // Handle form submission
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get reCAPTCHA response
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                alert('Please complete the reCAPTCHA verification.');
                return;
            }
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            data.recaptcha_token = recaptchaResponse;

            // Track form submission start
            gtag('event', 'form_submission_start', {
                custom_parameter: 'uninstall_feedback'
            });

            fetch('/api/uninstalled/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    // Track successful submission
                    gtag('event', 'form_submission_complete', {
                        custom_parameter: 'uninstall_feedback',
                        reason: data.reason,
                        experience: data.experience,
                        would_return: data.would_return
                    });

                    // Show success modal
                    document.getElementById('successModal').style.display = 'flex';
                } else {
                    alert('There was an error submitting your feedback. Please try again or visit our download page to give us another chance.');
                    window.location.href = '/download';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your feedback. Please try again or visit our download page to give us another chance.');
                window.location.href = '/download';
            });
        });

        // Close modal
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        // Track form field interactions
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('focus', function() {
                gtag('event', 'form_field_focus', {
                    custom_parameter: 'uninstall_feedback',
                    field_name: this.name,
                    field_type: this.type || this.tagName.toLowerCase()
                });
            });

            field.addEventListener('change', function() {
                gtag('event', 'form_field_change', {
                    custom_parameter: 'uninstall_feedback',
                    field_name: this.name,
                    field_value: this.value
                });
            });
        });

        // Track page engagement
        let engagementTime = 0;
        setInterval(() => {
            engagementTime++;
            if (engagementTime % 30 === 0) { // Track every 30 seconds
                gtag('event', 'page_engagement', {
                    custom_parameter: 'uninstall_page',
                    engagement_time_seconds: engagementTime
                });
            }
        }, 1000);
    </script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</body>
</html>
