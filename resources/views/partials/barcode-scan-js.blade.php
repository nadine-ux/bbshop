{{-- resources/views/partials/barcode-scan-js.blade.php
     À inclure dans @section('js') AVANT ton propre script :
        @include('partials.barcode-scan-js')
     Puis dans ta page :
        BarcodeScan.onDetected = function (code) { ... };
--}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
window.BarcodeScan = (function () {

    var html5QrCode = null, scanActive = false, done = false;
    var cameras = [], backCams = [], backIdx = 0;
    var lastText = '', lastTime = 0;

    var api = { onDetected: null, start: startScanner, stop: stopScanner };

    function $(id) { return document.getElementById(id); }

    function setStatus(msg, isErr) {
        var el = $('cameraStatus');
        if (!el) return;
        el.textContent = msg || '';
        el.style.color = isErr ? '#ff6b6b' : '#aaa';
    }

    function newScanner(elId) {
        var F = Html5QrcodeSupportedFormats;
        return new Html5Qrcode(elId, {
            formatsToSupport: [F.EAN_13, F.EAN_8, F.UPC_A, F.UPC_E, F.CODE_128, F.CODE_39, F.CODE_93, F.ITF, F.CODABAR, F.QR_CODE],
            useBarCodeDetectorIfSupported: true,
            verbose: false
        });
    }

    function scanConfig() {
        return {
            fps: 10,
            qrbox: function (w, h) {
                return { width: Math.max(50, Math.floor(w * 0.72)), height: Math.max(50, Math.floor(h * 0.55)) };
            },
            disableFlip: false,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };
    }

    function camErrorMessage(e) {
        var name = (e && e.name) ? e.name : '';
        var msg  = (e && e.message) ? e.message : String(e);
        if (!window.isSecureContext) return "La caméra exige une connexion sécurisée : ouvrez le site en https://";
        if (name === 'NotAllowedError' || /permission/i.test(msg)) return "Accès à la caméra refusé. Autorisez-la dans les réglages du navigateur.";
        if (name === 'NotFoundError' || /no camera|requested device not found/i.test(msg)) return "Aucune caméra détectée sur cet appareil.";
        if (name === 'NotReadableError' || /could not start video/i.test(msg)) return "La caméra est utilisée par une autre application. Fermez-la puis réessayez.";
        return "Impossible de démarrer la caméra : " + msg;
    }

    // Un code est lu : on arrête la caméra et on passe le code à la page
    function submitCode(code) {
        if (done) return;
        done = true;
        setStatus('✅ Détecté : ' + code);
        if (navigator.vibrate) navigator.vibrate(120);
        stopScanner();
        if (typeof api.onDetected === 'function') api.onDetected(String(code).trim());
    }

    function onDecoded(text) {
        var now = Date.now();
        if (text === lastText && now - lastTime < 2000) return;
        lastText = text; lastTime = now;
        submitCode(text);
    }

    async function loadBackCams() {
        var cams = [];
        try { cams = await Html5Qrcode.getCameras(); } catch (e) {}
        cameras = cams || [];
        var back = cameras.filter(function (c) {
            return /back|rear|arri[eè]re|environment|facing back/i.test(c.label || '');
        });
        backCams = back.length ? back : cameras;
    }

    function isVideoBlack() {
        try {
            var v = $('scannerContainer').querySelector('video');
            if (!v || !v.videoWidth) return true;
            var c = document.createElement('canvas');
            c.width = 32; c.height = 32;
            var x = c.getContext('2d');
            x.drawImage(v, 0, 0, 32, 32);
            var d = x.getImageData(0, 0, 32, 32).data, sum = 0;
            for (var i = 0; i < d.length; i += 4) sum += d[i] + d[i + 1] + d[i + 2];
            return (sum / (d.length / 4 * 3)) < 8;
        } catch (e) { return false; }
    }

    async function startWith(src) {
        $('scannerContainer').innerHTML = '';
        html5QrCode = newScanner('scannerContainer');
        await html5QrCode.start(src, scanConfig(), onDecoded, function () {});
    }

    async function startScanner(source) {
        if (scanActive) return;
        done = false;

        $('cameraBox').classList.remove('d-none');

        if (typeof Html5Qrcode === 'undefined') {
            setStatus('Librairie scanner non chargée. Rechargez la page.', true);
            return;
        }
        if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            setStatus('La caméra exige https://. Utilisez « Photo du code ».', true);
            return;
        }

        setStatus('Démarrage de la caméra…');

        if (!backCams.length) await loadBackCams();

        var list = [];
        if (source) list.push(source);
        else {
            for (var k = 0; k < backCams.length; k++) list.push(backCams[(backIdx + k) % backCams.length].id);
            list.push({ facingMode: 'environment' });
        }

        var started = false, lastErr = null;
        for (var i = 0; i < list.length && !started; i++) {
            try {
                await startWith(list[i]);
                await new Promise(function (r) { setTimeout(r, 1200); });
                if (isVideoBlack() && i < list.length - 1 && !source) {
                    try { await html5QrCode.stop(); } catch (x) {}
                    await new Promise(function (r) { setTimeout(r, 400); });
                    continue;
                }
                started = true;
                if (typeof list[i] === 'string') {
                    backIdx = Math.max(0, backCams.findIndex(function (c) { return c.id === list[i]; }));
                }
            } catch (e) {
                lastErr = e;
                console.warn('[scan] échec', list[i], e);
                try { await html5QrCode.stop(); } catch (x) {}
                await new Promise(function (r) { setTimeout(r, 400); });
            }
        }

        if (!started) {
            setStatus(camErrorMessage(lastErr), true);
            html5QrCode = null;
            return;
        }

        scanActive = true;
        setStatus('Caméra active — code à 15–25 cm, bien éclairé et net. Image noire ? Bouton ↻ pour changer de caméra.');
    }

    async function stopScanner() {
        scanActive = false;
        if (html5QrCode) {
            try { await html5QrCode.stop(); } catch (e) {}
            try { html5QrCode.clear(); } catch (e) {}
            html5QrCode = null;
        }
        var c = $('scannerContainer');
        if (c) c.innerHTML = '';
        var box = $('cameraBox');
        if (box) box.classList.add('d-none');
    }

    async function switchCamera() {
        if (!backCams.length) await loadBackCams();
        if (backCams.length <= 1) { setStatus('Une seule caméra disponible.', true); return; }
        backIdx = (backIdx + 1) % backCams.length;
        var next = backCams[backIdx].id;
        await stopScanner();
        await new Promise(function (r) { setTimeout(r, 500); });
        $('cameraBox').classList.remove('d-none');
        await startScanner(next);
    }

    // Lecture via photo (marche aussi sans https)
    async function scanFromFile(input) {
        var file = input.files && input.files[0];
        if (!file) return;
        if (typeof Html5Qrcode === 'undefined') { alert('Librairie scanner non chargée.'); return; }
        var h = newScanner('fileScanHolder');
        try {
            var text = await h.scanFile(file, false);
            done = false;
            submitCode(text);
        } catch (e) {
            console.warn('[scan] photo non lue', e);
            alert('Code non lu sur la photo. Rapprochez-vous, bonne lumière, code bien net et à plat.');
        }
        try { h.clear(); } catch (e) {}
        input.value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var btnCamera = $('btnCamera');
        if (btnCamera) btnCamera.addEventListener('click', function () { startScanner(); });
        $('btnCameraClose').addEventListener('click', stopScanner);
        $('btnCameraSwitch').addEventListener('click', switchCamera);
        $('barcodeFileInput').addEventListener('change', function () { scanFromFile(this); });

        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') stopScanner(); });
        window.addEventListener('beforeunload', function () { if (html5QrCode) stopScanner(); });
    });

    return api;
})();
</script>