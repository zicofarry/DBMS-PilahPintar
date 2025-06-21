jenis_to_id = {
    "plastik": 1,
    "kayu": 2,
    "kardus": 3,
    "sisa makanan": 4,
    "kertas": 5,
    "tumbuhan": 6,
    "kulit buah": 7,
    "kaleng": 8,
    "styrofoam": 9,
    "kertas organik": 10,
    "kaca": 11,
    "kotoran": 12,
    "tisu": 13,
    "tissue": 13,
    "karung": 14,
    "kapas": 15,
    "baterai": 16,
    "sayur": 17,
    "sayuran": 17,
    "karton": 18,
    "sampah dapur": 19,
    "oli bekas": 20,
    "plafon kalsiboard": 21
}

inputs = []

print("Masukkan nama jenis sampah (satu per baris), ketik 'done' jika selesai:")

while True:
    line = input().strip()
    if line.lower() == "done":
        break
    inputs.append(line)

print("\nHasil ID jenis:")
i = 2
for jenis in inputs:
    jenis_lower = jenis.lower()
    id_val = jenis_to_id.get(jenis_lower)
    # print(f"{i} -> ", end="")
    if id_val:
        print(id_val)
    else:
        print("UNKNOWN")
    i = i + 1
