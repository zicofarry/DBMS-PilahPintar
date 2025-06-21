seen = set()
output = []

print("Masukkan baris (atau ketik done untuk berhenti): ")
while True:
    line = input()
    if line == "done":
        break
    if line not in seen:
        seen.add(line)
        output.append(line)

print("\nBaris unik yang dimasukkan:")
for line in output:
    print(line)
