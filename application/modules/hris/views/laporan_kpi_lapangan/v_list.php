<fieldset>
    <legend>List Data</legend>
    
        <?php if ($params['jabatan'] == 'ppl' ){?>
            <div style="margin-bottom: 15px; display:flex; flex-direction:row; gap:5px;">
                <div style="position: relative; width: 400px;">                
                    <input oninput="kpi.search_datatable(this)" type="text" id="searchTable" 
                        style="width: 100%; padding: 8px 35px 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box;" 
                        placeholder="Cari nama PPL, Penimbang, atau Wilayah...">
                    <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none;">
                        <i class="fa fa-search"></i>
                    </span>

                </div>
                <button class="btn btn-secondary" jabatan="ppl" onclick="kpi.export_excel(this)"> <i class="fa fa-file-excel-o" aria-hidden="true"></i> | Export Xls</button>
            </div>

            <div style="height:400px; overflow-x:auto; overflow-y:auto;">
                <table class="gmp-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Nama PPL</th>
                            <th class="text-center">Total<br>Populasi</th>
                            <th class="text-center">Jumlah<br>Peternak</th>
                            <th class="text-center">Rata<sup>2</sup> FCR</th>
                            <th class="text-center">Rata<sup>2</sup> IP</th>
                            <th class="text-center">Rata<sup>2</sup> Deplesi</th>
                            <th class="text-center">Rata<sup>2</sup> BB</th>
                            <th class="text-center">Rata<sup>2</sup> <br>Umur Panen</th>
                            <th class="text-center">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $list_data_ppl = $list_data_ppl ?? []; ?>
                        <?php if (!empty($list_data_ppl)): ?>
                            <?php foreach ($list_data_ppl as $wilayah_name => $wilayah_data): ?>

                                <!-- 1. HEADER UNIT (DI ATAS) -->
                                <tr style="font-weight: bold;">
                                    <td style="background-color: #e4f1ff;" colspan="9">📍 <?php echo $wilayah_data['nama_wilayah']; ?></td>
                                </tr>
                                                                        
                                <!-- 2. LIST DATA PPL (DI TENGAH) -->
                                <?php foreach ($wilayah_data['ppl_list'] as $ppl): ?>
                                    <tr>
                                        <td><a href="javascript:void(0)" nama="<?php echo $ppl['nama_ppl'] ?>" jabatan="ppl" onclick="kpi.show_detail_rhpp(this)"><?php echo ucwords(strtolower($ppl['nama_ppl'])) ?></a></td>
                                        <td class="text-center"><?php echo number_format($ppl['total_populasi'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?php echo $ppl['jumlah_peternak'] ?></td>
                                        <td class="text-center"><?php echo number_format($ppl['rata_fcr'], 3, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($ppl['rata_ip'], 3, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($ppl['rata_deplesi'], 3, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($ppl['rata_bb'], 3, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($ppl['rata_umur_panen'], 1, ',', '.') ?></td>
                                        <td class="text-center custom-tooltip">
                                            <?php echo number_format($ppl['persen_kontribusi_populasi'], 2, ',', '.') ?>%
                                            <div class="tooltip-text"><b>Rumus Kontribusi:</b><br>(<?php echo number_format($ppl['total_populasi'], 0, ',', '.'); ?> / <?php echo number_format($wilayah_data['total_populasi_wilayah'], 0, ',', '.'); ?>) x 100%<br>= <span style='color:#f1c40f; font-weight:bold;'><?php echo number_format($ppl['persen_kontribusi_populasi'], 2, ',', '.'); ?>%</span></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- 3. TOTAL / SUMMARY UNIT (DI BAWAH) -->
                                <tr style=" font-weight: bold; border-top: 2px solid #ccc; border-bottom: 4px solid #ddd;">
                                    <td>Total <?php echo ucwords(strtolower($wilayah_data['nama_wilayah'])); ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_populasi_wilayah'], 0, ',', '.') ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_peternak_wilayah'], 0, ',', '.') ?></td>
                                    <!-- Rata-rata tidak dijumlahkan, diberi tanda strip agar tidak misleading -->
                                    <td class="text-center">-</td> 
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <!-- Kontribusi total unit selalu 100% -->
                                    <td class="text-center">100,00%</td> 
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data untuk periode yang dipilih.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php }?>
    
    
        <?php if ($params['jabatan'] == 'penimbang' ){?>
            <div style="margin-bottom: 15px; display:flex; flex-direction:row; gap:5px;">
                <div style="position: relative; width: 400px;">
                    <input oninput="kpi.search_datatable(this)" type="text" id="searchTable" 
                        style="width: 100%; padding: 8px 35px 8px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; box-sizing: border-box;" 
                        placeholder="Cari nama PPL, Penimbang, atau Wilayah...">
                    <span style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none;">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
                <button class="btn btn-secondary" jabatan="penimbang" onclick="kpi.export_excel(this)"> <i class="fa fa-file-excel-o" aria-hidden="true"></i> | Export Xls</button>
            </div>

            <div style="height:400px; overflow-x:scroll">
                <table class="gmp-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Nama Penimbang</th>
                            <th class="text-center">Jumlah<br>Peternak</th>
                            <th class="text-center">Total<br>Populasi</th>
                            <th class="text-center">Total<br>Panen (Ekor)</th>
                            <th class="text-center">Total<br>Panen (Kg)</th>
                            <th class="text-center">Rata2<br>BW Panen</th>
                            <th class="text-center">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $list_data_penimbang = $list_data_penimbang ?? []; ?>
                        <?php if (!empty($list_data_penimbang)): ?>
                            <?php foreach ($list_data_penimbang as $wilayah_name => $wilayah_data): ?>

                                <!-- 1. HEADER UNIT (DI ATAS) -->
                                <tr style=" font-weight: bold;">
                                    <td style="background-color: #e4f1ff;"  colspan="7">📍 <?php echo $wilayah_data['nama_wilayah']; ?></td>
                                </tr>
                                                                        
                                <!-- 2. LIST DATA PENIMBANG (DI TENGAH) -->
                                <?php foreach ($wilayah_data['penimbang_list'] as $penimbang): ?>
                                    <tr>
                                        <td><a href="javascript:void(0)" nama="<?php echo $penimbang['nama_penimbang'] ?>" jabatan="penimbang" onclick="kpi.show_detail_rhpp(this)"> <?php echo ucwords(strtolower($penimbang['nama_penimbang'])) ?></a></td>
                                        <td class="text-center"><?php echo $penimbang['jumlah_peternak'] ?></td>
                                        <td class="text-center"><?php echo number_format($penimbang['total_populasi'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($penimbang['total_panen_ekor'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($penimbang['total_panen_kg'], 3, ',', '.') ?></td>
                                        <td class="text-center"><?php echo number_format($penimbang['rata_bb_panen'], 3, ',', '.') ?></td>
                                        <td class="text-center custom-tooltip">
                                            <?php echo number_format($penimbang['persen_kontribusi_panen'], 2, ',', '.') ?>%
                                            <div class="tooltip-text"><b>Rumus Kontribusi:</b><br>(<?php echo number_format($penimbang['total_panen_ekor'], 0, ',', '.'); ?> / <?php echo number_format($wilayah_data['total_panen_ekor_wilayah'], 0, ',', '.'); ?>) x 100%<br>= <span style='color:#f1c40f; font-weight:bold;'><?php echo number_format($penimbang['persen_kontribusi_panen'], 2, ',', '.'); ?>%</span></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

                                <!-- 3. TOTAL / SUMMARY UNIT (DI BAWAH) -->
                                <tr style=" font-weight: bold; border-top: 2px solid #ccc; border-bottom: 4px solid #ddd;">
                                    <td>Total <?php echo ucwords(strtolower($wilayah_data['nama_wilayah'])); ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_peternak_wilayah'], 0, ',', '.') ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_populasi_wilayah'] ?? 0, 0, ',', '.') ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_panen_ekor_wilayah'], 0, ',', '.') ?></td>
                                    <td class="text-center"><?php echo number_format($wilayah_data['total_panen_kg_wilayah'], 3, ',', '.') ?></td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">100,00%</td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada data untuk periode yang dipilih.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php }?>
      

    </div>
</fieldset>
