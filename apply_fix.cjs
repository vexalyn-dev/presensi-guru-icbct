const fs = require('fs');
const p = 'D:/icb-ct-absensi-guru/resources/views/classrooms/qr.blade.php';
let c = fs.readFileSync(p, 'utf8');

// Add hidden input before script tag
const beforeScript = '\n    <script>\n        async function downloadQRCode()';
const withHidden = '\n    <input type="hidden" id="classroomNameData" value="{{ $classroom->name }}">\n    <script>\n        async function downloadQRCode()';

if (!c.includes('id="classroomNameData"')) {
    c = c.replace(beforeScript, withHidden);
    fs.writeFileSync(p, c, 'utf8');
    console.log('added hidden input');
} else {
    console.log('already has hidden input');
}

// Verify
var lines = c.split('\n');
for (var i = 109; i <= 120; i++) {
    console.log(i + 1 + ': ' + JSON.stringify(lines[i]));
}
