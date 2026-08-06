<style>
    .btn-import {
        border-radius:5px; 
        border:1px solid #C7C7C7; 
        background-color: #ffffff; 
        padding:5px; 
        width:30px; 
        text-align:center;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }
    .btn-import:hover{
        background-color: #666565; 
        color: #ffffff;
        cursor: pointer;
    }
</style>

<div style="border-radius:5px; border:1px solid #C7C7C7; background-color: #EBEBEB; padding:6px; display: flex; justify-content: space-between; align-items:center;">
    <i>Upload here</i>
    <span class="btn-import" onclick="kpi.exec_import_excel_penilaian(this, event)">
        <i class="fa fa-upload" aria-hidden="true"></i>
    </span>
</div>
<br>

<div class="kpi-area">
</div>