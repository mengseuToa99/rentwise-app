
// Progressive JavaScript Loading
(function() {
    // Load non-critical JS after page load
    window.addEventListener("load", function() {
        // Defer non-critical scripts
        const scripts = [
            "/js/charts.js",
            "/js/calendar.js"
        ];
        
        scripts.forEach(function(src) {
            const script = document.createElement("script");
            script.src = src;
            script.async = true;
            document.head.appendChild(script);
        });
    });
    
    // Intersection Observer for lazy loading
    if ("IntersectionObserver" in window) {
        const lazyImages = document.querySelectorAll("img[data-src]");
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove("lazy");
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }
})();
