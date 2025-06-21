email_to_id = {
    "gherryalviart769@upi.edu": 1,
    "dickaf547@gmail.com": 2,
    "dickaf547@upi.edu": 3,
    "juliarahmawati@upi.edu": 4,
    "anasmf098@gmail.com": 5,
    "umarexandromeda@upi.edu": 6,
    "hawadwiafina@upi.edu": 7,
    "dhiyau27@upi.edu": 8,
    "fitravgg22@upi.edu": 9,
    "viviagustina@upi.edu": 10,
    "muhammadazmi36@upi.edu": 11,
    "rizki2008fs@gmail.com": 12,
    "nafisasyakir.a04@upi.edu": 13,
    "fauzia26rahma@gmail.com": 14,
    "nisrina.natun3@upi.edu": 15,
    "shidqirasyad70@upi.edu": 16,
    "mrizkiana4432@upi.edu": 17,
    "frosttskrt@gmail.com": 18,
    "mmudrik@upi.edu": 19,
    "naurafaizah@upi.edu": 20,
    "nadzallad@gmail.com": 21,
    "rizkauliaa2715@upi.edu": 22,
    "selikakanajmii@upi.edu": 23,
    "nurulatiqah@upi.edu": 24,
    "rafisub@upi.edu": 25,
    "imam76@upi.edu": 26,
    "dzokodolog@upi.edu": 27,
    "muhrangganp.05@upi.edu": 28,
    "bintangfajarputra@upi.edu": 29,
    "shakilaaulia@upi.edu": 30,
    "firdaridzki.1@upi.edu": 31,
    "iqbalrizkymaulana@upi.edu": 32,
    "maulanaa1408@upi.edu": 33,
    "adwarsalman45@gmail.com": 34,
    "pindandss2@gmail.com": 35,
    "basriaazka.workspace@upi.edu": 36,
    "rex_id@upi.edu": 37,
    "putiiwsyalala@upi.edu": 38,
    "naufal_rbbni@upi.edu": 39,
    "mahendradevid30@upi.edu": 40,
    "nurabdillah@upi.edu": 41,
    "rafikasyas23s@gmail.com": 42,
    "repa.pitrianni@upi.edu": 43,
    "naufalzahid@upi.edu": 44,
    "zahranzaidan3@gmail.com": 45,
    "ervinakusnanda@upi.edu": 46,
    "daffadhiaacandra@upi.edu": 47,
    "zulfianrais3101@gmail.com": 48,
    "frizkiaskusnendar@upi.edu": 49,
    "yassarm@upi.edu": 50,
    "wilsontulus@upi.edu": 51,
    "farahmauliida@upi.edu": 52,
    "rifadanindra@upi.edu": 53,
    "aryaps@upi.edu": 54,
    "saidbanx12@gmail.com": 55,
    "nihaaprill@upi.edu": 56,
    "rifkyfadhillahakbar@upi.edu": 57,
    "najmialifahh@gmail.com": 58,
    "rappepu@upi.edu": 59,
    "raffienanda@upi.edu": 60,
    "malanandika13@upi.edu": 61,
    "adwarsalman45@upi.edu": 62,
    "juliarahma12606@gmail.com": 63,
    "atharghaisan002@upi.edu": 64,
    "zharfanfw@upi.edu": 65
}

# Menyimpan email inputan
input_emails = []
print('Masukkan daftar email (satu per baris), ketik "done" jika selesai:')

while True:
    email = input().strip()
    if email.lower() == "done":
        break
    input_emails.append(email)

print("\n=== Hasil ID dari Email yang Dimasukkan ===")
for email in input_emails:
    id_result = email_to_id.get(email, "Email tidak ditemukan")
    print(f"{id_result}")
