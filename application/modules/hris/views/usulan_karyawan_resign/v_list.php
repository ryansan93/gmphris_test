<style>
    .modern-table-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 14px rgba(15,23,42,0.05);
        overflow: hidden;
        border: 1px solid #f1f3f5;
    }

    .modern-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table thead {
        background: #e0e2e3;
        border-bottom: 2px solid #e9ecef;
    }

    .modern-table thead th {
        padding: 16px 14px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        border: none;
        white-space: nowrap;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f1f3f5;
    }

    .modern-table tbody tr:last-child {
        border-bottom: none;
    }

    .modern-table tbody tr:hover {
        background-color: #f8f9fa;
        /* transform: translateX(2px); */
    }

    .modern-table tbody tr.selected-row {
        background-color: #fff9d6 !important;
        border-left: 4px solid #ffc107;
    }

    .modern-table tbody td {
        padding: 14px;
        font-size: 14px;
        color: #2c3e50;
        border: none;
        vertical-align: middle;
    }

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

<div class="modern-table-container">
    <table class="modern-table">
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
</div>