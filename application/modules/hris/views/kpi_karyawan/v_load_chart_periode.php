<table class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">KPI</th>
            <th class="text-center">Bobot</th>
            <th class="text-center">AVG Nilai</th>
            <th class="text-center">Jumlah Dinilai</th>
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_periode as $row) { ?>
            <?php 
                $nilai  = array_column($row['data_penilaian'], 'nilai');
                $avg    = count($nilai) > 0 ? array_sum($nilai) / count($nilai) : 0;
            ?>
            <tr>
                <td><?php echo $row['nama_kpi'] ?></td>
                <td class="text-right"><?php echo number_format($row['bobot'], 0) . '%' ?></td>
                <td class="text-center"><?php echo number_format($avg, 2) ?></td>
                <td class="text-center"><?php echo count($row['data_penilaian']) ?> Orang</td>
                <td class="text-center">
                    <button class="btn btn-secondary btn-sm" bobot="<?php echo number_format($row['bobot'], 0) . '%' ?>" index="<?php echo $row['nama_kpi'] ?>" onclick="kpi.detail_chart_periode(this, event)">Detail</button>
                </td>

                <td class="detail" style="display:none;">
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($row['data_penilaian'] as $dt) { ?>
                            <div style="display:flex; flex-direction:row; gap:10px; border-radius:5px; border:1px solid #D6D6D6; padding: 10px;">
                                <div style="display:flex; flex-direction:column; width:40%;">
                                    <label>Nama Karyawan</label>
                                    <span style="font-size:15px;"><?php echo $dt['nama'] ?></span>
                                </div>
    
                                <div style="display:flex; flex-direction:column; width:20%">
                                    <label>Nilai</label>
                                    <span style="font-size:15px;"><?php echo $dt['nilai'] ?></span>
                                </div>

                                <div style="display:flex; flex-direction:column; width:20%">
                                    <label>Score</label>
                                    <span style="font-size:15px;"><?php echo $dt['skor'] ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>