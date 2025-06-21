document.addEventListener('DOMContentLoaded', function () {

    // Fungsionalitas Sidebar Toggle pakai Dua Tombol Burger
    const sidebar = document.querySelector('.sidebar');
    const container = document.querySelector('.container');

    const hamburgerMenuInSidebar = document.getElementById('hamburgerMenu');
    const openSidebarBtnInHeader = document.getElementById('openSidebarBtn');

    // Fungsi untuk membuka/menutup sidebar dan atur layout kontennya
    function toggleSidebar() {
        // Cek apakah elemen sidebar ada
        if (sidebar) {
            sidebar.classList.toggle('collapsed');
        }

        // Cek apakah container utama ada
        if (container) {
            container.classList.toggle('sidebar-is-collapsed');
        }

        // Update tampilan tombol burger setelah sidebar ditoggle
        updateBurgerVisibility();
    }

    // Fungsi untuk atur tombol burger mana yang tampil (di sidebar atau header)
    function updateBurgerVisibility() {
        // Cek dulu apakah semua elemen penting tersedia
        if (!sidebar || !hamburgerMenuInSidebar || !openSidebarBtnInHeader) {
            return; // Kalau ada yang nggak ketemu, skip aja
        }

        // Cek apakah sidebar dalam kondisi collapsed
        if (sidebar.classList.contains('collapsed')) {
            // Sidebar lagi disembunyikan
            hamburgerMenuInSidebar.style.display = 'none';
            openSidebarBtnInHeader.style.display = 'inline-block';
        } else {
            // Sidebar lagi tampil
            hamburgerMenuInSidebar.style.display = 'inline-block';
            openSidebarBtnInHeader.style.display = 'none';
        }
    }

    // Kalau tombol burger di sidebar ada, pasang event klik
    if (hamburgerMenuInSidebar) {
        hamburgerMenuInSidebar.addEventListener('click', toggleSidebar);
    }

    // Kalau tombol burger di header ada, pasang event klik
    if (openSidebarBtnInHeader) {
        openSidebarBtnInHeader.addEventListener('click', toggleSidebar);
    }

    // Panggil saat pertama kali untuk atur tampilan tombol burger
    updateBurgerVisibility();

    // Fungsionalitas Form Laporan Sampah Dinamis
    const reportItemsContainer = document.getElementById('report-items-container');
    const addReportItemButton = document.getElementById('addReportItem');

    // Fungsi untuk update tampilan item laporan (nomor, tombol hapus)
    function updateReportItemUI() {
        // Pastikan container item laporan ada
        if (!reportItemsContainer) return;

        const items = reportItemsContainer.querySelectorAll('.report-item');
        items.forEach((item, index) => {
            const h3 = item.querySelector('h3');

            // Kalau elemen h3 ada, update judul dan tombol hapusnya
            if (h3) {
                const oldRemoveBtn = h3.querySelector('.remove-item-btn');
                if (oldRemoveBtn) {
                    oldRemoveBtn.remove();
                }

                h3.innerHTML = `Laporan Sampah #${index + 1} <button type="button" class="remove-item-btn" title="Hapus item ini">&times;</button>`;
            }

            const removeBtn = item.querySelector('.remove-item-btn');

            // Tampilkan tombol hapus kalau item lebih dari 1
            if (removeBtn) {
                removeBtn.style.display = (items.length > 1) ? 'inline-block' : 'none';
            }
        });
    }

    // Kalau ada setidaknya 1 item saat awal, update tampilannya
    if (reportItemsContainer && reportItemsContainer.querySelector('.report-item')) {
        updateReportItemUI();
    }

    // Tambahkan item baru saat tombol tambah diklik
    if (addReportItemButton && reportItemsContainer) {
        addReportItemButton.addEventListener('click', function () {
            const firstItemTemplate = reportItemsContainer.querySelector('.report-item');

            // Cek apakah template item ditemukan
            if (!firstItemTemplate) {
                console.error("Template item laporan (.report-item) nggak ditemukan!");
                return;
            }

            // Duplikat item pertama
            const newItem = firstItemTemplate.cloneNode(true);
            const currentItemIndex = reportItemsContainer.querySelectorAll('.report-item').length;

            // Kosongkan semua input di item baru
            newItem.querySelectorAll('input[type="text"], input[type="number"], textarea').forEach(input => input.value = '');
            newItem.querySelectorAll('select').forEach(select => select.selectedIndex = 0);

            // Update semua id agar unik
            newItem.querySelectorAll('[id]').forEach(element => {
                const oldId = element.id;
                if (oldId && oldId.includes('_')) {
                    const baseId = oldId.substring(0, oldId.lastIndexOf('_') + 1);
                    const newGeneratedId = baseId + currentItemIndex;
                    element.id = newGeneratedId;

                    const correspondingLabel = newItem.querySelector(`label[for="${oldId}"]`);
                    if (correspondingLabel) {
                        correspondingLabel.htmlFor = newGeneratedId;
                    }
                }
            });

            reportItemsContainer.appendChild(newItem);
            updateReportItemUI();
        });
    }

    // Pasang event untuk hapus item kalau tombol "×" diklik
    if (reportItemsContainer) {
        reportItemsContainer.addEventListener('click', function (event) {
            // Cek apakah yang diklik itu tombol hapus
            if (event.target && event.target.classList.contains('remove-item-btn')) {
                const items = reportItemsContainer.querySelectorAll('.report-item');

                // Hanya hapus kalau item masih lebih dari 1
                if (items.length > 1) {
                    event.target.closest('.report-item').remove();
                    updateReportItemUI();
                }
            }
        });
    }

});
