// This script checks if the user is on iOS or Android and displays a bar with options to open the app or get it from the App Store or Google Play.
(function() {
    function onReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    onReady(function() {
        var userAgent = navigator.userAgent || window.opera;
        var isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;
        var isAndroid = /android/i.test(userAgent);

        // App links
        var iosAppUrl = "https://apps.apple.com/app/6746516633";
        var iosAppScheme = "parfumvaultmaking://";
        var androidAppUrl = "https://www.perfumersvault.com/store/apps/pvMaking.apk";
        var androidAppScheme = "parfumvaultmaking://";

        if (!(isIOS || isAndroid)) return;

        var bar = document.createElement("div");
        bar.className = "alert alert-primary d-flex align-items-center justify-content-between shadow-sm border-0";
        bar.style.position = "fixed";
        bar.style.top = "0";
        bar.style.left = "0";
        bar.style.right = "0";
        bar.style.zIndex = "9999";
        bar.style.marginBottom = "0";
        bar.style.padding = "0.75rem 1.25rem";
        bar.style.background = "#0d6efd";
        bar.style.color = "#fff";

        var openBtnId = "openAppBtn";
        var getBtnId = "getAppBtn";
        var appType = isIOS ? "iOS" : "Android";
        var appUrl = isIOS ? iosAppUrl : androidAppUrl;
        var appScheme = isIOS ? iosAppScheme : androidAppScheme;

        bar.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-phone me-2" style="font-size:1.5rem;color:#fff;"></i>
                <span class="fw-semibold">For a better experience:</span>
            </div>
            <div>
                <a href="#" id="${openBtnId}" class="btn btn-light btn-sm me-2" style="font-weight:bold;">Open app</a>
                <a href="${appUrl}" id="${getBtnId}" class="btn btn-outline-light btn-sm me-2" style="font-weight:bold; display:none;" target="_blank">Get the ${appType} app</a>
            </div>
            <button type="button" class="btn-close btn-close-white ms-2" id="closeAppBar" aria-label="Close"></button>
        `;

        document.body.prepend(bar);

        document.getElementById("closeAppBar").onclick = function() {
            bar.style.display = "none";
            document.body.style.paddingTop = "";
        };

        document.body.style.paddingTop = "64px";

        document.getElementById(openBtnId).onclick = function(e) {
            e.preventDefault();
            var now = Date.now();
            window.location = appScheme;
            setTimeout(function() {
                // If user is still on the page after 1s, show the Store link
                if (Date.now() - now < 1500) {
                    document.getElementById(getBtnId).style.display = "inline-block";
                }
            }, 1000);
        };
    });
})();