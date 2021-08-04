<?php CUtil::InitJSCore(['ajax', 'jquery', 'popup']); ?>

<link rel="shortcut icon" href="/virtual-product/TemplateData/favicon.ico">
<link rel="stylesheet" href="/virtual-product/TemplateData/style.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<script type="text/javascript">

    function hidePopup() {
        $('#product_3d').hide();
    }

    BX.ready(function () {

        const addAnswer = new BX.PopupWindow("product_3d", null, {
            content: BX('unity-container'),
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            draggable: {restrict: false},
        });
        $('#click_test').click(function () {
            addAnswer.show(); // появление окна
        });

        const container = document.querySelector("#unity-container");
        const canvas = document.querySelector("#unity-canvas");
        const loadingBar = document.querySelector("#unity-loading-bar");
        const progressBarFull = document.querySelector("#unity-progress-bar-full");
        const fullscreenButton = document.querySelector("#unity-fullscreen-button");
        const warningBanner = document.querySelector("#unity-warning");

        // Shows a temporary message banner/ribbon for a few seconds, or
        // a permanent error message on top of the canvas if type=='error'.
        // If type=='warning', a yellow highlight color is used.
        // Modify or remove this function to customize the visually presented
        // way that non-critical warnings and error messages are presented to the
        // user.
        function unityShowBanner(msg, type) {
            function updateBannerVisibility() {
                warningBanner.style.display = warningBanner.children.length ? 'block' : 'none';
            }

            const div = document.createElement('div');
            div.innerHTML = msg;
            warningBanner.appendChild(div);
            if (type == 'error') div.style = 'background: red; padding: 10px;';
            else {
                if (type == 'warning') div.style = 'background: yellow; padding: 10px;';
                setTimeout(function () {
                    warningBanner.removeChild(div);
                    updateBannerVisibility();
                }, 5000);
            }
            updateBannerVisibility();
        }

        const buildUrl = "/virtual-product/Build";
        const loaderUrl = buildUrl + "/virtual-market.loader.js";
        const config = {
            dataUrl: buildUrl + "/virtual-market.data",
            frameworkUrl: buildUrl + "/virtual-market.framework.js",
            codeUrl: buildUrl + "/virtual-market.wasm",
            streamingAssetsUrl: "StreamingAssets",
            companyName: "DefaultCompany",
            productName: "unimarket",
            productVersion: "0.1",
            showBanner: unityShowBanner,
        };

        // By default Unity keeps WebGL canvas render target size matched with
        // the DOM size of the canvas element (scaled by window.devicePixelRatio)
        // Set this to false if you want to decouple this synchronization from
        // happening inside the engine, and you would instead like to size up
        // the canvas DOM size and WebGL render target sizes yourself.
        // config.matchWebGLToCanvasSize = false;

        if (/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)) {
            // Mobile device style: fill the whole browser client area with the game canvas:

            var meta = document.createElement('meta');
            meta.name = 'viewport';
            meta.content = 'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, shrink-to-fit=yes';
            document.getElementsByTagName('head')[0].appendChild(meta);
            container.className = "unity-mobile";

            // To lower canvas resolution on mobile devices to gain some
            // performance, uncomment the following line:
            // config.devicePixelRatio = 1;

            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';

            unityShowBanner('WebGL builds are not supported on mobile devices.');
        } else {
            // Desktop style: Render the game canvas in a window that can be maximized to fullscreen:

            canvas.style.width = "960px";
            canvas.style.height = "600px";
        }

        loadingBar.style.display = "block";

        var script = document.createElement("script");
        script.src = loaderUrl;
        script.onload = () => {
            createUnityInstance(canvas, config, (progress) => {
                progressBarFull.style.width = 100 * progress + "%";
            }).then((unityInstance) => {
                loadingBar.style.display = "none";
                unityInstance.SendMessage('Main', 'SetBxSessId', BX.bitrix_sessid());
                unityInstance.SendMessage('Main', 'SetSiteId', BX.message('SITE_ID'));
                unityInstance.SendMessage('Main', 'SetConfirmOrderUrl', 'df');
                unityInstance.SendMessage('Main', 'SetServerName', window.location.host);
                fullscreenButton.onclick = () => {
                    unityInstance.SetFullscreen(1);
                };
            }).catch((message) => {
                alert(message);
            });
        };
        document.body.appendChild(script);

    });
</script>

<button id="click_test"><?= GetMessage('OPEN_MODEL_BUTTON') ?></button>
<div id="unity-container" class="unity-desktop">
    <span class="popup-window-close-icon popup-window-titlebar-close-icon" onclick="hidePopup()"
          style="right: 20px; top: 10px;"></span>
    <canvas id="unity-canvas"></canvas>
    <div id="unity-loading-bar">
        <div id="unity-logo"></div>
        <div id="unity-progress-bar-empty">
            <div id="unity-progress-bar-full"></div>
        </div>
    </div>
</div>
