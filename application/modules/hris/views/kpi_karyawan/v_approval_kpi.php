<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Filter</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">
        <input type="text" class="form form-control" placeholder="Masukan kata kunci" oninput="kpi.filter_approval_kpi(this,event)">
    </div>
</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>Data Approval KPI</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding notifContain">

       <div class="list_approval">

       </div>

    </div>
</fieldset>

<div style="display:flex; flex-direction; justify-content:right; align-items:center; gap:10px;">
    <button class="btn btn-secondary" onclick="window.location.href='hris/KpiKaryawan'">Kembali</button>
</div>