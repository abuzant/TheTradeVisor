<x-public-layout>
    <x-slot name="title">Pricing - TheTradeVisor | Professional Trading Analytics</x-slot>
    <x-slot name="description">Flexible pricing plans for traders of all levels. Start free with 1 account, add more as you grow. Professional and Enterprise plans available.</x-slot>
    <x-slot name="keywords">trading analytics pricing, MT4 MT5 pricing, trading platform cost, professional trading tools</x-slot>

    {{-- Hero Section --}}
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl font-bold text-gray-900 mb-6">Simple, Transparent Pricing</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Start free, scale as you grow. No hidden fees, cancel anytime.
            </p>
        </div>
    </section>

    {{-- Pricing Cards --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                
                {{-- Free Plan --}}
                <div class="bg-white rounded-lg border-2 border-gray-200 p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-gray-900">$0</span>
                            <span class="text-gray-600 ml-2">/forever</span>
                        </div>
                        <p class="text-gray-600">Perfect for getting started</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700"><strong>1 Trading Account</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Real-time Analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Performance Tracking</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Global Analytics Access</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Data Export (CSV/PDF)</span>
                        </li>
                    </ul>
                    
                    @guest
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-gray-100 text-gray-900 rounded font-semibold hover:bg-gray-200">
                        Get Started Free
                    </a>
                    @else
                    <span class="block w-full py-3 px-6 text-center bg-gray-100 text-gray-600 rounded font-semibold">
                        Current Plan
                    </span>
                    @endguest
                </div>

                {{-- Pay Per Account --}}
                <div class="bg-white rounded-lg border-2 border-blue-500 p-8 relative">
                    <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 text-sm font-semibold rounded-bl rounded-tr">
                        POPULAR
                    </div>
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Pay Per Account</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-gray-900">$2.99</span>
                            <span class="text-gray-600 ml-2">/account/month</span>
                        </div>
                        <p class="text-gray-600">Add accounts as you need</p>
                        <p class="text-sm text-gray-500 mt-2">or $24.99/account/year (save 30%)</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700"><strong>Unlimited Accounts</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">All Free Features</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Advanced Analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Pay Only For What You Use</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Cancel Anytime</span>
                        </li>
                    </ul>
                    
                    @guest
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Get Started
                    </a>
                    @else
                    <a href="{{ route('accounts.index') }}" class="block w-full py-3 px-6 text-center bg-blue-600 text-white rounded font-semibold hover:bg-blue-700">
                        Add Accounts
                    </a>
                    @endguest
                </div>

                {{-- Pro Plan --}}
                <div class="bg-white rounded-lg border-2 border-gray-200 p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Pro</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-gray-900">$24.99</span>
                            <span class="text-gray-600 ml-2">/month</span>
                        </div>
                        <p class="text-gray-600">For professional traders</p>
                        <p class="text-sm text-gray-500 mt-2">or $239/year (save 20%)</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700"><strong>Up to 10 Accounts</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">All Features Included</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Priority Support</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Advanced Risk Analytics</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">API Access</span>
                        </li>
                    </ul>
                    
                    @guest
                    <a href="{{ route('register') }}" class="block w-full py-3 px-6 text-center bg-gray-900 text-white rounded font-semibold hover:bg-gray-800">
                        Get Started
                    </a>
                    @else
                    <a href="{{ route('accounts.index') }}" class="block w-full py-3 px-6 text-center bg-gray-900 text-white rounded font-semibold hover:bg-gray-800">
                        Upgrade to Pro
                    </a>
                    @endguest
                </div>

                {{-- Enterprise Plan --}}
                <div class="bg-white rounded-lg border-2 border-gray-200 p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Enterprise</h3>
                        <div class="flex items-baseline mb-4">
                            <span class="text-5xl font-bold text-gray-900">$999</span>
                            <span class="text-gray-600 ml-2">/month</span>
                        </div>
                        <p class="text-gray-600">For trading firms</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700"><strong>Unlimited Accounts</strong></span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">All Pro Features</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Dedicated Support</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Custom Integrations</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">SLA Guarantee</span>
                        </li>
                    </ul>
                    
                    <a href="/contact" class="block w-full py-3 px-6 text-center bg-gray-900 text-white rounded font-semibold hover:bg-gray-800">
                        Contact Sales
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Can I change plans anytime?</h3>
                    <p class="text-gray-600">Yes, you can upgrade, downgrade, or cancel your plan at any time. Changes take effect immediately.</p>
                </div>
                
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">What payment methods do you accept?</h3>
                    <p class="text-gray-600">We accept all major credit cards, PayPal, and wire transfers for Enterprise plans.</p>
                </div>
                
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Is there a free trial?</h3>
                    <p class="text-gray-600">Yes! The Free plan includes 1 account forever. No credit card required to start.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Do you offer refunds?</h3>
                    <p class="text-gray-600">Yes, we offer a 30-day money-back guarantee on all paid plans.</p>
                </div>
            </div>
        </div>
    </section>

</x-public-layout>
