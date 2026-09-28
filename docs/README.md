# 📸 Screenshots — ICB CT Presensi Guru

Dokumentasi visual tampilan halaman-halaman utama sistem.

> Semua screenshot diambil dari versi production di [presensi-guru.smkicb-teknika.sch.id](https://presensi-guru.smkicb-teknika.sch.id)

---

## Struktur Folder

```
docs/screenshots/
├── auth/          → Halaman login, lupa password, reset password
├── admin/         → Dashboard admin, live monitoring, log aktivitas, pengaturan
├── guru/          → Dashboard guru, presensi harian, presensi kelas, riwayat, izin
├── piket/         → Dashboard piket, manual presensi, approval izin
└── mobile/        → Tampilan mobile (responsive)
```

---

## 🔐 Auth

| Halaman | Preview |
|---------|---------|
| Login | ![Login](screenshots/auth/login.png) |
| Lupa Password | ![Forgot Password](screenshots/auth/forgot-password.png) |
| Reset Password | ![Reset Password](screenshots/auth/reset-password.png) |

---

## 🛠️ Admin

| Halaman | Preview |
|---------|---------|
| Dashboard | ![Dashboard Admin](screenshots/admin/dashboard.png) |
| Live Monitoring | ![Live Monitoring](screenshots/admin/live-monitoring.png) |
| Log Aktivitas | ![Log Aktivitas](screenshots/admin/activity-log.png) |
| Data Guru | ![Data Guru](screenshots/admin/teachers.png) |
| Laporan | ![Laporan](screenshots/admin/reports.png) |
| Pengaturan | ![Pengaturan](screenshots/admin/settings.png) |

---

## 👨‍🏫 Guru

| Halaman | Preview |
|---------|---------|
| Dashboard | ![Dashboard Guru](screenshots/guru/dashboard.png) |
| Presensi Harian | ![Presensi Harian](screenshots/guru/attendance.png) |
| Presensi Kelas | ![Presensi Kelas](screenshots/guru/class-attendance.png) |
| Riwayat | ![Riwayat](screenshots/guru/history.png) |
| Pengajuan Izin | ![Izin](screenshots/guru/leave.png) |
| Jadwal Mengajar | ![Jadwal](screenshots/guru/schedule.png) |
| Profil | ![Profil](screenshots/guru/profile.png) |

---

## 🏫 Piket

| Halaman | Preview |
|---------|---------|
| Dashboard Piket | ![Dashboard Piket](screenshots/piket/dashboard.png) |
| Manual Presensi | ![Manual Presensi](screenshots/piket/manual-attendance.png) |
| Approval Izin | ![Approval Izin](screenshots/piket/leave-approval.png) |

---

## 📱 Mobile

| Halaman | Preview |
|---------|---------|
| Login Mobile | ![Login Mobile](screenshots/mobile/login.png) |
| Dashboard Mobile | ![Dashboard Mobile](screenshots/mobile/dashboard.png) |
| Presensi Mobile | ![Presensi Mobile](screenshots/mobile/attendance.png) |

---

## Cara Nambahin Screenshot

1. Ambil screenshot halaman yang mau didokumentasiin
2. Simpan di folder yang sesuai, pakai nama file yang sama persis kayak tabel di atas
3. Format yang disupport: `.png`, `.jpg`, `.webp`
4. Resolusi recommended: **1280×800** buat desktop, **390×844** buat mobile
5. Commit dan push — otomatis keliatan di sini dan di README utama

```bash
git add docs/screenshots/
git commit -m "docs: tambah screenshots halaman X"
git push
```

---

<div align="center">

*Dokumentasi ini bagian dari project [ICB CT — Sistem Presensi Guru](../README.md)*

</div>
