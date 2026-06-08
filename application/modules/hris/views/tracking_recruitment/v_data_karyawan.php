<style>
    .employee-card {
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 10px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .employee-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .employee-name {
        font-size: 24px;
        font-weight: 700;
    }

    .employee-nik {
        color: #777;
        font-size: 14px;
    }

    .atasan {
        font-size: 14px;
        color: #444;
    }

    .history-wrapper {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .history-card {
        border-left: 4px solid #000;
        padding: 5px;
        border-radius: 8px;
        background: #fafafa;
        transition: .2s;
    }

    .history-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .jabatan {
        font-size: 14px;
        font-weight: 600;
    }

    .periode {
        color: #666;
        margin-top: 5px;
        font-size: 13px;
    }

    .badge-group {
        margin-top: 12px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .badge-custom {
        background: #ececec;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    .status-active {
        background: #d4f7d4;
        color: #187718;
    }

    .status-nonactive {
        background: #f7a7a7;
        color: #771818;
    }

    .table-detail-karyawan {
        margin-top: 15px;
    }

    .table-detail-karyawan table{
        width: 100%;
        border-collapse: collapse;
    }
    .table-detail-karyawan table tr td, th{
        border: 1px solid #ddd;
        padding: 8px;
        font-size: 13px;
    }

    
</style>

<div class="employee-card">

    <div class="employee-header">
        <div>
            <div class="employee-name"><?php echo $data_karyawan['nama_karyawan'] ?></div>
            <div class="employee-nik">NIK : <?php echo $data_karyawan['nik'] ?></div>
        </div>

        <div class="atasan">
            Atasan : <b><?php echo $data_karyawan['nama_atasan'] ?></b>
        </div>

    </div>

    <div class="table-detail-karyawan">
        <table> 
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Tgl Mulai</th>
                    <th>Tgl Selesai</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data_probation as $dp){ ?>
                    <tr>
                        <td><?php echo $dp['nama_kategori'] ?></td>
                        <td><?php echo tglIndonesia($dp['tgl_berlaku'], "-", " ") ?></td>
                        <td><?php echo !empty($dp['tgl_selesai']) ? tglIndonesia($dp['tgl_selesai'], "-", " ") : '-' ?></td>
                        <td><?php echo $dp['keterangan'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    

    <div class="history-wrapper">

        <?php foreach($karyawan_history as $kh){ ?>
            <div class="history-card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="jabatan">
                        <?php echo $kh['nama_jabatan'] ?>
                    </div>
                    <div class="periode">
                    <?php echo tglIndonesia($kh['tgl_mulai'], "-", " ") ?> - <?php echo !empty($kh['tgl_selesai']) ? tglIndonesia($kh['tgl_selesai'], "-", " ") : 'sekarang'  ?>
                    </div>
                </div>
                <div class="badge-group">
                    <div class="badge-custom">
                        📍 <?php echo $kh['unit']?>
                    </div>
                    <div class="badge-custom">
                        🌎 <?php echo $kh['wilayah']?>
                    </div>
                    <div class="badge-custom status-<?php echo !empty($kh['tgl_selesai']) ? 'nonactive' : 'active' ?>">
                        <?php echo !empty($kh['tgl_selesai']) ? 'NON-AKTIF' : 'AKTIF' ?> 
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

</div>