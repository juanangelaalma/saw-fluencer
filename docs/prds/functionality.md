# 📄 PRD — Part 1: Business Context
**Sistem Pendukung Keputusan Penentuan Influencer**
**PT. Behaestex | Versi 1.0**
*Prepared based on: Proposal Skripsi Ahmad Zulfian Pratama Putra*

---

## 1. Executive Summary

### 1.1 Latar Belakang Masalah

PT. Behaestex, produsen sarung dan busana muslim terkemuka yang berdiri sejak 1953, menghadapi tantangan nyata dalam era digital marketing: **pemilihan influencer untuk kampanye promosi produk masih dilakukan secara subjektif** — berdasarkan popularitas umum atau rekomendasi personal, tanpa ada sistem perhitungan kuantitatif yang terstandarisasi.

Kondisi ini menimbulkan tiga masalah utama:
- **Inefisiensi anggaran** — biaya promosi tidak terukur efektivitasnya sebelum kampanye berjalan
- **Bias keputusan** — pemilihan dipengaruhi faktor non-data, bukan performa aktual influencer
- **Tidak ada benchmark** — tidak ada standar baku untuk membandingkan ratusan kandidat influencer secara konsisten

### 1.2 Solusi yang Diajukan

Membangun **Sistem Pendukung Keputusan (SPK)** berbasis web menggunakan metode **Simple Additive Weighting (SAW)** yang mampu meranking kandidat influencer secara objektif berdasarkan 6 kriteria terukur: Engagement Rate, Follower, Jumlah Like, Jumlah Komentar, Rate Card, dan Niche.

### 1.3 Objectives

| # | Objective | Ukuran Keberhasilan |
|---|-----------|-------------------|
| O1 | Menghasilkan ranking influencer berbasis data | Sistem menghasilkan output ranking dari ≥1 influencer yang diinput |
| O2 | Mempercepat proses seleksi influencer | Proses perhitungan selesai dalam <5 detik untuk ≤202 data |
| O3 | Memberikan transparansi perhitungan | Setiap tahap normalisasi & bobot dapat dilihat di halaman Perhitungan |
| O4 | Mendukung pengambilan keputusan manajemen | Hasil dapat diekspor ke PDF dan dipresentasikan |

---

## 2. User Personas

### Persona 1 — Admin (Staff Digital Marketing)

| Atribut | Detail |
|---------|--------|
| **Nama Fiktif** | Rizky, 27 tahun |
| **Jabatan** | Staff Digital Marketing & E-commerce |
| **Tanggung Jawab** | Mengumpulkan data influencer, menginput ke sistem, menjalankan perhitungan |
| **Tingkat Tech-Savvy** | Menengah — familiar dengan Excel dan platform Sociabuzz |
| **Goal Utama** | Memasukkan data influencer dengan cepat dan mendapatkan hasil ranking yang akurat |
| **Pain Point** | Selama ini harus membandingkan ratusan influencer secara manual di spreadsheet, memakan waktu dan rawan human error |
| **Kebutuhan Kritis** | Fitur import massal (CSV/Excel), form input yang intuitif, dan feedback jelas saat ada data yang salah |

### Persona 2 — Manajer (Pengambil Keputusan)

| Atribut | Detail |
|---------|--------|
| **Nama Fiktif** | Bapak Hendra, 42 tahun |
| **Jabatan** | Supervisor Digital Marketing & E-commerce Specialist |
| **Tanggung Jawab** | Menetapkan bobot kriteria, mereview hasil ranking, mengambil keputusan final |
| **Tingkat Tech-Savvy** | Rendah-Menengah — lebih fokus pada hasil akhir daripada proses teknis |
| **Goal Utama** | Mendapatkan rekomendasi influencer terbaik yang sesuai anggaran dan target pasar |
| **Pain Point** | Keputusan sebelumnya sering dipertanyakan karena tidak ada data pendukung yang solid |
| **Kebutuhan Kritis** | Dashboard hasil yang bersih, bisa ekspor PDF untuk dibawa ke rapat, dan bisa mengubah bobot kriteria jika strategi berubah |

---

