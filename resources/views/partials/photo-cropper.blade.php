{{--
  Photo Cropper + Viewer Partial
  Include di halaman profile dengan: @include('partials.photo-cropper')
  Requires: Cropper.js (loaded via CDN di bawah)
--}}

{{-- ── CROP MODAL ── --}}
<div id="photo-crop-modal"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(10,15,30,0.85);
            backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:24px;width:100%;max-width:520px;overflow:hidden;
                box-shadow:0 32px 64px rgba(15,23,42,0.3);animation:cropModalIn 0.3s cubic-bezier(0.22,1,0.36,1);">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0F172A,#1E293B);padding:18px 24px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;background:rgba(250,204,21,0.15);border:1px solid rgba(250,204,21,0.3);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="#FACC15" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h3m0 0V4m0 3a7 7 0 017 7M20 17h-3m0 0v3m0-3a7 7 0 01-7-7"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:0.95rem;font-weight:700;color:#fff;margin:0;">Sesuaikan Foto</p>
                    <p style="font-size:0.72rem;color:rgba(255,255,255,0.45);margin:0;">Drag, zoom & rotate untuk menyesuaikan</p>
                </div>
            </div>
            <button onclick="closeCropModal()" style="width:32px;height:32px;background:rgba(255,255,255,0.08);border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.6);transition:background 0.2s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Crop area --}}
        <div style="padding:20px 24px;background:#0F172A;">
            <div style="border-radius:12px;overflow:hidden;background:#000;max-height:320px;">
                <img id="crop-image-src" style="max-width:100%;display:block;">
            </div>
        </div>

        {{-- Controls --}}
        <div style="padding:16px 24px;background:#F8FAFC;border-top:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            {{-- Rotate --}}
            <button type="button" onclick="cropperInstance && cropperInstance.rotate(-90)"
                    title="Putar Kiri"
                    style="flex:1;min-width:48px;height:42px;background:#fff;border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;"
                    onmouseover="this.style.borderColor='#0F172A'" onmouseout="this.style.borderColor='#E2E8F0'">
                <svg width="16" height="16" fill="none" stroke="#0F172A" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <button type="button" onclick="cropperInstance && cropperInstance.rotate(90)"
                    title="Putar Kanan"
                    style="flex:1;min-width:48px;height:42px;background:#fff;border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;"
                    onmouseover="this.style.borderColor='#0F172A'" onmouseout="this.style.borderColor='#E2E8F0'">
                <svg width="16" height="16" fill="none" stroke="#0F172A" stroke-width="2" viewBox="0 0 24 24" style="transform:scaleX(-1);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            {{-- Flip H --}}
            <button type="button" onclick="cropperInstance && cropperInstance.scaleX(cropFlipX = cropFlipX * -1)"
                    title="Balik Horizontal"
                    style="flex:1;min-width:48px;height:42px;background:#fff;border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;"
                    onmouseover="this.style.borderColor='#0F172A'" onmouseout="this.style.borderColor='#E2E8F0'">
                <svg width="16" height="16" fill="none" stroke="#0F172A" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 3H5a2 2 0 00-2 2v14m6 0v-9m6 9V3h3a2 2 0 012 2v14a2 2 0 01-2 2h-3m-6 0H7"/>
                </svg>
            </button>
            {{-- Reset --}}
            <button type="button" onclick="cropperInstance && cropperInstance.reset(); cropFlipX = 1;"
                    title="Reset"
                    style="flex:1;min-width:48px;height:42px;background:#fff;border:1.5px solid #E2E8F0;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.15s;color:#64748B;font-size:0.7rem;font-weight:600;gap:4px;"
                    onmouseover="this.style.borderColor='#0F172A'" onmouseout="this.style.borderColor='#E2E8F0'">
                Reset
            </button>
            {{-- Apply --}}
            <button type="button" onclick="applyCrop()"
                    style="flex:2;min-width:100px;height:42px;background:#0F172A;color:#fff;border:none;border-radius:10px;cursor:pointer;font-size:0.88rem;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;transition:opacity 0.15s;box-shadow:0 4px 12px rgba(15,23,42,0.22);"
                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Pakai Foto Ini
            </button>
        </div>
    </div>
</div>

{{-- ── PHOTO VIEWER LIGHTBOX ── --}}
<div id="photo-viewer-modal"
     onclick="closePhotoViewer()"
     style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0);
            align-items:center;justify-content:center;padding:20px;
            transition:background 0.25s ease;cursor:zoom-out;">
    <img id="viewer-photo-img"
         style="max-width:min(500px,90vw);max-height:min(500px,85vh);border-radius:20px;
                object-fit:cover;display:block;
                transform:scale(0.4);opacity:0;
                transition:transform 0.32s cubic-bezier(0.22,1,0.36,1), opacity 0.28s ease;
                box-shadow:0 32px 80px rgba(0,0,0,0.6);
                cursor:default;"
         onclick="event.stopPropagation()">
</div>

