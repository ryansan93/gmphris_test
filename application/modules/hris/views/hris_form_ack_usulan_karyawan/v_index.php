<style id="k9d3a1">
    .btn-filter {
        border-radius: 5px;
        border: none;
        padding: 10px;
        flex: 1 1 150px; 
        cursor: pointer;
        font-weight: bold;
    }

    .btn-filter.approve {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.active {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.ack {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }
    .btn-filter.reject {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }

    .btn-filter.done {
        border: 2px solid #151b26;
        color: #151b26;
        background-color:white;
    }

    @media (max-width: 576px) {
        .btn-filter {
            flex: 1 1 100%;
        }
    }
</style>

<fieldset style="margin-bottom: 15px;">
    <legend style="width:50%">
            LIST DATA USULAN KARYAWAN
    </legend>

    <div class="col-xs-12 no-padding list_data" style="overflow-x:scroll" >
        <div class="spinner-load"></div>
    </div>
</fieldset>


