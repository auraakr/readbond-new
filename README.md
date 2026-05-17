# 📖 Readbond

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/MySQL-00758F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

**Readbond** adalah platform web book tracking berbasis komunitas yang menggabungkan fitur jejaring sosial dengan elemen gamifikasi untuk membangun kebiasaan membaca yang konsisten.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
| :--- | :--- |
| **🔥 Reading Streak** | Melacak konsistensi membaca harian secara *real-time* untuk membangun kebiasaan yang baik. |
| **📅 Horizontal Timeline Row** | Fitur kalender horisontal interaktif untuk melihat catatan log berdasarkan tanggal spesifik dengan cepat. |
| **🎭 Mood & Reflection Diary** | Mengabadikan perasaan Anda setelah membaca lewat indikator *mood* visual (😊, 😢, 🤩, 🥱) beserta catatan refleksi tanpa batas kata. |
| **📊 Smart Dashboard Stats** | Statistik lengkap mencakup total buku unik yang dibaca tahun ini, jumlah halaman terkumpul, dan progres bulanan. |
| **🌌 Dark Elegant UI** | Desain antarmuka modern bernuansa gelap (*slate-900*) dengan aksen warna ungu (*purple-600*) yang nyaman di mata untuk sesi malam hari. |

---

## 🛠️ Arsitektur & Teknologi

* **Backend Framework:** Laravel 10+ / 11+
* **Frontend Styling:** Tailwind CSS v3+
* **Database:** MySQL / PostgreSQL
* **Date Handling:** Carbon (Date Manipulation Library)
* **Design Philosophy:** Clean timeline logging & micro-interactions

---

## 🚀 Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menjalankan **Readbond** di komputer lokal Anda:

### 1. Kloning Repositori
```bash
git clone [https://github.com/username/readbond.git](https://github.com/username/readbond.git)
cd readbond
```

### 2. Instalasi Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Migrasi Database
```bash
php artisan migrate
```

### 5. Jalankan Aplikasi
```bash
composer run dev
```
