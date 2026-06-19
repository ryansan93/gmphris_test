<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead> 
                <tr> 
                    <th class="text-center">KPI</th>
                    <th class="text-center">Bobot</th>
                    <th class="text-center">Nilai</th>
                    <th class="text-center">Score</th>
                </tr>
            </thead>
            <tbody>
                <?php $total_score = 0; ?>
                <?php foreach($bobot as $b){?>
                    <?php $total_score += $b['skor']; ?>
                    <tr>
                        <td><?php echo $b['nama_kpi'] ?></td>
                        <td class="text-center"><?php echo number_format($b['bobot'], 0); ?>%</td>
                        <td class="text-right"><?php echo $b['nilai'] ?></td>
                        <td class="text-right"><?php echo $b['skor'] ?></td>
                    </tr>
                <?php } ?>
                
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-center">
                        Total Score
                    </th>
                    <th class="text-right"><?php echo number_format($total_score, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="pull-right">
    <button class="btn-detail btn btn-primary" val="1" onclick="kpi.keputusanKpi(this, event)">Approve</button>
    <button class="btn-detail btn btn-danger" val="2" onclick="kpi.keputusanKpi(this, event)">Reject</button>
</div>
