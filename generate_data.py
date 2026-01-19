import csv
import random
from datetime import datetime, timedelta

# Path ke file CSV
csv_path = 'd:\\CODE\\XAMPP\\XAMPP-8.2.12\\htdocs\\Vigazafarm\\ml\\data\\dataset_puyuh_180_hari.csv'

# Baca data terakhir dari CSV
with open(csv_path, 'r') as f:
    reader = csv.reader(f)
    header = next(reader)
    last_row = None
    for row in reader:
        last_row = row

# Parse data terakhir
last_date = datetime.strptime(last_row[0], '%Y-%m-%d')
last_umur = int(last_row[1])
last_pakan = float(last_row[2])
last_protein = int(last_row[3])
last_berat = float(last_row[4])
last_harga = int(last_row[5])
last_produksi = int(last_row[6])
last_biaya = float(last_row[7])

# Fungsi untuk generate data baru
def generate_row(date, umur, pakan, protein, berat, harga, produksi_base):
    # Increment nilai
    umur += 1
    pakan = max(20.0, pakan + random.uniform(-0.1, 0.1))  # Stabil sekitar 20-25
    protein = 17 if umur > 100 else 20  # Turun ke 17 setelah umur 100
    berat += random.uniform(0.1, 0.5)  # Increment berat
    harga += random.randint(10, 20)  # Increment harga
    produksi = max(400, min(1000, produksi_base + random.randint(-50, 50)))  # Variasi produksi
    biaya = round((harga * pakan) / produksi, 2)  # Hitung biaya

    return [
        date.strftime('%Y-%m-%d'),
        umur,
        round(pakan, 1),
        protein,
        round(berat, 1),
        harga,
        produksi,
        biaya
    ]

# Generate 20,000 baris tambahan
rows_to_add = 400000
new_rows = []

current_date = last_date + timedelta(days=1)
current_umur = last_umur
current_pakan = last_pakan
current_protein = last_protein
current_berat = last_berat
current_harga = last_harga
current_produksi = last_produksi

for i in range(rows_to_add):
    row = generate_row(current_date, current_umur, current_pakan, current_protein, current_berat, current_harga, current_produksi)
    new_rows.append(row)

    # Update untuk iterasi berikutnya
    current_date += timedelta(days=1)
    current_umur = int(row[1])
    current_pakan = float(row[2])
    current_protein = int(row[3])
    current_berat = float(row[4])
    current_harga = int(row[5])
    current_produksi = int(row[6])

# Tambahkan ke CSV
with open(csv_path, 'a', newline='') as f:
    writer = csv.writer(f)
    writer.writerows(new_rows)

print(f"Berhasil menambahkan {rows_to_add} baris data ke dataset_puyuh_180_hari.csv")