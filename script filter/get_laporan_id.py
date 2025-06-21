data = []

print('Masukkan data ID dan datetime (misal: 1 06/04/2025 22:24), ketik "done" jika selesai:')

while True:
    line = input().strip()
    if line.lower() == "done":
        break
    if not line:
        continue
    parts = line.split(maxsplit=1)
    if len(parts) == 2:
        id_val = int(parts[0])
        timestamp = parts[1]
        data.append((id_val, timestamp))

current_value = 1
previous_pair = None

for idx, (id_val, timestamp) in enumerate(data):
    current_pair = (id_val, timestamp)
    if idx == 0:
        print(current_value)
    else:
        if current_pair != previous_pair:
            current_value += 1
        print(current_value)
    previous_pair = current_pair
