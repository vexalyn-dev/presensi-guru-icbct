const fs = require('fs');
const p = 'D:/icb-ct-absensi-guru/resources/views/classrooms/qr.blade.php';
let c = fs.readFileSync(p, 'utf8');

// Add a hidden data element for the classroom name (avoids blade syntax in JS)
// Insert right before the <script> tag
const scriptTag = '<script>\n        async function downloadQRCode()';
const dataElement = '<input type="hidden" id="classroomNameData" value="{{ $classroom->name }}">\n    <script>\n        async function downloadQRCode()';

if (!c.includes('id="classroomNameData"')) {
    c = c.replace(scriptTag, dataElement);
}

// Replace the entire script block
const scriptStart = c.indexOf('<script>');
const scriptEnd = c.indexOf('</script>', scriptStart);

const newScript = `<script>
        async function downloadQRCode() {
            const svg = document.querySelector('#qr-code-container svg');
            if (!svg) { alert('QR Code tidak ditemukan!'); return; }

            const className = document.getElementById('classroomNameData').value;
            const templateUrl = '{{ asset('images/qr-code.png') }}';

            // Canvas: half of template (1414x2000 -> 707x1000)
            const CW = 707, CH = 1000;
            const canvas = document.createElement('canvas');
            canvas.width = CW;
            canvas.height = CH;
            const ctx = canvas.getContext('2d');

            // Load template
            const tplImg = await new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = () => reject(new Error('Template tidak bisa dimuat'));
                img.src = templateUrl;
            });
            ctx.drawImage(tplImg, 0, 0, CW, CH);

            // Draw class name below SCAN HERE (y=325 pushes it below)
            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 26px Inter, sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(className.toUpperCase(), CW / 2, 325);

            // QR position and size (smaller and lower)
            const QR_X = 105, QR_Y = 530, QR_SIZE = 390;

            // Convert SVG to Image
            const svgData = new XMLSerializer().serializeToString(svg);
            const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
            const svgUrl = URL.createObjectURL(svgBlob);
            const qrImg = await new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = () => reject(new Error('QR SVG gagal dimuat'));
                img.src = svgUrl;
            });

            ctx.drawImage(qrImg, QR_X, QR_Y, QR_SIZE, QR_SIZE);
            URL.revokeObjectURL(svgUrl);

            // Download
            canvas.toBlob(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = className.replace(/[^a-zA-Z0-9\\s]/g, '').replace(/\\s+/g, '_') + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 'image/png');
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>`;

c = c.substring(0, scriptStart) + newScript + c.substring(scriptEnd + '</script>'.length);
fs.writeFileSync(p, c, 'utf8');
console.log('Done');
