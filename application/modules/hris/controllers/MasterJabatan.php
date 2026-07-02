<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MasterJabatan extends Public_Controller {

    private $pathView = 'hris/master_jabatan/';
    private $url;
    private $hakAkses;

    function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        parent::__construct();
        $this->url = $this->current_base_uri;
        $this->hakAkses = hakAkses($this->url);
    }

    public function index($segment=0)
    {

        if ( $this->hakAkses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/master_jabatan/js/master_jabatan.js",
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/master_jabatan/css/master_jabatan.css",
            ));
            $m_conf                     = new \Model\Storage\Conf();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Master Jabatan';
            $content['jabatan_atasan']  = $m_conf->hydrateRaw("select distinct(j.kode), j.* from jabatan_atasan ja inner join jabatan j on ja.kode_jabatan_atasan = j.kode ")->toArray();

            // cetak_r($content['akses'], 1);

            // Load Indexx
            $data['title_menu']     = 'HRIS - Master Jabatan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function getJabatan($kode = null)
    {
    
        $m_conf = new \Model\Storage\Conf();
        $sql    = " SELECT
                        j.kode,
                        j.nama,
                        j.level,
                        j.kode_dokumen,
                        STUFF((
                            SELECT ', ' + ja2.kode_jabatan_atasan
                            FROM jabatan_atasan ja2
                            WHERE ja2.kode_jabatan = j.kode
                            FOR XML PATH(''), TYPE
                        ).value('.', 'NVARCHAR(MAX)'), 1, 2, '') AS kode_jabatan_atasan
                    FROM jabatan j ";

        if (!empty($kode)){
            $sql .= " where j.kode = '" . $kode . "'";
        }

        $sql .= " order by j.level asc, j.nama asc";
        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }


    public function save()
    {

        $params = $_POST;
        // cetak_r($params, 1);
        
        try {
    
            foreach ($params['detail'] as $v_det) {
                $m_jabatan = new \Model\Storage\MasterJabatan_model();
                $m_jabatan->kode            = $v_det['kode_jabatan'];
                $m_jabatan->nama            = $v_det['nama_jabatan'];
                $m_jabatan->level           = $v_det['level'];
                $m_jabatan->kode_dokumen    = $v_det['kode_dokumen'];
                $m_jabatan->save();

                $m_jabatan_atasan = new \Model\Storage\MasterJabatanAtasan_model();
                $m_jabatan_atasan->kode_jabatan         = $v_det['kode_jabatan'];
                $m_jabatan_atasan->kode_jabatan_atasan  = $v_det['jabatan_atasan'];
                $m_jabatan_atasan->save();

                $id            = $m_jabatan->id;
                $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run('base/event/save', $m_jabatan, $deskripsi_log, null, $id, $m_jabatan);
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
            // $this->result['content'] = array('id' => $no_mm);
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );

    }


    public function edit_data()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        $m_conf                     = new \Model\Storage\Conf();

        $data                       = $this->includes;
        $content['jabatan']         = $this->getJabatan($params['kode_jabatan'])[0];
        $content['jabatan_atasan']  = $m_conf->hydrateRaw("select distinct(j.kode), j.* from jabatan_atasan ja inner join jabatan j on ja.kode_jabatan_atasan = j.kode ")->toArray();
        //  cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function update()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        try {
            $kode_jabatan = $params['kode_jabatan'];

            $m_jabatan = new \Model\Storage\MasterJabatan_model();

            $d_jabatan = $m_jabatan->where('kode', $kode_jabatan)->first();
            if (!$d_jabatan) {
                throw new \Exception("Data form tidak ditemukan.");
            }

            $m_jabatan->where('kode', $kode_jabatan)->update([
                'nama'  => $params['nama_jabatan'],
                'level' => $params['level'],
                'kode_dokumen' => $params['kode_dokumen'],
            ]);

            $m_jabatan_atasan = new \Model\Storage\MasterJabatanAtasan_model();

            $m_jabatan_atasan->where('kode_jabatan', $kode_jabatan)->update([
                'kode_jabatan_atasan' => $params['jabatan_atasan'],
            ]);

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $m_jabatan, $deskripsi_log, null, $kode_jabatan, $m_jabatan);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di update.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function delete()
    {
        $params = $_POST;
        $kode_jabatan = $params['kode_jabatan'];
        $m_jabatan = new \Model\Storage\MasterJabatan_model();
        $m_jabatan_atasan = new \Model\Storage\MasterJabatanAtasan_model();

        try {

            $m_jabatan->where('kode', $kode_jabatan)->delete();
            $m_jabatan_atasan->where('kode_jabatan', $kode_jabatan)->delete();

            $deskripsi_log = 'di-hapus oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m_jabatan, $deskripsi_log, null, $kode_jabatan, $m_jabatan);


            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di hapus.';

        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }


    public function load_form()
    {
        $content['akses']           = $this->hakAkses;
        $content['list']            =  $this->getJabatan();
        // cetak_r($content, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function generate_kode(){
        $m_kategori = new \Model\Storage\HrisKategori_model();
        $last = $m_kategori->where('kode_kategori', 'like', 'HRIS/K/%')->orderBy('kode_kategori', 'desc')->first();

        $no = 1;

        if ($last) {
            // ambil angka terakhir (001, 002, dst)
            $last_kode = $last->kode_kategori;
            $explode = explode('/', $last_kode);
            $no = (int)$explode[2] + 1;
        }

        $no_format = str_pad($no, 3, '0', STR_PAD_LEFT);
        $kode_kategori = 'HRIS/K/' . $no_format;

        return $kode_kategori;
    }

}