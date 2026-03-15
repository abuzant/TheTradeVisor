<script>
// Fix for Cloudflare SRI issue
if (typeof window !== 'undefined') {
    const originalCreateElement = document.createElement;
    document.createElement = function(tagName) {
        const element = originalCreateElement.call(this, tagName);
        if (tagName.toLowerCase() === 'script' && element.src && element.src.includes('cloudflareinsights.com')) {
            element.removeAttribute('integrity');
            element.removeAttribute('crossorigin');
        }
        return element;
    };
}
</script>
