
# Dokumen Kebutuhan Bisnis (BRD)
## Sistem Prediksi Pinjaman Online

### 1. **Tujuan**
Dokumen ini menjelaskan kebutuhan bisnis untuk **Sistem Prediksi Pinjaman Online**, yang bertujuan untuk memprediksi kemampuan membayar peminjam serta menentukan jumlah dan lama pinjaman yang disetujui berdasarkan data pengguna. Sistem ini akan digunakan oleh lembaga keuangan untuk menilai kelayakan aplikasi pinjaman.

### 2. **Lingkup Pekerjaan (Scope)**
Sistem ini akan menyediakan REST API berbasis **FastAPI** yang memanfaatkan model machine learning untuk melakukan prediksi:

- **Kemampuan Bayar**: Apakah peminjam diprediksi mampu atau tidak mampu membayar (klasifikasi).
- **Total Pinjaman**: Jumlah maksimum pinjaman yang dapat disetujui.
- **Lama Pinjaman**: Periode/cicilan pinjaman dalam satuan bulan.

Validasi data input juga akan dilakukan sebelum prediksi untuk memastikan data memenuhi syarat.

### 3. **Asumsi**
- Model machine learning telah dilatih sebelumnya dan disimpan dalam format `.pkl`.
- Model dimuat menggunakan library `joblib`.
- Model yang digunakan:
  - `RandomForestClassifier` untuk memprediksi kemampuan bayar.
  - `RandomForestRegressor` untuk memprediksi jumlah pinjaman dan lama pinjaman.
- Fitur input:
  - Umur
  - Gaji
  - Jumlah pinjaman
  - Tanggal pinjaman dan tanggal pelunasan

### 4. **Kebutuhan Fungsional**
#### 4.1 **Permintaan Prediksi (Endpoint POST /predict)**
Input dalam format JSON:

```json
{
  "name": "Budi Santoso",
  "age": 30,
  "salary": 5000000,
  "loan_amount": 8000000,
  "loan_date": "2025-01-01",
  "payday": "2025-01-20"
}
```

Keterangan:
- `name`: Nama lengkap peminjam.
- `age`: Usia (minimal 21 tahun).
- `salary`: Gaji bulanan (minimal 1.000.000).
- `loan_amount`: Jumlah pinjaman yang dimohon (maksimal 10.000.000).
- `loan_date`: Tanggal pinjaman dimulai (`YYYY-MM-DD`).
- `payday`: Tanggal pelunasan (`YYYY-MM-DD`).

#### 4.2 **Respon Prediksi**
Hasil prediksi akan dikembalikan dalam format JSON:

```json
{
  "predicted_kemampuan_bayar": 1,
  "total_pemberian_pinjaman": 6835263.15,
  "lama_pinjaman": 6
}
```

- `predicted_kemampuan_bayar`: `1` untuk "mampu bayar", `0` untuk "tidak mampu bayar".
- `total_pemberian_pinjaman`: Prediksi jumlah pinjaman disetujui.
- `lama_pinjaman`: Prediksi lama waktu pinjaman (dalam bulan).

#### 4.3 **Validasi Input**
- **Gaji** harus ≥ 1.000.000.
- **Usia** harus ≥ 21 tahun.
- **Jumlah Pinjaman** maksimal 10.000.000.
- **Tanggal Pinjaman dan Tanggal Pelunasan** harus berada dalam **bulan dan tahun yang sama**.

Jika validasi gagal, sistem akan mengembalikan error 400:

Contoh:
```json
{
  "detail": "Salary must be at least 1,000,000 to make a prediction."
}
```

#### 4.4 **Penanganan Error**
Jika terjadi error sistem, API akan mengembalikan:
```json
{
  "detail": "Prediction failed: [deskripsi error]"
}
```

### 5. **Kebutuhan Non-Fungsional**
- **Performa**: Waktu respon maksimal 2 detik.
- **Keamanan**: Implementasi API key atau rate limiting.
- **Skalabilitas**: Dapat menangani banyak permintaan secara bersamaan.

### 6. **Stack Teknologi**
- **API Backend**: FastAPI (Python)
- **Model ML**: Scikit-learn + Joblib
- **Database**: MariaDB (jika ingin menyimpan data riwayat prediksi)
- **Frontend (opsional)**: Laravel 12 + Filament v3 untuk panel admin

### 7. **Aturan Validasi**
| Aturan | Keterangan | Error |
|--------|------------|-------|
| Gaji minimal | ≥ 1.000.000 | `Salary must be at least 1,000,000` |
| Usia minimal | ≥ 21 tahun | `Age must be at least 21.` |
| Jumlah pinjaman maksimal | ≤ 10.000.000 | `Loan amount must not exceed 10,000,000.` |
| Tanggal pinjaman dan pelunasan harus dalam bulan & tahun yang sama | Cek `loan_date` & `payday` | `Loan date and payday must be in the same month and year.` |

### 8. **Contoh Respon**
#### Mampu Bayar:
```json
{
  "predicted_kemampuan_bayar": 1,
  "total_pemberian_pinjaman": 7500000.00,
  "lama_pinjaman": 6
}
```

#### Tidak Mampu Bayar:
```json
{
  "predicted_kemampuan_bayar": 0,
  "total_pemberian_pinjaman": 1500000.00,
  "lama_pinjaman": 2
}
```

### 9. **User Story**
1. **Sebagai pengguna**, saya ingin mengirimkan data pinjaman dan mendapatkan hasil apakah saya layak diberi pinjaman dan seberapa besar jumlahnya.
2. **Sebagai admin**, saya ingin mengelola sistem prediksi dan mengintegrasikannya ke sistem manajemen internal.
3. **Sebagai pengembang**, saya ingin sistem ini memiliki validasi ketat sebelum menjalankan prediksi untuk menghindari hasil tidak akurat.

### 10. **Kesimpulan**
Sistem Prediksi Pinjaman ini akan membantu lembaga keuangan untuk:
- Menyederhanakan proses pengajuan pinjaman.
- Mengotomatisasi penilaian kelayakan nasabah.
- Memberikan estimasi jumlah dan durasi pinjaman yang sesuai dengan profil keuangan pengguna.

---

**Dokumen ini menjadi acuan utama dalam proses pengembangan dan integrasi sistem prediksi pinjaman online.**

# NB
```php 
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' fastapi_app
docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' data_mining
docker network create shared_net
docker network connect shared_net fastapi_app
docker network connect shared_net data_mining
```