## 3. User Stories & Acceptance Criteria

### Epic 1: Manajemen Pengguna & Autentikasi

---

**US-01 | Login ke Sistem**
> *Sebagai Admin/Manajer, saya ingin bisa login menggunakan username dan password, agar hanya pengguna berwenang yang bisa mengakses sistem.*

**Acceptance Criteria:**
- [ ] Form login menampilkan field Username dan Password
- [ ] Jika kredensial benar → redirect ke halaman Dashboard sesuai role
- [ ] Jika kredensial salah → muncul pesan error "Username atau password salah"
- [ ] Setelah 5x percobaan gagal berturut-turut, akun terkunci sementara selama 10 menit
- [ ] Terdapat tombol Logout yang menghapus session

---

**US-02 | Manajemen User oleh Admin**
> *Sebagai Admin, saya ingin bisa membuat, mengedit, dan menghapus akun pengguna, agar akses sistem dapat dikontrol.*

**Acceptance Criteria:**
- [ ] Admin dapat membuat akun baru dengan field: Nama, Username, Password, Role (Admin/Manajer)
- [ ] Admin dapat mengedit data akun yang sudah ada (kecuali mengubah role diri sendiri)
- [ ] Admin dapat menonaktifkan akun (soft delete, bukan hard delete)
- [ ] Manajer **tidak** memiliki akses ke halaman manajemen user
- [ ] Password baru wajib minimal 8 karakter

---

### Epic 2: Manajemen Kriteria & Sub Kriteria

---

**US-03 | Kelola Kriteria**
> *Sebagai Admin, saya ingin bisa menambah, mengedit, dan menghapus kriteria beserta bobotnya, agar parameter penilaian bisa disesuaikan dengan kebutuhan perusahaan.*

**Acceptance Criteria:**
- [ ] Admin dapat menambah kriteria baru dengan field: Nama Kriteria, Bobot (%), Jenis (Benefit/Cost)
- [ ] Total bobot semua kriteria aktif **harus** = 100% — sistem menampilkan validasi real-time
- [ ] Jika total bobot ≠ 100%, tombol Simpan dinonaktifkan dan muncul peringatan
- [ ] Admin dapat mengedit nama, bobot, dan jenis kriteria yang sudah ada
- [ ] Kriteria tidak bisa dihapus jika sudah ada data sub kriteria terkait — muncul pesan konfirmasi

---

**US-04 | Kelola Sub Kriteria (Skala Likert)**
> *Sebagai Admin, saya ingin mendefinisikan rentang nilai untuk setiap kriteria, agar konversi data mentah ke skala Likert 1–5 berjalan otomatis.*

**Acceptance Criteria:**
- [ ] Setiap kriteria memiliki 5 sub kriteria dengan nilai 1 (Kurang) hingga 5 (Sangat Baik)
- [ ] Admin dapat mengedit parameter rentang (misal: batas bawah/atas untuk tiap level)
- [ ] Sistem otomatis mengkonversi nilai input influencer ke skala Likert berdasarkan sub kriteria yang aktif
- [ ] Perubahan sub kriteria hanya berlaku untuk perhitungan **baru** — tidak mengubah data historis

---

### Epic 3: Manajemen Data Influencer

---

**US-05 | Input Manual Data Influencer**
> *Sebagai Admin, saya ingin bisa menginput data influencer satu per satu melalui form, agar saya bisa menambah atau mengoreksi data individu dengan mudah.*

**Acceptance Criteria:**
- [ ] Form input memiliki field: Nama Influencer, Username, Engagement Rate (%), Jumlah Follower, Jumlah Like, Jumlah Komentar, Rate Card (Rp), Niche (multi-select)
- [ ] Field Engagement Rate hanya menerima input numerik dengan format persen
- [ ] Field Rate Card hanya menerima input angka (format Rupiah ditampilkan otomatis)
- [ ] Field Niche menggunakan checkbox multi-select dengan pilihan: Fashion, Lifestyle, Religi, Budaya, Komunitas, Event, Entertainment, dan lainnya
- [ ] Setelah simpan, sistem otomatis mengkonversi nilai ke skala Likert dan menampilkan preview hasilnya

