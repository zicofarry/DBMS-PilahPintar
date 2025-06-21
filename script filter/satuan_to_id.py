satuan_to_id = {
    "gram": 1,
    "g": 1,
    "gr": 1,
    "buah": 2,
    "ml": 3,
    "lembar": 4,
    "pcs": 2,
    "gram": 1,
    "botol": 2,
    "bungkus": 5,
    "gelas": 2,
    "cup": 2,
    "kantong": 5,
    "kresek": 5
}

inputs = []

print("Masukkan satuan (satu per baris), ketik 'done' jika selesai:")

while True:
    line = input().strip()
    if line.lower() == "done":
        break
    inputs.append(line)

print("\nHasil ID satuan:")
for satuan in inputs:
    satuan_lower = satuan.lower()
    id_val = satuan_to_id.get(satuan_lower)
    if id_val:
        print(id_val)
    else:
        print("UNKNOWN")
