<?php if (!empty($laporan)) { ?>
    <?php foreach ($laporan as $periode => $items) { ?>
        <tr>
            <td colspan="6" style="font-weight:bold;background:#D6D6D6;">
                <?php echo $periode; ?>
            </td>
        </tr>

        <?php foreach ($items as $l) { ?>
            <tr>
                <td><?php echo $l['nik']; ?></td>
                <td><?php echo ucwords(strtolower($l['nama'])); ?></td>
                <td style="text-align:center;">
            
                    <?php
                        $tglMasuk   = new DateTime($l['tgl_masuk']);
                        $hariIni    = new DateTime();
                        $selisih    = $tglMasuk->diff($hariIni);

                        // echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ' . $selisih->d . ' Hari';
                        echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ';

                    ?>

                </td>
                <td><?php echo $l['nama_jabatan']; ?></td>
               <td>
                    <?php 
                        // 1. Buat array lookup sementara dari $unit agar bisa dicari berdasarkan 'id'
                        // Hasilnya akan seperti: [46 => 'Jatim', 48 => 'Kab Bantul', ...]
                        $unit_lookup = [];
                        if (!empty($unit) && is_array($unit)) {
                            $unit_lookup = array_column($unit, 'nama', 'id');
                        }

                        // 2. Ambil data mentah (misal: "46, 48")
                        $raw_value = isset($l['nama_wilayah']) ? $l['nama_wilayah'] : '';
                        $nama_unit_hasil = [];

                        if (!empty($raw_value)) {
                            $array_id = explode(',', $raw_value);
                            
                            foreach ($array_id as $id) {
                                $clean_id = trim($id); // Hapus spasi, jadi "46" dan "48"
                                
                                // Cek apakah ID ada di dalam lookup table yang baru kita buat
                                if (isset($unit_lookup[$clean_id])) {
                                    $nama_unit_hasil[] = $unit_lookup[$clean_id];
                                }
                            }
                        }

                        // 3. Gabungkan dan cetak. Jika kosong, tampilkan '-'
                        echo !empty($nama_unit_hasil) ? implode(', ', $nama_unit_hasil) : '-';
                    ?>
                </td>
                <td class="text-right"><?php echo $l['total_nilai']; ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="6" class="text-center">
            <i>Tidak ada data</i>
        </td>
    </tr>
<?php } ?>