---

**US-06 | Import Massal Data Influencer (CSV/Excel)**
> *Sebagai Admin, saya ingin bisa mengupload file CSV atau Excel berisi banyak influencer sekaligus, agar proses input data tidak memakan waktu terlalu lama.*

**Acceptance Criteria:**
- [ ] Sistem menyediakan tombol **"Download Template"** untuk format CSV/Excel yang benar
- [ ] Admin dapat mengupload file berformat .csv atau .xlsx (maksimal 5MB)
- [ ] Sebelum diproses, sistem menampilkan **preview tabel** data yang akan diimport
- [ ] Sistem memvalidasi setiap baris — baris dengan data tidak valid ditandai merah dan **tidak diimport**, baris valid tetap diproses
- [ ] Setelah proses import selesai, sistem menampilkan ringkasan: "X data berhasil diimport, Y data gagal" beserta detail baris yang gagal
- [ ] Data yang sudah ada (berdasarkan username) akan **dilewati** (skip), bukan ditimpa

---

**US-07 | Edit & Hapus Data Influencer**
> *Sebagai Admin, saya ingin bisa mengedit atau menghapus data influencer yang sudah diinput, agar data tetap akurat dan relevan.*

**Acceptance Criteria:**
- [ ] Setiap baris data influencer di tabel memiliki tombol Edit dan Hapus
- [ ] Saat Edit, form pre-filled dengan data yang sudah ada
- [ ] Saat Hapus, muncul dialog konfirmasi "Apakah Anda yakin ingin menghapus influencer ini?"
- [ ] Hapus bersifat **permanent** (hard delete) karena tidak ada kebutuhan riwayat per data individu

---

### Epic 4: Perhitungan SAW

---

**US-08 | Jalankan Perhitungan SAW**
> *Sebagai Admin, saya ingin bisa menjalankan perhitungan SAW dari semua data influencer yang aktif, agar sistem menghasilkan ranking terbaru.*

**Acceptance Criteria:**
- [ ] Terdapat tombol **"Hitung"** di halaman Perhitungan
- [ ] Sebelum menghitung, sistem memvalidasi: minimal ada 2 influencer dan total bobot = 100%
- [ ] Jika validasi gagal, tombol Hitung tidak aktif dan muncul pesan error spesifik
- [ ] Proses perhitungan menampilkan tiga tahap secara terurut: Matriks Keputusan → Matriks Normalisasi → Nilai Akhir (Vi)
- [ ] Hasil perhitungan baru **menimpa** hasil sebelumnya
- [ ] Proses selesai dalam <5 detik untuk ≤202 data influencer

---

**US-09 | Lihat Hasil Perangkingan**
> *Sebagai Admin dan Manajer, saya ingin melihat hasil ranking influencer beserta skornya, agar bisa mengambil keputusan berdasarkan data.*

**Acceptance Criteria:**
- [ ] Halaman Hasil menampilkan tabel ranking dengan kolom: Rank, Nama Influencer, Username, Nilai Vi, dan detail nilai per kriteria
- [ ] Tabel diurutkan otomatis dari nilai Vi tertinggi ke terendah
- [ ] Manajer memiliki akses **read-only** ke halaman ini (tidak bisa mengedit/menghapus)
- [ ] Terdapat indikator visual (badge/warna) untuk Top 3 influencer

---

### Epic 5: Ekspor Laporan

---

**US-10 | Ekspor Hasil ke PDF**
> *Sebagai Manajer, saya ingin bisa mengekspor hasil perangkingan ke format PDF, agar bisa dibagikan dan dipresentasikan kepada manajemen.*

**Acceptance Criteria:**
- [ ] Terdapat tombol **"Export PDF"** di halaman Hasil
- [ ] PDF yang dihasilkan memuat: Judul laporan, tanggal generate, tabel ranking lengkap, dan detail bobot kriteria yang digunakan
- [ ] File PDF terunduh otomatis dengan nama format: `Ranking_Influencer_[YYYY-MM-DD].pdf`
- [ ] PDF dapat digenerate baik oleh Admin maupun Manajer
