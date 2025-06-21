# Set untuk menyimpan email unik
seen_emails = set()

# List untuk menyimpan hasil akhir (untuk menjaga urutan)
unique_entries = []

print('Masukkan data email dan angka (pisahkan dengan tab), ketik "done" jika selesai:')

while True:
    line = input().strip()
    if line.lower() == "done":
        break
    if '\t' not in line:
        print("INVALID INPUT (gunakan tab untuk pisahkan email dan angka)")
        continue

    email, angka = line.split('\t', 1)

    if email not in seen_emails:
        seen_emails.add(email)
        unique_entries.append(f"{email}\t{angka}")

# Output hasil unik
print("\nHasil yang sudah difilter:")
for entry in unique_entries:
    print(entry)
