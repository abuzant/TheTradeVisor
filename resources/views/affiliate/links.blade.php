@extends('layouts.affiliate')

@section('title', 'Links & Tools')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Primary Referral Link -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Primary Referral Link</h3>
                <div class="flex items-center space-x-2 mb-4">
                    <input type="text" readonly value="{{ $affiliate->referral_url }}" id="primaryLink" class="flex-1 rounded-md border-gray-300 bg-gray-50 text-sm">
                    <button onclick="copyLink('primaryLink')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                        Copy
                    </button>
                </div>
                <p class="text-sm text-gray-600">Share this link to earn $1.99 for every paid signup</p>
            </div>
        </div>

        <!-- UTM Link Builder -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">UTM Link Builder</h3>
                <p class="text-sm text-gray-600 mb-4">Create custom tracking links for different campaigns</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Source</label>
                        <input type="text" id="utmSource" placeholder="e.g., facebook, twitter, email" class="w-full rounded-md border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Where the traffic comes from</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Medium</label>
                        <input type="text" id="utmMedium" placeholder="e.g., social, cpc, email" class="w-full rounded-md border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Marketing medium</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Name</label>
                        <input type="text" id="utmCampaign" placeholder="e.g., summer_promo" class="w-full rounded-md border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Specific campaign identifier</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Content (Optional)</label>
                        <input type="text" id="utmContent" placeholder="e.g., banner_ad" class="w-full rounded-md border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Differentiate similar content</p>
                    </div>
                </div>

                <button onclick="generateUTMLink()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                    Generate Link
                </button>

                <div id="generatedLinkContainer" class="hidden mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Generated Link</label>
                    <div class="flex items-center space-x-2">
                        <input type="text" readonly id="generatedLink" class="flex-1 rounded-md border-gray-300 bg-gray-50 text-sm">
                        <button onclick="copyLink('generatedLink')" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                            Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Generator -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code</h3>
                <p class="text-sm text-gray-600 mb-4">Download QR code for offline marketing</p>
                
                <div class="flex items-start space-x-6">
                    <div id="qrcode" class="border-2 border-gray-200 p-4 rounded-lg"></div>
                    <div>
                        <p class="text-sm text-gray-700 mb-2">Use this QR code on:</p>
                        <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                            <li>Business cards</li>
                            <li>Flyers and posters</li>
                            <li>Social media posts</li>
                            <li>Email signatures</li>
                        </ul>
                        <button onclick="downloadQR()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            Download QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Materials -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Marketing Materials</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Email Template</h4>
                        <p class="text-sm text-gray-600 mb-3">Pre-written email you can send to prospects</p>
                        <button onclick="showEmailTemplate()" class="text-sm text-indigo-600 hover:text-indigo-900">View Template →</button>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Social Media Posts</h4>
                        <p class="text-sm text-gray-600 mb-3">Ready-to-share social media content</p>
                        <button onclick="showSocialTemplates()" class="text-sm text-indigo-600 hover:text-indigo-900">View Templates →</button>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Banner Ads</h4>
                        <p class="text-sm text-gray-600 mb-3">Download banner images for your website</p>
                        <button onclick="showBanners()" class="text-sm text-indigo-600 hover:text-indigo-900">View Banners →</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <h3 class="text-lg font-semibold mb-4">Quick Tips for Success</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-3xl font-bold mb-2">📊</div>
                    <h4 class="font-semibold mb-1">Track Your Campaigns</h4>
                    <p class="text-sm opacity-90">Use UTM parameters to identify your best traffic sources</p>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">🎯</div>
                    <h4 class="font-semibold mb-1">Target Traders</h4>
                    <p class="text-sm opacity-90">Focus on forex and MT5 trading communities</p>
                </div>
                <div>
                    <div class="text-3xl font-bold mb-2">💰</div>
                    <h4 class="font-semibold mb-1">Earn More</h4>
                    <p class="text-sm opacity-90">$1.99 per paid signup adds up quickly!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
// Generate QR Code
const qrcode = new QRCode(document.getElementById("qrcode"), {
    text: "{{ $affiliate->referral_url }}",
    width: 200,
    height: 200,
    colorDark: "#4F46E5",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
});

function copyLink(elementId) {
    const input = document.getElementById(elementId);
    input.select();
    document.execCommand('copy');
    alert('Link copied to clipboard!');
}

function generateUTMLink() {
    const source = document.getElementById('utmSource').value;
    const medium = document.getElementById('utmMedium').value;
    const campaign = document.getElementById('utmCampaign').value;
    const content = document.getElementById('utmContent').value;

    if (!source || !medium || !campaign) {
        alert('Please fill in at least Source, Medium, and Campaign Name');
        return;
    }

    let url = "{{ $affiliate->referral_url }}";
    const params = new URLSearchParams();
    
    params.append('utm_source', source);
    params.append('utm_medium', medium);
    params.append('utm_campaign', campaign);
    if (content) params.append('utm_content', content);

    const finalUrl = url + '?' + params.toString();
    
    document.getElementById('generatedLink').value = finalUrl;
    document.getElementById('generatedLinkContainer').classList.remove('hidden');
}

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const url = canvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = 'thetradevisor-qr-code.png';
    link.href = url;
    link.click();
}

function showEmailTemplate() {
    alert('Email template feature coming soon!');
}

function showSocialTemplates() {
    alert('Social media templates coming soon!');
}

function showBanners() {
    alert('Banner ads coming soon!');
}
</script>
@endsection
