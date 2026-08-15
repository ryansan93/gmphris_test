<style>

    .gmp-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    .gmp-table thead th {
        background: #f0f4f8;
        color: #5b6b7c;
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .5px;
        font-weight: 700;
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    .gmp-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #eef2f6;
        font-size: .9rem;
        color: #334155;
        vertical-align: middle;
    }

    .gmp-table tbody tr { transition: background .15s; }
    .gmp-table tbody tr:hover td { background: #fafcfe; }
    .gmp-table tbody tr:last-child td { border-bottom: none; }

    .gmp-table .text-center, .gmp-table th.text-center { text-align: center; }
    .gmp-table .text-right,  .gmp-table td.text-right  { text-align: right; }

    .gmp-table-wrap {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        overflow-x: auto;
    }

    /* Garis pemisah antar kolom */
    .gmp-table thead th + th { border-left: 1px solid #e2e8f0; }
    .gmp-table tbody td + td { border-left: 1px solid #eef2f6; }


    .employee-name {
        font-weight: 600;
        color: #1a2332;
    }

    .employee-position {
        color: #6b7a85;
        font-size: 13px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-draft {
        background: #fff7e6;
        color: #a66b00;
        border: 1px solid #ffe4b3;
    }

    .status-ack {
        background: #e8f1ff;
        color: #0b539f;
        border: 1px solid #b3d4ff;
    }

    .status-approved {
        background: #e6f7ec;
        color: #0b6b2f;
        border: 1px solid #b3e6c7;
    }

    .status-reject {
        background: #fff0f0;
        color: #c23b3b;
        border: 1px solid #ffcccc;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .action-buttons .btn {
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #8a96a3;
    }

    .empty-state i {
        font-size: 48px;
        opacity: 0.3;
        margin-bottom: 12px;
    }

    @media (max-width: 768px) {
        .modern-table-container {
            overflow-x: auto;
        }

        .modern-table {
            min-width: 800px;
        }

        .modern-table thead th,
        .modern-table tbody td {
            padding: 10px 8px;
            font-size: 12px;
        }
    }
</style>


<table class="gmp-table">
    <thead>
        <tr>
            <th class="text-center">No. Document</th>
            <th class="text-center">NIK</th>
            <th>Nama Karyawan</th>
            <th>Jabatan</th>
            <th class="text-center">Tanggal Pengajuan</th>
            <th class="text-center">Tanggal Efektif</th>
            <th class="text-center">Status</th>
            <th class="text-center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($list)) { ?>
            
            <?php foreach($list as $l){?>
                <tr class="<?php echo !empty($l['selected']) ? 'selected-row' : ''; ?>"> 
                    <td class="text-center">
                        <a href="javascript:void(0)" id_data="<?php echo $l['id'] ?>" style="color: #1f75fe;" onclick="ukr.showDetailUsulan(this, event)"><?php echo $l['document'] ?></a>
                    </td>
                    <td class="text-center">
                        <span style="font-family: monospace; background: #f1f3f5; padding: 4px 8px; border-radius: 4px;">
                            <?php echo $l['nik'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="employee-name">
                            <?php echo ucwords(strtolower($l['nama_karyawan'])) ?>
                        </div>
                    </td>
                    <td>
                        <div class="employee-position">
                            <?php echo $l['nama_jabatan'] ?>
                        </div>
                    </td>
                    <td class="text-center">
                        <?php echo tglIndonesia($l['tanggal_pengajuan'], '-', ' ') ?>
                    </td>
                    <td class="text-center">
                        <?php echo tglIndonesia($l['tanggal_resign'], '-', ' ') ?>
                    </td>

                    <?php
                        $status_map = [
                            1 => ['class' => 'status-draft', 'label' => 'Draft', 'icon' => '🟡'],
                            2 => ['class' => 'status-ack', 'label' => 'Acknowledge', 'icon' => '🔵'],
                            3 => ['class' => 'status-approved', 'label' => 'Approved', 'icon' => '🟢'],
                            4 => ['class' => 'status-reject', 'label' => 'Reject HRD', 'icon' => '🔴'],
                            5 => ['class' => 'status-reject', 'label' => 'Reject CEO', 'icon' => '🔴'],
                        ];
                        $status_info = $status_map[$l['status']] ?? ['class' => '', 'label' => '-', 'icon' => ''];
                    ?>
                    <td class="text-center">
                        <span class="status-badge <?php echo $status_info['class']; ?>">
                            <span><?php echo $status_info['icon']; ?></span>
                            <span><?php echo $status_info['label']; ?></span>
                        </span>
                    </td>
                    <td>
                        <?php if ($l['status'] == 1 ) { ?>
                            <div class="action-buttons">
                                <button id_data="<?php echo $l['id'] ?>" onclick="ukr.edit_data(this, event)" class="btn btn-warning" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button id_data="<?php echo $l['id'] ?>" onclick="ukr.delete(this, event)" class="btn btn-danger" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>

        <?php } else { ?>
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fa fa-inbox"></i>
                        <div>Tidak ada data</div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
