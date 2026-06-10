<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>FILTER</b>
        </div>
    </legend>
    <div style="display:flex; flex-direction:row; gap:10px;">

        <div style="display:flex; flex-direction:row; width:50%; gap:10px;">
            <label style="width:200px;">Cari data</label>
            <input type="text" class="form form-control pengaju-filter" placeholder="Masukan kata kunci" name="" id="">
        </div>

        <div>
            <button class="btn btn-primary" onclick="tr.filter_data(this, event)"><i class="fa fa-search"
                    style="margin-right: 10px;" aria-hidden="true"></i> Filter</button>
        </div>
    </div>
</fieldset>

<fieldset style="margin-bottom: 15px;">
    <legend>
        <div class="col-xs-12 no-padding">
            <b>LIST DATA</b>
        </div>
    </legend>
    <div class="col-xs-12 no-padding list_data">
        <div class="spinner-load"></div>
    </div>
</fieldset>