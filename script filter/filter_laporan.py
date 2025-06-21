import random
from datetime import datetime

seen = set()
results = []

while True:
    line = input()
    if line.lower() == "done":
        break

    parts = line.strip().split(maxsplit=2)
    if len(parts) != 3:
        print("Format salah! Gunakan: [ID_UTAMA] [ID_EMAIL] [mm/dd/yyyy hh:mm:ss]")
        continue

    id_utama, id_email, raw_timestamp = parts

    try:
        # Ubah format dari mm/dd/yyyy ke yyyy-mm-dd
        dt = datetime.strptime(raw_timestamp, "%m/%d/%Y %H:%M:%S")
        formatted_timestamp = dt.strftime("%Y-%m-%d %H:%M:%S")
    except ValueError:
        print("Format tanggal salah! Gunakan: mm/dd/yyyy hh:mm:ss")
        continue

    key = (id_utama, id_email, formatted_timestamp)

    if key not in seen:
        results.append(key)
        seen.add(key)

# Print hasil unik dengan angka acak 1-6
for id_utama, id_email, timestamp in results:
    angka_acak = random.randint(1, 6)
    print(f"{id_utama},{id_email},{timestamp},{angka_acak}")