{{-- Cropper.js CDN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

<style>
@keyframes cropModalIn {
    from { opacity:0; transform:scale(0.94) translateY(16px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>

<script>
// ─── State ────────────────────────────────────────────
var cropperInstance = null;
var cropFlipX       = 1;
var cropSourceInput = null;  // input file yang trigger crop
var cropPreviewTargets = []; // array of img element IDs to update after crop

// ─── Open crop modal ──────────────────────────────────
function openCropModal(file, inputEl, previewIds) {
    cropSourceInput   = inputEl;
    cropPreviewTargets = previewIds || [];

    var reader = new FileReader();
    reader.onload = function(e) {
        var imgEl = document.getElementById('crop-image-src');
        imgEl.src = e.target.result;

        var modal = document.getElementById('photo-crop-modal');
        modal.style.display = 'flex';

        // Destroy old instance
        if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
        cropFlipX = 1;

        // Init Cropper.js
        cropperInstance = new Cropper(imgEl, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.85,
            responsive: true,
            guides: true,
            center: true,
            highlight: false,
            cropBoxResizable: true,
            minCropBoxWidth: 80,
            minCropBoxHeight: 80,
            background: false,
        });
    };
    reader.readAsDataURL(file);
}

// ─── Close crop modal ─────────────────────────────────
function closeCropModal() {
    document.getElementById('photo-crop-modal').style.display = 'none';
    if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
    // Reset input
    if (cropSourceInput) cropSourceInput.value = '';
}

// ─── Apply crop → set to hidden input + preview ───────
function applyCrop() {
    if (!cropperInstance) return;

    var canvas = cropperInstance.getCroppedCanvas({ width: 600, height: 600, imageSmoothingQuality: 'high' });
    var dataUrl = canvas.toDataURL('image/jpeg', 0.92);

    // Set hidden input (base64)
    var hidden = document.getElementById('cropped_photo_data');
    if (hidden) hidden.value = dataUrl;

    // Clear the file input so server knows to use base64
    if (cropSourceInput) cropSourceInput.value = '';

    // Update all preview images — profile-avatar di card, photo-preview di form admin
    cropPreviewTargets.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.src = dataUrl;
    });
    // Selalu update profile-avatar kalau ada
    var avatar = document.getElementById('profile-avatar');
    if (avatar) avatar.src = dataUrl;

    // Update photo info text
    var info = document.getElementById('photo-info');
    if (info) { info.textContent = '✓ Foto siap disimpan — klik Simpan Profil'; info.style.color = '#16a34a'; }

    // Show cancel button
    var cancelBtn = document.getElementById('photo-cancel-btn');
    if (cancelBtn) cancelBtn.classList.contains('hidden') ? cancelBtn.classList.replace('hidden','inline-flex') : null;

    // Update drop zone border
    var zone = document.getElementById('photo-drop-zone');
    if (zone) { zone.style.borderStyle = 'solid'; zone.style.borderColor = '#0F172A'; }

    closeCropModal();
}

// ─── Photo viewer ─────────────────────────────────────
function openPhotoViewer(imgSrc) {
    var modal = document.getElementById('photo-viewer-modal');
    var img   = document.getElementById('viewer-photo-img');
    img.src = imgSrc;
    modal.style.display = 'flex';

    // Force reflow then animate in
    requestAnimationFrame(function() {
        requestAnimationFrame(function() {
            modal.style.background = 'rgba(0,0,0,0.88)';
            img.style.transform    = 'scale(1)';
            img.style.opacity      = '1';
        });
    });
}

function closePhotoViewer() {
    var modal = document.getElementById('photo-viewer-modal');
    var img   = document.getElementById('viewer-photo-img');
    modal.style.background = 'rgba(0,0,0,0)';
    img.style.transform    = 'scale(0.4)';
    img.style.opacity      = '0';
    setTimeout(function() {
        modal.style.display = 'none';
        img.src = '';
    }, 280);
}

// Close modals on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeCropModal(); closePhotoViewer(); }
});
// Close crop modal on backdrop click
document.getElementById('photo-crop-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCropModal();
});

// ─── 3-dot menu toggle ────────────────────────────────
function togglePhotoMenu(e) {
    if (e) e.stopPropagation();
    var menu = document.getElementById('photo-3dot-menu');
    if (!menu) return;
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('photo-menu-wrap');
    var menu = document.getElementById('photo-3dot-menu');
    if (menu && wrap && !wrap.contains(e.target)) menu.style.display = 'none';
});

// ─── Delete photo ─────────────────────────────────────
function deletePhoto() {
    if (!confirm('Hapus foto profil? Akan kembali ke foto default.')) return;
    // Set flag to delete
    var hidden = document.getElementById('cropped_photo_data');
    if (hidden) hidden.value = 'DELETE';
    // Reset preview to default avatar
    var defaultUrl = window.profileDefaultPhoto || '';
    var imgs = ['photo-preview', 'profile-avatar'];
    imgs.forEach(function(id) {
        var el = document.getElementById(id);
        if (el && defaultUrl) el.src = defaultUrl;
    });
    // Update info text
    var info = document.getElementById('photo-info');
    if (info) { info.textContent = 'Foto akan dihapus saat kamu klik Simpan'; info.style.color = '#ef4444'; }
    // Show cancel btn
    var btn = document.getElementById('photo-cancel-btn');
    if (btn && btn.classList.contains('hidden')) btn.classList.replace('hidden', 'inline-flex');
    // Close menu
    var menu = document.getElementById('photo-3dot-menu');
    if (menu) menu.style.display = 'none';
}
</script>
