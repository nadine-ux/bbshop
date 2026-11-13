{{-- resources/views/partials/barcode-scan.blade.php
     Zone scanner (caméra + photo). À inclure dans @section('content') :
        @include('partials.barcode-scan')
     Le bouton qui ouvre la caméra doit avoir l'id="btnCamera" (voir la page appelante).
     Le JS est dans partials/barcode-scan-js.blade.php --}}

<div id="cameraBox" class="camera-box d-none">
    <div class="camera-box__header">
        <span><i class="fas fa-camera"></i> Scanner en cours...</span>
        <div class="camera-box__btns">
            <button type="button" id="btnCameraSwitch" title="Changer de caméra">
                <i class="fas fa-sync-alt"></i>
            </button>
            <button type="button" id="btnCameraClose" title="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <div id="scannerContainer"></div>
    <div id="cameraStatus">Initialisation...</div>
</div>

<label class="btn-photo-scan">
    <input type="file" accept="image/*" capture="environment" id="barcodeFileInput" style="display:none">
    <i class="fas fa-image"></i> Photo du code (si le scan direct ne marche pas)
</label>
<div id="fileScanHolder" style="display:none"></div>

<style>
.camera-box {
    position: relative; border-radius: 12px; overflow: hidden;
    border: 2px solid #27ae60; background: #000; margin-top: .6rem;
}
.camera-box__header {
    display: flex; justify-content: space-between; align-items: center;
    padding: .55rem 1rem; background: #27ae60; color: white;
    font-weight: 600; font-size: .88rem;
}
.camera-box__header button {
    background: rgba(255,255,255,.2); color: white; border: none;
    border-radius: 7px; padding: .25rem .65rem; cursor: pointer;
}
.camera-box__btns { display: flex; gap: .4rem; }
#cameraStatus { padding: .4rem 1rem; background: #111; color: #aaa; font-size: .78rem; }

/* IMPORTANT : pas de object-fit:cover sur la vidéo (décale la zone de lecture) */
#scannerContainer { border: none !important; padding: 0 !important; min-height: 0; background: #000; }
#scannerContainer video { width: 100% !important; height: auto !important; display: block; }
#scannerContainer img[src*="scanning"] { display: none !important; }
#qr-shaded-region { border-color: rgba(0,0,0,.35) !important; }
#qr-shaded-region > div { display: none !important; }

.btn-photo-scan {
    margin: .6rem 0 0; padding: .7rem; border: 2px dashed #e9ecef; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    font-size: .82rem; font-weight: 600; color: #7f8c8d; cursor: pointer; transition: all .2s;
}
.btn-photo-scan:hover { border-color: #27ae60; color: #27ae60; }
</style>