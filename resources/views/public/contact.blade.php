<x-public-layout>
    <x-slot name="title">Contact Us - TheTradeVisor | Get in Touch</x-slot>
    <x-slot name="description">Contact TheTradeVisor support team. We're here to help with any questions about our trading analytics platform.</x-slot>
    
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "mainEntity": {
            "@type": "Organization",
            "name": "TheTradeVisor",
            "url": "https://thetradevisor.com",
            "contactPoint": {
                "@type": "ContactPoint",
                "email": "hello@thetradevisor.com",
                "contactType": "customer support",
                "availableLanguage": ["English"]
            }
        }
    }
    </script>

    <section class="bg-white py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl font-bold text-gray-900 mb-6 text-center">Contact Us</h1>
            <p class="text-xl text-gray-600 text-center mb-12">Have a question? We're here to help.</p>

            @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid md:grid-cols-2 gap-12 mb-12">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a message</h2>
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" required class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" required rows="6" class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        @if(config('services.recaptcha.enabled'))
                        <div>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @error('recaptcha')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                            Send Message
                        </button>
                    </form>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Other ways to reach us</h2>
                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                            <p class="text-gray-600"><a href="mailto:hello@thetradevisor.com" class="text-blue-600 hover:text-blue-700"><span data-cfemail="[email protected]">hello@thetradevisor.com</span></a></p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Support Hours</h3>
                            <p class="text-gray-600">24/7 Email Support<br>Priority support for Pro & Enterprise customers</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-2">Documentation</h3>
                            <p class="text-gray-600">Check our <a href="/faq" class="text-blue-600 hover:text-blue-700">FAQ page</a> for quick answers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
