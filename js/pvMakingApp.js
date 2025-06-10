// This script checks if the user is on iOS and displays a bar with options to open the app or get it from the App Store.
(function() {
    function onReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    onReady(function() {
        function getMobileOS() {
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;
            if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                return "ios";
            }
            return null;
        }
        var os = getMobileOS();
        if (os) {
            var appUrl = "https://apps.apple.com/app/6746516633"; // iOS app link only
            var appScheme = "parfumvaultmaking://";

            // Modern Bootstrap 5 style banner
            var bar = document.createElement("div");
            bar.className = "alert alert-primary d-flex align-items-center justify-content-between shadow-sm border-0";
            bar.style.position = "fixed";
            bar.style.top = "0";
            bar.style.left = "0";
            bar.style.right = "0";
            bar.style.zIndex = "9999";
            bar.style.marginBottom = "0";
            bar.style.padding = "0.75rem 1.25rem";
            bar.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-phone me-2" style="font-size:1.5rem;"></i>
                    <span class="fw-semibold">For a better experience:</span>
                </div>
                <div>
                    <a href="#" id="openAppBtn" class="btn btn-success btn-sm me-2" style="display:none;">Open app</a>
                    <a href="${appUrl}" id="getAppBtn" class="btn btn-outline-dark btn-sm me-2" style="display:none;" target="_blank">Get the iOS app</a>
                </div>
                <button type="button" class="btn-close ms-2" id="closeAppBar" aria-label="Close"></button>
            `;

            document.body.prepend(bar);

            document.getElementById("closeAppBar").onclick = function() {
                bar.style.display = "none";
                document.body.style.paddingTop = "";
            };

            // Push page content down so bar doesn't cover it
            document.body.style.paddingTop = "64px";

            // Try to detect if app is installed
            function checkAppInstalled(callback) {
                var timeout;
                var hasFocus = false;
                function onBlur() {
                    hasFocus = true;
                }
                window.addEventListener('blur', onBlur);

                // Try to open the app
                var iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = appScheme;
                document.body.appendChild(iframe);

                timeout = setTimeout(function() {
                    window.removeEventListener('blur', onBlur);
                    document.body.removeChild(iframe);
                    // If blur did not happen, app is not installed
                    callback(true);
                }, 1200);

                // If blur happens, app is installed
                window.addEventListener('blur', function handler() {
                    clearTimeout(timeout);
                    window.removeEventListener('blur', handler);
                    document.body.removeChild(iframe);
                    callback(false);
                });
            }

            // Show correct button based on app presence
            checkAppInstalled(function(notInstalled) {
                if (notInstalled) {
                    document.getElementById("getAppBtn").style.display = "inline";
                } else {
                    document.getElementById("openAppBtn").style.display = "inline";
                }
            });

            // Open app if Open button clicked
            document.getElementById("openAppBtn").onclick = function(e) {
                e.preventDefault();
                window.location = appScheme;
            };
        }
    });
})();