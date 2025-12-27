# Jago Academy POS - Dummy Data Generator

Script untuk generate data dummy (categories & products) ke Backend API.

## 📋 Data yang Akan Dibuat

### Categories (6 kategori):
1. **Makanan Utama** - Nasi, Mie, Soto, dll
2. **Minuman Panas** - Kopi, Teh, Coklat Panas
3. **Minuman Dingin** - Es Teh, Jus, Milkshake
4. **Snack & Cemilan** - Kentang Goreng, Pisang Goreng, Lumpia
5. **Dessert** - Es Krim, Pancake, Puding
6. **Paket Combo** - Paket Hemat & Bundle

### Products (~47 produk):
- 8 Makanan Utama (Rp 17.000 - Rp 28.000)
- 7 Minuman Panas (Rp 5.000 - Rp 15.000)
- 10 Minuman Dingin (Rp 5.000 - Rp 20.000)
- 7 Snack (Rp 7.000 - Rp 12.000)
- 6 Dessert (Rp 8.000 - Rp 15.000)
- 5 Paket Combo (Rp 12.000 - Rp 35.000)

Semua produk sudah include stock quantity yang realistis.

---

## 🚀 Cara Menggunakan

### Step 1: Login & Dapatkan Token

**Opsi A: Via Aplikasi (Recommended)**
1. Buka aplikasi Jago Academy POS
2. Login dengan akun kamu
3. Token akan otomatis tersimpan di local storage

**Opsi B: Via API (Manual)**
```bash
curl -X POST http://192.168.1.101:8000/api/login \
  -H 'Content-Type: application/json' \
  -d '{
    "email": "your@email.com",
    "password": "yourpassword"
  }'
```

Response:
```json
{
  "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "user": { ... }
}
```

Copy value dari `access_token`.

### Step 2: Edit Script

Buka file `generate_dummy_data.sh` dan edit bagian ini:

```bash
# CONFIGURATION - EDIT INI!
BASE_URL="http://192.168.1.101:8000/api"  # Sesuaikan dengan IP backend kamu
TOKEN="YOUR_ACCESS_TOKEN_HERE"             # <-- PASTE TOKEN DI SINI!
```

Ganti:
- `BASE_URL` dengan IP backend kamu (cek di `lib/core/constants/variables.dart`)
- `TOKEN` dengan access_token dari step 1

### Step 3: Jalankan Script

```bash
# Berikan permission execute
chmod +x generate_dummy_data.sh

# Jalankan script
./generate_dummy_data.sh
```

atau

```bash
bash generate_dummy_data.sh
```

### Step 4: Verifikasi

1. Refresh aplikasi POS
2. Cek halaman **Product & Stock**
3. Cek halaman **Point of Sale** - produk sudah muncul!

---

## 🎨 Output Script

Script akan menampilkan progress seperti ini:

```
╔════════════════════════════════════════════╗
║   Jago Academy POS - Dummy Data Generator ║
╚════════════════════════════════════════════╝

✓ Token found

[1/2] Creating Categories...

  Creating: Makanan Utama
  ✓ Created with ID: 1

  Creating: Minuman Panas
  ✓ Created with ID: 2

  ...

✓ Categories created: 6

[2/2] Creating Products...

  Category: Makanan Utama
  Creating: Nasi Goreng Spesial (Rp 25000)
  ✓ Created

  ...

╔════════════════════════════════════════════╗
║            ✓ SELESAI!                      ║
╚════════════════════════════════════════════╝

Summary:
  • Categories created: 6
  • Products created: ~47 items

Happy selling with Jago Academy POS! 🚀
```

---

## 🛠️ Troubleshooting

### Error: "Token belum di-set!"
- Edit file `generate_dummy_data.sh`
- Ganti `TOKEN="YOUR_ACCESS_TOKEN_HERE"` dengan token hasil login

### Error: "Failed to connect"
- Pastikan backend sudah running
- Cek `BASE_URL` di script, sesuaikan dengan IP backend kamu
- Coba ping: `ping 192.168.1.101`

### Error: "Unauthorized 401"
- Token expired atau salah
- Login ulang dan dapatkan token baru
- Update `TOKEN` di script

### Error: "Category/Product creation failed"
- Cek log backend untuk detail error
- Pastikan database sudah di-migrate
- Pastikan user kamu punya permission (Owner/Manager)

### Products tidak muncul di aplikasi
- Pull to refresh di aplikasi
- Restart aplikasi
- Clear cache: Settings > Clear Data

---

## 📝 Customisasi Data

Kamu bisa edit script untuk customize data dummy:

### Menambah Category
```bash
CATEGORIES=(
    "Makanan Utama"
    "Minuman Panas"
    "Kategori Baru Kamu"  # <-- tambah di sini
)
```

### Menambah Product
```bash
# Tambahkan di section yang sesuai
create_product "Nama Produk Baru" $CAT_MAKANAN 50000 100
#              ^nama              ^category   ^harga ^stock
```

### Ubah Harga/Stock
Edit angka di parameter `create_product`:
```bash
create_product "Nasi Goreng" $CAT_MAKANAN 25000 50
#                                         ^harga ^stock
```

---

## 🔄 Reset Data (Opsional)

Jika ingin hapus semua data dan generate ulang:

1. **Via Backend/Database:**
   ```sql
   DELETE FROM products;
   DELETE FROM categories;
   ```

2. **Generate ulang:**
   ```bash
   ./generate_dummy_data.sh
   ```

---

## 📞 Support

Jika ada masalah:
1. Cek backend logs
2. Cek response error di terminal
3. Pastikan semua dependencies backend sudah terinstall
4. Contact team untuk bantuan

---

**Happy coding! 🚀**
*Jago Academy POS SaaS Edition v1.0.1*
