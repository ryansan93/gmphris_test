


            <div style="display:flex; flex-direction:column; gap:10px;">

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Kode Usulan</span>
                    <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo $data_detail['kode']; ?></span>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Tanggal Usulan</span>
                    <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo tglIndonesia($data_detail['tanggal']); ?></span>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Pengusul</span>
                    <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo ucwords(strtolower($data_detail['nama_pengusul'])); ?></span>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Karyawan</span>
                    <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;"><?php echo ucwords(strtolower($data_detail['nama_karyawan'])); ?></span>
                </div>


                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Jenis</span>
                    <div style="padding:10px;border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%;">
                        

                        <table class="table table-bordered">
                            <tr>
                                <th colspan="2" class="text-center"><?php echo ucwords(strtolower($data_detail['jenis'])); ?></th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width:50%;">Jabatan Asal</th>
                                <th class="text-center" style="width:50%;">Jabatan Tujuan</th>
                            </tr>
                            <tr>
                                <td class="text-center"><?php echo $data_detail['nama_jabatan_asal']; ?></td>
                                <td class="text-center"><?php echo $data_detail['nama_jabatan_tujuan']; ?></td>
                            </tr>
                        </table>

                    </div>                                     
                </div>

                

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <span style="width:100px;">Alasan</span>
                    <span style="border: 1px solid #C9C9C9; border-radius:5px; padding:7px; width:100%; height:100px;"><?php echo $data_detail['alasan']; ?></span>
                </div>
                
            </div>
           

      
