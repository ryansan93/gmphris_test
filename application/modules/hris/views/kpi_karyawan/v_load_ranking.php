<?php if (!empty($data_ranking)) { ?>
    <?php foreach ($data_ranking as $jabatan => $rows) { ?>
        <tr>
            <td colspan="6" style="font-weight:bold;background:#f2f2f2;">
                <?php echo $jabatan; ?>
            </td>
        </tr>
        <?php echo $no = 1;?>
        <?php foreach ($rows as $i => $row) { ?>
            <tr>
                <td class="text-center" style="width:5%"><?php echo $no++ ?></td>
                <td class="text-center"><?php echo $row['nik']; ?></td>
                <td class="text-center">
                    <?php
                        $tglMasuk = new DateTime($row['tanggal_masuk']);
                        $hariIni = new DateTime();

                        $selisih = $tglMasuk->diff($hariIni);

                        // echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ' . $selisih->d . ' Hari';
                        echo $selisih->y . ' Tahun ' . $selisih->m . ' Bulan ';

                    ?>
                </td>
                <td><?php echo ucwords(strtolower($row['nama'])); ?></td>
                <td>
                    <?php 
                        // 1. Buat array lookup sementara dari $unit agar bisa dicari berdasarkan 'id'
                        // Hasilnya akan seperti: [46 => 'Jatim', 48 => 'Kab Bantul', ...]
                        $unit_lookup = [];
                        if (!empty($unit) && is_array($unit)) {
                            $unit_lookup = array_column($unit, 'nama', 'id');
                        }

                        // 2. Ambil data mentah (misal: "46, 48")
                        $raw_value = isset($row['nama_wilayah']) ? $row['nama_wilayah'] : '';
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
                <td class="text-center"><a href="#" nik="<?php echo $row['nik']; ?>" total_score="<?php echo number_format($row['score'], 2); ?>" onclick="kpi.view_detail_nilai(this, event)"><?php echo number_format($row['score'], 2); ?></a></td>
            </tr>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="6" class="text-center">
            Tidak ada data
        </td>
    </tr>
<?php } ?>