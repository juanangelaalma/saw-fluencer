### Penjelasan Fungsi Setiap Tabel

1. **`users` (Tabel Pengguna)**
   * **Fungsi:** Menyimpan data orang yang bisa masuk (login) ke dalam sistem (misalnya Admin atau Manajer).
   * **Isi Penting:** Memiliki *username*, *password*, *role* (hak akses/jabatan), dan *is_active* untuk menentukan apakah akun tersebut masih boleh digunakan atau sudah diblokir.

2. **`criteria` (Tabel Kriteria Penilaian)**
   * **Fungsi:** Menyimpan daftar patokan/kriteria yang digunakan untuk menilai influencer. Contohnya: "Jumlah Followers", "Engagement Rate", atau "Harga Endorse".
   * **Isi Penting:** Memiliki *weight* (bobot seberapa penting kriteria ini, misal 20%) dan *type* (jenisnya, biasanya di sistem seperti SAW ini tipenya adalah *Benefit* jika nilai besar itu bagus, atau *Cost* jika nilai kecil itu bagus).

3. **`sub_criteria` (Tabel Detail Tingkatan Nilai Kriteria)**
   * **Fungsi:** Menyimpan rentang nilai atau skala penjabaran dari kriteria utama.
   * **Contoh Praktis:** Untuk kriteria "Jumlah Followers", tabel ini mengatur tingkatan skalanya:
     * Level 1: 1.000 - 10.000 followers (Nilai 1)
     * Level 2: 10.001 - 50.000 followers (Nilai 2)
     * ...dan seterusnya.

4. **`influencers` (Tabel Data Influencer)**
   * **Fungsi:** Menyimpan daftar orang (influencer) yang akan diseleksi atau dinilai oleh sistem.
   * **Isi Penting:** Menyimpan *name* (Nama asli) dan *username* (Username sosial medianya).

5. **`influencer_scores` (Tabel Nilai Influencer)**
   * **Fungsi:** Tabel ini adalah tabel penghubung (*bridge*) yang mencatat nilai yang didapatkan oleh seorang influencer pada kriteria tertentu.
   * **Isi Penting:**
     * Menghubungkan *Siapa* (influencer_id) dapat nilai di *Kriteria Apa* (criterion_id).
     * Memiliki *raw_value* (Nilai asli/mentah, misal jumlah followers aslinya 15.000).
     * Memiliki *likert_value* (Nilai skala hasil konversi, misal masuk ke skala/level 2).