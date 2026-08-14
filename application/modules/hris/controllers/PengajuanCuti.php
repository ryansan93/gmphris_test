<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class PengajuanCuti extends Public_Controller
{
	private $url;
	private $pathView = 'hris/hris_pengajuan_cuti/';

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
		$this->load->library('telegram_lib');
	}

	public function index()
	{
        $akses = hakAkses($this->url);
        // cetak_r($akses, 1)
		if ( $akses['a_view'] == 1 ) {

            $this->add_external_js(array(
                "assets/select2/js/select2.min.js",
                "assets/moments/moment.js",
                "assets/toastr/js/toastr.js",
                "assets/toastr/js/toastr.min.js",
                "assets/bootbox_old/js/bootbox.js",
                "assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
                "assets/hris/pengajuan_cuti/js/pengajuan_cuti.js",
                "assets/xlsx/js/xlsx.full.min.js"
            ));
            $this->add_external_css(array(
                'assets/select2/css/select2.min.css',
                'assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.old.css',
                "assets/toastr/css/toastr.css",
                "assets/toastr/css/toastr.min.css",
                'assets/hris/pengajuan_cuti/css/pengajuan_cuti.css'
            ));
            $data 				= $this->includes;


            $content = [];

            $m_karyawan 			= new \Model\Storage\Karyawan_model();
            $d_karyawan = $m_karyawan->select(
                'karyawan.*',
                'jabatan.nama as nama_jabatan',
                'hris_master_cuti.hak_cuti',
                'hris_master_cuti.sisa_cuti',
                'hris_master_cuti.cuti_terpakai'
            )
            ->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
            ->leftJoin('hris_master_cuti', 'hris_master_cuti.nik', '=', 'karyawan.nik')
            ->where('karyawan.status', 1)
            ->orderBy('karyawan.level', 'asc')
            ->get();

            $data_karyawan = $d_karyawan->toArray();
            
            $content['karyawan']	= $data_karyawan;
            $content['title_menu']  = 'Pengajuan Cuti';
            $content['akses']       = $akses;
            $content['nik_login']   = $this->cek_nik();
            $content['list']        = $this->getPengajuan();

            // cetak_r($content['nik_login'], 1);
            
            $data['view'] 		= $this->load->view($this->pathView. 'v_index', $content, true);       
			$this->load->view($this->template, $data);
		} else {
			showErrorAkses();
		}
	}


    public function cek_nik()
	{
		$m_conf = new \Model\Storage\Conf();

		$sql = "
			SELECT 
				du.id_user,
				k.nik
			FROM detail_user du
			INNER JOIN karyawan k
				ON du.nama_detuser = k.nama
				AND k.status = 1
			WHERE du.id_user = '" . $_SESSION['id_user'] . "'
			AND du.nonaktif_detuser IS NULL
		";

		$d_conf = $m_conf->hydrateRaw($sql);

		if ($d_conf->count() == 0) {
			return null;
		}

		$data = $d_conf->toArray();

		if ($_SESSION['detail_user']['data_group']['nama_group'] == 'HRD') {
			return null;
		}

		return $data[0]['nik'];
	}


    public function getPengajuan($id = null)
    {

        $m_conf = new \Model\Storage\Conf();
        $sql    = " select hpc.id, k.nik, k.nama as nama_karyawan, hpc.status_pengajuan, hpc.tanggal_mulai, hpc.tanggal_selesai, hpc.jenis_cuti, hpc.alasan, j.nama as nama_jabatan, hpc.keterangan_reject,
                    hpc.jumlah_hari
					from hris_pengajuan_cuti hpc
					inner join karyawan k on hpc.nik = k.nik and k.status = 1
                    inner join jabatan j on k.jabatan = j.kode
                    --inner join hris_master_cuti hmc on hmc.nik = k.nik and hmc.tahun = year(hpc.tanggal_mulai) and hmc.status = 1
                     ";

        if (!empty($id)){
            $sql .= " where hpc.id = '" . $id . "'";
        }

        $nik_login = $this->cek_nik();

        if(isset($nik_login) && $nik_login){
                $sql .= " and hpc.nik = '". $nik_login ."' ";
        }

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function getLaporanPengajuan()
    {
        $m_conf = new \Model\Storage\Conf();
        $sql    = " select hpc.id, 
                        k.nik, k.nama as nama_karyawan, 
                        hpc.status_pengajuan, 
                        hpc.tanggal_mulai, 
                        hpc.tanggal_selesai, 
                        hpc.jenis_cuti, 
                        hpc.alasan, 
                        j.nama as nama_jabatan, 
                        hpc.keterangan_reject,
                        hpc.jumlah_hari,
                        hpc.ack_by,
                        hpc.ack_date,
                        hpc.approve_by,
                        hpc.approve_date,
                        hpc.revert_note,
                        hpc.updated_at,
                        hpc.edit_note
					from hris_pengajuan_cuti hpc
					inner join karyawan k on hpc.nik = k.nik and k.status = 1
                    inner join jabatan j on k.jabatan = j.kode ";

        $nik_login = $this->cek_nik();
        
        if(isset($nik_login) && $nik_login){
            $sql .= " where k.atasan_nik = '" . $nik_login . "' ";
        } 

          $sql .= " order by hpc.id desc ";

        // if (!empty($id)){
        //     $sql .= " where hpc.id = '" . $id . "'";
        // }

        $d_conf = $m_conf->hydrateRaw( $sql );
        $data   = null;

        if ( $d_conf->count() > 0 ) {
            $data = $d_conf->toArray();
        }

        return $data;
    }

    public function getAttachments($pengajuan_id = null)
    {
        if (empty($pengajuan_id)) return [];

        $m = new \Model\Storage\HrisAttachmentCuti_model();
        $d = $m->where('pengajuan_id', $pengajuan_id)->get();
        if ($d) return $d->toArray();
        return [];
    }

    public function load_form()
    {
		// cetak_r($content, 1);
        $content['list'] = $this->getPengajuan();
        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function edit_data()
    {
        $params = $_POST;
        $content = [];
        if (!empty($params['id'])){
            $data = $this->getPengajuan($params['id']);
            $content['data'] = $data[0];
            $content['attachments'] = $this->getAttachments($params['id']);
        }

		$m_karyawan 			= new \Model\Storage\Karyawan_model();
        $d_karyawan             = $m_karyawan->select(
                                'karyawan.*',
                                'jabatan.nama as nama_jabatan'
                            )
                            ->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
                            ->where('karyawan.status', 1)
                            ->orderBy('karyawan.level', 'asc')
                            ->get();

		$data_karyawan  		= $d_karyawan->toArray();
		$content['karyawan']	= $data_karyawan;
        $content['nik_login']   = $this->cek_nik();
        // cetak_r($content, 1);


        // $content['nik_login'] = $this->cek_nik();
        echo $this->load->view($this->pathView . 'v_edit_data', $content, TRUE);
    }

    public function save()
    {
        $params = $_POST;
        // cetak_r($params, 1);

        try {
            $tanggal_mulai = null;
            $tanggal_selesai = null;
            if (!empty($params['tanggal_mulai'])){
                $dtMulai = \DateTime::createFromFormat('d m Y', $params['tanggal_mulai']);
                if (!$dtMulai) throw new \Exception('Format tanggal mulai salah. Gunakan DD MM YYYY.');
                $tanggal_mulai = $dtMulai->format('Y-m-d');
            }
            if (!empty($params['tanggal_selesai'])){
                $dtSelesai = \DateTime::createFromFormat('d m Y', $params['tanggal_selesai']);
                if (!$dtSelesai) throw new \Exception('Format tanggal selesai salah. Gunakan DD MM YYYY.');
                $tanggal_selesai = $dtSelesai->format('Y-m-d');
            }
            if (!empty($tanggal_mulai) && !empty($tanggal_selesai) && strtotime($tanggal_selesai) < strtotime($tanggal_mulai)){
                $this->result['status'] = 0;
                $this->result['message'] = 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.';
                display_json($this->result);
                return;
            }

            $nik  = isset($params['nik']) ? $params['nik'] : null;
            $jenis = isset($params['jenis_cuti']) ? $params['jenis_cuti'] : null;

            $pengajuanModel = new \Model\Storage\PengajuanCuti_model();


            // hanya untuk pengajuan baru
            if (empty($params['id']) && !empty($nik)) {

                // 1. Cek bentrok tanggal Berlaku untuk semua jenis cuti
                $bentrok = $pengajuanModel
                    ->where('nik', $nik)
                    ->whereNotIn('status_pengajuan', [4,5])
                    ->where(function($q) use ($tanggal_mulai, $tanggal_selesai){

                        $q->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                        ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                        ->orWhere(function($q2) use ($tanggal_mulai, $tanggal_selesai){

                                $q2->where('tanggal_mulai', '<=', $tanggal_mulai)
                                ->where('tanggal_selesai', '>=', $tanggal_selesai);

                        });

                    })
                    ->first();

                if ($bentrok) {

                    $this->result['status'] = 0;
                    $this->result['message'] = 
                        'Tanggal pengajuan cuti bentrok dengan pengajuan sebelumnya (' .
                        date('d M Y', strtotime($bentrok->tanggal_mulai)) .
                        ' - ' .
                        date('d M Y', strtotime($bentrok->tanggal_selesai)) .
                        ').';

                    display_json($this->result);
                    return;
                }



                // 2. Cek urutan tanggal terakhir Tidak berlaku untuk cuti sakit dan force majeure
                if (!in_array($jenis, ['cuti_sakit','cuti_force_majeure'])) {
                    $lastPengajuan = $pengajuanModel
                        ->selectRaw('MAX(tanggal_selesai) as last_selesai')
                        ->where('nik', $nik)
                        ->whereNotIn('status_pengajuan', [4,5])
                        ->first();

                    if ($lastPengajuan && !empty($lastPengajuan->last_selesai)) {
                        $lastSelesai = $lastPengajuan->last_selesai;
                        if (strtotime($tanggal_mulai) <= strtotime($lastSelesai)) {
                            $this->result['status'] = 0;
                            $this->result['message'] =
                                'Tanggal mulai pengajuan baru harus lebih besar dari tanggal selesai pengajuan lama (' .
                                date('d M Y', strtotime($lastSelesai)) .
                                ').';
                            display_json($this->result);
                            return;
                        }
                    }
                }

                
                // 3. Cek tanggal tidak boleh sebelum hari ini Tidak berlaku untuk sakit dan force majeure
                if (!empty($tanggal_mulai) && !in_array($jenis, ['cuti_sakit','cuti_force_majeure']) ) {
                    $today = date('Y-m-d');
                    if (strtotime($tanggal_mulai) < strtotime($today)) {
                        $this->result['status'] = 0;
                        $this->result['message'] = 'Tanggal mulai tidak boleh sebelum hari ini untuk jenis cuti selain Cuti Sakit/Force Majeure.';
                        display_json($this->result);
                        return;
                    }
                }

            }

            // cek master cuti aktif di tahun berjalan
            $year = date('Y');
            $nik = isset($params['nik']) ? $params['nik'] : null;
            if (empty($nik)) {
                $this->result['status'] = 0;
                $this->result['message'] = 'NIK tidak boleh kosong.';
                display_json($this->result);
                return;
            }
            // $m_master = new \Model\Storage\Conf();
            // $master_cuti = $m_master->hydrateRaw(
            //     "select * from hris_master_cuti where nik = ? and tahun = ? and status = 1",
            //     [$nik, $year]
            // )->first();
            // if (!$master_cuti) {
                // $this->result['status'] = 0;
                // $this->result['message'] = 'Pengajuan tidak dapat diproses. Tidak ditemukan data master cuti aktif untuk NIK tersebut pada tahun berjalan.';
                // display_json($this->result);
                // return;
            // }

            // wajib attachment hanya untuk Cuti Sakit atau Force Majeure
            $hasFile = false;
            if (!empty($_FILES['attachment'])){
                for ($i=0;$i<count($_FILES['attachment']['name']);$i++){
                    if (!empty($_FILES['attachment']['name'][$i])){ $hasFile = true; break; }
                }
            }
            if (in_array($jenis, ['cuti_sakit','cuti_force_majeure']) && !$hasFile){
                $this->result['status'] = 0;
                $this->result['message'] = 'Attachment wajib diunggah untuk Cuti Sakit atau Force Majeure.';
                display_json($this->result);
                return;
            }
            $m = new \Model\Storage\PengajuanCuti_model();
            $m->nik                 = $params['nik'];
            $m->tanggal_mulai       = $tanggal_mulai;
            $m->tanggal_selesai     = $tanggal_selesai;
            $m->jenis_cuti          = $params['jenis_cuti'];
            $m->alasan              = $params['alasan'];
			$m->status_pengajuan    = 1;

            // hitung jumlah_hari tanpa menghitung hari Minggu
            $jumlah_hari = 0;
            try {
                if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
                    $start = new \DateTime($tanggal_mulai);
                    $end = new \DateTime($tanggal_selesai);
                    if ($end >= $start) {
                        $cur = clone $start;
                        while ($cur <= $end) {
                            if ($cur->format('w') != 0) { // bukan Minggu
                                $jumlah_hari++;
                            }
                            $cur->modify('+1 day');
                        }
                    }
                } elseif (!empty($tanggal_mulai)) {
                    $d = new \DateTime($tanggal_mulai);
                    $jumlah_hari = ($d->format('w') != 0) ? 1 : 0;
                }
            } catch (\Exception $ex) {
                $jumlah_hari = 0;
            }

            $m->jumlah_hari = $jumlah_hari;
		    $m->save();

            // handle attachments
            if (!empty($_FILES['attachment'])){
                $files = $_FILES['attachment'];
                $upload_path = 'uploads/hris_cuti/' . $m->id . '/';
                if (!is_dir(FCPATH . $upload_path)){
                    mkdir(FCPATH . $upload_path, 0755, true);
                }

                for( $i=0; $i<count($files['name']); $i++){
                    if (empty($files['name'][$i])) continue;
                    $original = $files['name'][$i];
                    $tmp = $files['tmp_name'][$i];
                    $ext = pathinfo($original, PATHINFO_EXTENSION);
                    $encName = md5(uniqid() . $original) . '.' . $ext;
                    $target = $upload_path . $encName;
                    if (move_uploaded_file($tmp, FCPATH . $target)){
                        $m_att = new \Model\Storage\HrisAttachmentCuti_model();
                        $m_att->pengajuan_id = $m->id;
                        $m_att->file_attachment = $encName; 
                        $m_att->nama_file = $original; 
                        $m_att->file_path = $target;
                        $m_att->uploaded_by = isset($this->userdata['detail_user']['nama_detuser']) ? $this->userdata['detail_user']['nama_detuser'] : null;
                        $m_att->uploaded_at = date('Y-m-d H:i:s');
                        $m_att->save();
                    }
                }
            }

            $deskripsi_log = 'di-submit oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/save', $m, $deskripsi_log, null, $m->id, $m);

            $message_telegram =
            "📢 *Pengajuan Cuti *\n\n" .
            " - Pengaju : {$params['nik']}\n" .
            " - Tanggal Mulai : {$params['tanggal_mulai']}\n" .
            " - Tanggal Selesai : {$params['tanggal_selesai']}\n" .
            " - Alasan : {$params['jenis_cuti']} - {$params['alasan']}\n\n" .
            " - Di input oleh : {$_SESSION['detail_user']['nama_detuser']} ({$_SESSION['id_user']})";
            $this->telegram_lib->sendMessages($message_telegram);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data berhasil di simpan.';
        } catch (Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json( $this->result );
    }

    public function update()
    {
        $params = $_POST;

        // cetak_r($params, 1);

        try {
            $tanggal_mulai = null;
            $tanggal_selesai = null;
            if (!empty($params['tanggal_mulai'])){
                $dtMulai = \DateTime::createFromFormat('d m Y', $params['tanggal_mulai']);
                if (!$dtMulai) throw new \Exception('Format tanggal mulai salah. Gunakan DD MM YYYY.');
                $tanggal_mulai = $dtMulai->format('Y-m-d');
            }
            if (!empty($params['tanggal_selesai'])){
                $dtSelesai = \DateTime::createFromFormat('d m Y', $params['tanggal_selesai']);
                if (!$dtSelesai) throw new \Exception('Format tanggal selesai salah. Gunakan DD MM YYYY.');
                $tanggal_selesai = $dtSelesai->format('Y-m-d');
            }
            if (!empty($tanggal_mulai) && !empty($tanggal_selesai) && strtotime($tanggal_selesai) < strtotime($tanggal_mulai)){
                $this->result['status'] = 0;
                $this->result['message'] = 'Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.';
                display_json($this->result);
                return;
            }
            
            $nik  = isset($params['nik']) ? $params['nik'] : null;
            $jenis = isset($params['jenis_cuti']) ? $params['jenis_cuti'] : null;

            $pengajuanModel = new \Model\Storage\PengajuanCuti_model();

            if (!empty($params['nik'])) {

                // 1. Cek bentrok tanggal Berlaku untuk semua jenis cuti
          
                    $bentrok = $pengajuanModel
                        ->where('nik', $nik)
                        ->whereNotIn('status_pengajuan', [4,5]);

                    if (!empty($params['id'])) {
                        $bentrok = $bentrok->where('id', '!=', $params['id']);
                    }

                    $bentrok = $bentrok
                        ->where(function($q) use ($tanggal_mulai, $tanggal_selesai){
                            $q->whereBetween('tanggal_mulai', [$tanggal_mulai, $tanggal_selesai])
                            ->orWhereBetween('tanggal_selesai', [$tanggal_mulai, $tanggal_selesai])
                            ->orWhere(function($q2) use ($tanggal_mulai, $tanggal_selesai){
                                    $q2->where('tanggal_mulai', '<=', $tanggal_mulai)
                                    ->where('tanggal_selesai', '>=', $tanggal_selesai);
                            });
                        })
                        ->first();


                    if ($bentrok) {

                        $this->result['status'] = 0;
                        $this->result['message'] = 
                            'Tanggal pengajuan cuti bentrok dengan pengajuan sebelumnya (' .
                            date('d M Y', strtotime($bentrok->tanggal_mulai)) .
                            ' - ' .
                            date('d M Y', strtotime($bentrok->tanggal_selesai)) .
                            ').';

                        display_json($this->result);
                        return;
                    }
        

                // 2. Cek urutan tanggal terakhir Tidak berlaku untuk cuti sakit dan force majeure
                if (!in_array($jenis, ['cuti_sakit','cuti_force_majeure'])) {

                    $lastPengajuan = $pengajuanModel
                        ->selectRaw('MAX(tanggal_selesai) as last_selesai')
                        ->where('nik', $nik)
                        ->whereNotIn('status_pengajuan', [4,5]);

                    // jika update, jangan ambil data sendiri
                    if (!empty($params['id'])) {
                        $lastPengajuan = $lastPengajuan
                            ->where('id', '!=', $params['id']);
                    }

                    $lastPengajuan = $lastPengajuan->first();


                    if ($lastPengajuan && !empty($lastPengajuan->last_selesai)) {

                        $lastSelesai = $lastPengajuan->last_selesai;

                        if (strtotime($tanggal_mulai) <= strtotime($lastSelesai)) {

                            $this->result['status'] = 0;
                            $this->result['message'] =
                                'Tanggal mulai pengajuan baru harus lebih besar dari tanggal selesai pengajuan lama (' .
                                date('d M Y', strtotime($lastSelesai)) .
                                ').';

                            display_json($this->result);
                            return;
                        }
                    }
                }

                
                // 3. Cek tanggal tidak boleh sebelum hari ini Tidak berlaku untuk sakit dan force majeure
                // if (!empty($tanggal_mulai) && !in_array($jenis, ['cuti_sakit','cuti_force_majeure']) ) {
                //     $today = date('Y-m-d');
                //     if (strtotime($tanggal_mulai) < strtotime($today)) {
                //         $this->result['status'] = 0;
                //         $this->result['message'] = 'Tanggal mulai tidak boleh sebelum hari ini untuk jenis cuti selain Cuti Sakit/Force Majeure.';
                //         display_json($this->result);
                //         return;
                //     }
                // }
                


            }

            $id = $params['id'];
            $m = new \Model\Storage\PengajuanCuti_model();
            $d = $m->where('id', $id)->first();
            if (!$d) throw new \Exception('Data tidak ditemukan');

            $jumlah_hari = 0;
            try {
                if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
                    $start = new \DateTime($tanggal_mulai);
                    $end = new \DateTime($tanggal_selesai);
                    if ($end >= $start) {
                        $cur = clone $start;
                        while ($cur <= $end) {
                            if ($cur->format('w') != 0) { // bukan Minggu
                                $jumlah_hari++;
                            }
                            $cur->modify('+1 day');
                        }
                    }
                } elseif (!empty($tanggal_mulai)) {
                    $d = new \DateTime($tanggal_mulai);
                    $jumlah_hari = ($d->format('w') != 0) ? 1 : 0;
                }
            } catch (\Exception $ex) {
                $jumlah_hari = 0;
            }

            // $m->jumlah_hari = $jumlah_hari;

            $m->where('id', $id)->update([
                'nik'               => $nik,
                'tanggal_mulai'     => $tanggal_mulai,
                'tanggal_selesai'   => $tanggal_selesai,
                'jenis_cuti'        => $params['jenis_cuti'],
                'alasan'            => $params['alasan'],
                'jumlah_hari'       => $jumlah_hari,
                'edit_note'         => $params['edit_note'] ?? null,
                'updated_at'        => $params['edit_note'] ? date('Y-m-d H:i:s') : null,
            ]);

            // handle attachments on update
            // hitung existing attachments minus yang dihapus + file baru
            $m_att = new \Model\Storage\HrisAttachmentCuti_model();
            $existing = $m_att->where('pengajuan_id', $id)->get();
            $existingCount = ($existing && $existing->count()>0) ? $existing->count() : 0;
            $deletedIds = isset($params['deleted_attachment_ids']) ? $params['deleted_attachment_ids'] : [];
            $deletedCount = is_array($deletedIds) ? count($deletedIds) : 0;
            // ensure deletedIds is array
            if (!is_array($deletedIds) && !empty($deletedIds)){
                $deletedIds = [$deletedIds];
            }
            $newFileCount = 0;
            if (!empty($_FILES['attachment'])){
                for($i=0;$i<count($_FILES['attachment']['name']);$i++){
                    if (!empty($_FILES['attachment']['name'][$i])) $newFileCount++;
                }
            }
            $finalCount = ($existingCount - $deletedCount) + $newFileCount;
            if (in_array($jenis, ['cuti_sakit','cuti_force_majeure']) && $finalCount <= 0){
                $this->result['status'] = 0;
                $this->result['message'] = 'Attachment wajib ada untuk Cuti Sakit atau Force Majeure.';
                display_json($this->result);
                return;
            }

            // proses penghapusan attachment yang ditandai
            if (!empty($deletedIds) && is_array($deletedIds)){
                $m_att_proc = new \Model\Storage\HrisAttachmentCuti_model();
                foreach($deletedIds as $delId){
                    try {
                        $att = $m_att_proc->where('id', $delId)->first();
                        if ($att){
                            $path = FCPATH . $att->file_path;
                            if (file_exists($path)) @unlink($path);
                            $m_att_proc->where('id', $delId)->delete();
                        }
                    } catch (\Exception $ex){
                        // continue even if one delete fails
                    }
                }
            }

            if (!empty($_FILES['attachment'])){
                $files = $_FILES['attachment'];
                $upload_path = 'uploads/hris_cuti/' . $id . '/';
                if (!is_dir(FCPATH . $upload_path)){
                    mkdir(FCPATH . $upload_path, 0755, true);
                }
                for($i=0; $i<count($files['name']); $i++){
                    if (empty($files['name'][$i])) continue;
                    $original = $files['name'][$i];
                    $tmp = $files['tmp_name'][$i];
                    $ext = pathinfo($original, PATHINFO_EXTENSION);
                    $encName = md5(uniqid() . $original) . '.' . $ext;
                    $target = $upload_path . $encName;
                    if (move_uploaded_file($tmp, FCPATH . $target)){
                        $m_att = new \Model\Storage\HrisAttachmentCuti_model();
                        $m_att->pengajuan_id = $id;
                        $m_att->file_attachment = $encName;
                        $m_att->nama_file = $original;
                        $m_att->file_path = $target;
                        $m_att->uploaded_by = isset($this->userdata['detail_user']['nama_detuser']) ? $this->userdata['detail_user']['nama_detuser'] : null;
                        $m_att->uploaded_at = date('Y-m-d H:i:s');
                        $m_att->save();
                    }
                }
            }

            $deskripsi_log = 'di-update oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/update', $m, $deskripsi_log, null, $id, $m);

            $this->result['status']  = 1;
            $this->result['message'] = 'Data berhasil di update.';
        } catch (\Exception $e) {
            $this->result['status']  = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete()
    {
        $params = $_POST;
        $id = $params['id'];
        $m = new \Model\Storage\PengajuanCuti_model();
        try {
            
            $m_att          = new \Model\Storage\HrisAttachmentCuti_model();
            $attachments    = $m_att->where('pengajuan_id', $id)->get();

            if ($attachments && $attachments->count() > 0){
                foreach($attachments as $att){
                    try {
                        $path = FCPATH . $att->file_path;
                        if (file_exists($path)) @unlink($path);
                        
                        $m_att->where('id', $att->id)->delete();
                    } catch (\Exception $ex) {
                        // continue deleting other files even if one fails
                    }
                }
                
                $upload_dir = FCPATH . 'uploads/hris_cuti/' . $id . '/';
                if (is_dir($upload_dir)) {
                    @rmdir($upload_dir);
                }
            }

            // delete pengajuan record
            $m->where('id', $id)->delete();

            $deskripsi_log = 'di-hapus beserta attachment oleh ' . $this->userdata['detail_user']['nama_detuser'];
            Modules::run('base/event/delete', $m, $deskripsi_log, null, $id, $m);

            $this->result['status'] = 1;
            $this->result['message'] = 'Data dan file attachment berhasil di hapus.';
        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function delete_attachment()
    {
        $params = $_POST;
        $id = isset($params['id']) ? $params['id'] : null;
        $this->result = ['status' => 0, 'message' => 'Gagal menghapus attachment.'];
        if (empty($id)){
            display_json($this->result);
            return;
        }

        $m = new \Model\Storage\HrisAttachmentCuti_model();
        $d = $m->where('id', $id)->first();
        if (!$d){
            $this->result['message'] = 'Attachment tidak ditemukan.';
            display_json($this->result);
            return;
        }

        try {
            $path = FCPATH . $d->file_path;
            if (file_exists($path)) @unlink($path);
            $m->where('id', $id)->delete();
            $this->result['status'] = 1;
            $this->result['message'] = 'Attachment berhasil dihapus.';
        } catch (\Exception $e) {
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function change_status()
    {
        $params = $_POST;

        // cetak_r($params, 1);
        $this->result = ['status' => 0, 'message' => 'Parameter tidak lengkap.'];
        if (empty($params['id']) || empty($params['status'])){
            display_json($this->result);
            return;
        }

        $id = $params['id'];
        $status_code = (int) $params['status'];
        $note = isset($params['note']) ? $params['note'] : null;

        $map = [
            1 => 'DRAFT',
            2 => 'ACKNOWLEDGE',
            3 => 'APPROVED',
            4 => 'REJECT ATASAN',
            5 => 'REJECT HRD'
        ];

        if (!isset($map[$status_code])){
            $this->result['message'] = 'Status tidak valid.';
            display_json($this->result);
            return;
        }

        $status_label = $map[$status_code];

        if (in_array($status_code, [4, 5]) && empty(trim($note))) {
            $this->result['message'] = 'Keterangan reject wajib diisi untuk status reject.';
            display_json($this->result);
            return;
        }

        $reject_note = null;
        if (in_array($status_code, [4, 5])) {
            $reject_note = trim($note);
        }

        try {
            $m = new \Model\Storage\PengajuanCuti_model();
            $d = $m->where('id', $id)->first();
            if (!$d) throw new \Exception('Data tidak ditemukan');

            $update = [
                'status_pengajuan' => $status_code,
                'keterangan_reject' => $reject_note
            ];

            // set ack/approve metadata depending on status code
            if (in_array($status_code, [2, 4])) {
                $update['ack_by'] = isset($this->userdata['detail_user']['nama_detuser']) ? $this->userdata['detail_user']['nama_detuser'] : null;
                $update['ack_date'] = date('Y-m-d H:i:s');
            }
            if (in_array($status_code, [3, 5])) {
                $update['approve_by'] = isset($this->userdata['detail_user']['nama_detuser']) ? $this->userdata['detail_user']['nama_detuser'] : null;
                $update['approve_date'] = date('Y-m-d H:i:s');
            }

            $m->where('id', $id)->update($update);

            if ($status_code === 3) {
                $previous_status = isset($d->status_pengajuan) ? (int) $d->status_pengajuan : null;
                $jenis_val = strtolower(trim((string) $d->jenis_cuti));

                switch ($jenis_val) {
                    case '1':
                        $jenis_val = 'cuti';
                        break;
                    case '2':
                        $jenis_val = 'cuti_force_majeure';
                        break;
                    case '3':
                        $jenis_val = 'cuti_sakit';
                        break;
                    case '4':
                        $jenis_val = 'cuti_jatah_liburan';
                        break;
                }

          

                // ambil jatah master cuti
                // if ($status_code === 3 && empty($d->cuti_dipotong) && in_array($jenis_val, ['cuti', 'cuti_force_majeure', 'cuti_sakit'])) {
                //     $tanggal_mulai = !empty($d->tanggal_mulai) ? substr($d->tanggal_mulai, 0, 10) : null;
                //     $tanggal_selesai = !empty($d->tanggal_selesai) ? substr($d->tanggal_selesai, 0, 10) : null;
                //     if ($tanggal_mulai && $tanggal_selesai) {
                //         $start = new \DateTime($tanggal_mulai);
                //         $end = new \DateTime($tanggal_selesai);
                //         if ($end < $start) {
                //             throw new \Exception('Tanggal selesai tidak valid saat approval.');
                //         }
                        
                //         $days = null;
                //         if (isset($d->jumlah_hari)) {
                //             $days = (int) $d->jumlah_hari;
                //         } elseif (is_array($d) && isset($d['jumlah_hari'])) {
                //             $days = (int) $d['jumlah_hari'];
                //         }
                //         if ($days === null) {
                //             $days = 0;
                //             $cur = clone $start;
                //             while ($cur <= $end) {
                //                 if ($cur->format('w') != 0) { // bukan hari Minggu
                //                     $days++;
                //                 }
                //                 $cur->modify('+1 day');
                //             }
                //         }
                //         $year = (int) $start->format('Y');

                //         $m_master = new \Model\Storage\Conf();
                //         $master = $m_master->hydrateRaw("select * from hris_master_cuti where nik = ? and tahun = ?", [$d->nik, $year])->first();

                       
                       
                //         if (!$master) {
                //             throw new \Exception('Data master cuti tidak ditemukan untuk nik ' . $d->nik . ' tahun ' . $year . '.');
                //         }
                        

                //         // ambil nilai sisa_cuti dengan aman (bisa array atau object)
                //         $sisa_cuti = null;
                //         if (is_array($master)) {
                //             $sisa_cuti = isset($master['sisa_cuti']) ? (int) $master['sisa_cuti'] : null;
                //         } elseif (is_object($master)) {
                //             if (isset($master->sisa_cuti)) {
                //                 $sisa_cuti = (int) $master->sisa_cuti;
                //             } elseif (method_exists($master, 'toArray')) {
                //                 $arr = $master->toArray();
                //                 $sisa_cuti = isset($arr['sisa_cuti']) ? (int) $arr['sisa_cuti'] : null;
                //             }
                //         }
                //         if ($sisa_cuti === null) {
                //             throw new \Exception('Data sisa cuti tidak valid.');
                //         }

                //         if ($sisa_cuti < $days) {
                //             throw new \Exception('Sisa cuti tidak mencukupi untuk approval. Sisa: ' . $sisa_cuti . ', diperlukan: ' . $days);
                //         }

                //         $updated = $m_master->getConnection()->update(
                //             "update hris_master_cuti set cuti_terpakai = cuti_terpakai + ?, sisa_cuti = sisa_cuti - ? where nik = ? and tahun = ?",
                //             [$days, $days, $d->nik, $year]
                //         );
                //         if ($updated === 0) {
                //             throw new \Exception('Gagal memperbarui data master cuti.');
                //         }
                //     }
                // }
            }

            $deskripsi_log = 'keputusan pengajuan di-set ke ' . $status_label . ' oleh ' . $this->userdata['detail_user']['nama_detuser'];
            if (!empty($note)) $deskripsi_log .= ' (catatan: ' . substr($note,0,200) . ')';
            Modules::run('base/event/update', $m, $deskripsi_log, null, $id, $m);

            $this->result['status'] = 1;
            $this->result['message'] = 'Keputusan tersimpan.';
        } catch (\Exception $e){
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

    public function checkTanggalPengajuan()
    {
        $params = $_POST;
        $this->result = ['status' => 0, 'message' => 'Parameter tidak lengkap.'];

        if (empty($params['nik']) || empty($params['tanggal'])) {
            display_json($this->result);
            return;
        }

        $nik        = $params['nik'];
        $tanggal    = $params['tanggal'];

        try {

            $m = new \Model\Storage\PengajuanCuti_model();
            $exists = $m->where('nik', $nik)
                        ->where('tanggal_mulai', $tanggal)
                        ->whereNotIn('status_pengajuan', [4, 5])
                        ->first();

            if (empty($params['id'])){
                if ($exists) {
                    $this->result['message'] = 'Tanggal sudah pernah diajukan.';
                    display_json($this->result);
                    return;
                }
            }            

            $history_pengajuan = $this->getHistoryPengajuan($params);
            foreach ($history_pengajuan as $hp) {
                $tanggalPengajuan = strtotime($tanggal);
                $tanggalMulai     = strtotime($hp['tanggal_mulai']);
                $tanggalSelesai   = strtotime($hp['tanggal_selesai']);
                

                if ($tanggalPengajuan >= $tanggalMulai && $tanggalPengajuan <= $tanggalSelesai) {
                    $this->result['message'] = 'Tanggal pengajuan berada di antara pengajuan sebelumnya (' .
                        date('d M Y', $tanggalMulai) . ' s/d ' .
                        date('d M Y', $tanggalSelesai) . ').';
                    display_json($this->result);
                    return;
                }
            }

            $this->result['status'] = 1;
            $this->result['message'] = 'Tanggal belum diajukan.';
        } catch (\Exception $e) {
            $this->result['status'] = 0;
            $this->result['message'] = 'Gagal memeriksa tanggal: ' . $e->getMessage();
        }

        display_json($this->result);
    }

    public function getHistoryPengajuan($params)
    {

        $nik = $params['nik'];
        $id  = $params['id'] ?? null;
        $m_conf = new \Model\Storage\Conf();
        $sql    = "select tanggal_mulai, tanggal_selesai from hris_pengajuan_cuti where nik = '".$nik."' and status_pengajuan not in (4,5)";

        if($id){
            $sql .= " and id != ".$id;
        }

        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

        $data = [];
        if ($d_conf->count() > 0) {
            $data = $d_conf->toArray();
        }

        return $data;
    }


    public function ApprovalPengajuanCuti()
    {
        $akses = hakAkses($this->url);

        $this->add_external_js(array(
            "assets/select2/js/select2.min.js",
            "assets/moments/moment.js",
            "assets/toastr/js/toastr.js",
            "assets/toastr/js/toastr.min.js",
            "assets/bootbox_old/js/bootbox.js",
            "assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js",
            "assets/hris/pengajuan_cuti/js/pengajuan_cuti.js",
            "assets/xlsx/js/xlsx.full.min.js"
        ));
        $this->add_external_css(array(
            'assets/select2/css/select2.min.css',
            'assets/bootstrap-datetimepicker/css/bootstrap-datetimepicker.old.css',
            "assets/toastr/css/toastr.css",
            "assets/toastr/css/toastr.min.css",
            'assets/hris/pengajuan_cuti/css/pengajuan_cuti.css'
        ));

        $content = [];
        
        $data 				= $this->includes;
        $data['title_menu'] = 'Approval Pengajuan Cuti';
        $data['pengajuan']  = $this->getLaporanPengajuan();
        $data['akses']      = hakAkses('/hris/PengajuanCuti/ApprovalPengajuanCuti');
        $data['config']     = $_SESSION['detail_user']['nama_detuser'];

        // cetak_r($data['pengajuan'], 1);
        
        $data['view'] 		= $this->load->view($this->pathView. 'v_laporan_pengajuan', $data, true);       
        $this->load->view($this->template, $data);
    }

    public function filterPengajuan()
    {
        $params     = $_POST;
        $data       = $this->getPengajuan();

        $temp_data = [];

        foreach ($data as $d) {
            if (empty($params['status'])) {
                $temp_data[] = $d;
                continue;
            }

            if ($params['status'] == 'REJECT') {
                if (in_array($d['status_pengajuan'], [4, 5])) {
                    $temp_data[] = $d;
                }
                continue;
            }

            if ($d['status_pengajuan'] == $params['status']) {
                $temp_data[] = $d;
            }
        }
        $content['list'] = $temp_data;

        // cetak_r($params, 1);

        echo $this->load->view($this->pathView . 'v_list', $content, TRUE);
    }

    public function showAttachment()
    {
        $data['attachment'] = $this->getAttachments($_POST['id']);
        // cetak_r($data, 1);
        echo $this->load->view($this->pathView . 'v_detail_attahcment', $data, TRUE);
    }

    public function revert_status()
    {
        $params = $_POST;
        $this->result = ['status' => 0, 'message' => 'Parameter tidak lengkap.'];
        $id = !empty($params['id']) ? $params['id'] : (!empty($params['id_data']) ? $params['id_data'] : null);
        if (empty($id) || empty($params['revert'])){
            display_json($this->result);
            return;
        }

        $revert_type = strtoupper(trim($params['revert']));

        try {
            $m = new \Model\Storage\PengajuanCuti_model();
            $d = $m->where('id', $id)->first();
            if (!$d) throw new \Exception('Data tidak ditemukan');

            if ($revert_type === 'DRAFT') {
                $m->where('id', $id)->update([
                    'status_pengajuan' => 1, // revert ke DRAFT
                    'keterangan_reject' => null,
                    'ack_by' => null,
                    'ack_date' => null,
                    'approve_by' => null,
                    'approve_date' => null,
                    'revert_first_at' => date('Y-m-d H:i:s'),
                    'revert_note' => $params['revert_note']
                ]);

                $deskripsi_log = 'status pengajuan di-revert ke DRAFT oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run('base/event/update', $m, $deskripsi_log, null, $id, $m);

                $this->result['status'] = 1;
                $this->result['message'] = 'Status berhasil di-revert ke DRAFT.';
                display_json($this->result);
                return;
            }

            if ($revert_type === 'ACK') {
                $current_status = isset($d->status_pengajuan) ? (int) $d->status_pengajuan : null;
                if (!in_array($current_status, [3, 4, 5])) {
                    throw new \Exception('Revert ke ACK hanya dapat dilakukan dari status APPROVED atau REJECT.');
                }

                $update = [
                    'status_pengajuan' => 2,
                    'approve_by' => null,
                    'approve_date' => null,
                    'keterangan_reject' => null,
                    'revert_second_at' => date('Y-m-d H:i:s'),
                    'revert_note' => $params['revert_note']
                ];

                $jenis_val = strtolower(trim((string) $d->jenis_cuti));
                if (is_numeric($jenis_val)) {
                    if ($jenis_val === '1') $jenis_val = 'cuti';
                    elseif ($jenis_val === '2') $jenis_val = 'cuti_force_majeure';
                    elseif ($jenis_val === '3') $jenis_val = 'cuti_sakit';
                    elseif ($jenis_val === '4') $jenis_val = 'cuti_jatah_liburan';
                }

                // jika status saat ini APPROVED, kembalikan perubahan master cuti
                // if ($current_status === 3 && in_array($jenis_val, ['cuti', 'cuti_force_majeure', 'cuti_sakit'])) {
                //     $tanggal_mulai = !empty($d->tanggal_mulai) ? substr($d->tanggal_mulai, 0, 10) : null;
                //     $tanggal_selesai = !empty($d->tanggal_selesai) ? substr($d->tanggal_selesai, 0, 10) : null;
                //     if ($tanggal_mulai && $tanggal_selesai) {
                //         $start = new \DateTime($tanggal_mulai);
                //         $end = new \DateTime($tanggal_selesai);
                //         if ($end < $start) {
                //             throw new \Exception('Tanggal selesai tidak valid saat revert approval.');
                //         }

                //         $days = null;
                //         if (isset($d->jumlah_hari)) {
                //             $days = (int) $d->jumlah_hari;
                //         } elseif (is_array($d) && isset($d['jumlah_hari'])) {
                //             $days = (int) $d['jumlah_hari'];
                //         }
                //         if ($days === null) {
                //             $days = 0;
                //             $cur = clone $start;
                //             while ($cur <= $end) {
                //                 if ($cur->format('w') != 0) {
                //                     $days++;
                //                 }
                //                 $cur->modify('+1 day');
                //             }
                //         }

                //         $year = (int) $start->format('Y');
                //         $m_master = new \Model\Storage\Conf();
                //         $master = $m_master->hydrateRaw("select * from hris_master_cuti where nik = ? and tahun = ?", [$d->nik, $year])->first();
                //         if (!$master) {
                //             throw new \Exception('Data master cuti tidak ditemukan untuk nik ' . $d->nik . ' tahun ' . $year . '.');
                //         }

                //         $updated = $m_master->getConnection()->update(
                //             "update hris_master_cuti set cuti_terpakai = cuti_terpakai - ?, sisa_cuti = sisa_cuti + ? where nik = ? and tahun = ?",
                //             [$days, $days, $d->nik, $year]
                //         );
                //         if ($updated === 0) {
                //             throw new \Exception('Gagal mengembalikan data master cuti.');
                //         }
                //     }
                // }

                $m->where('id', $id)->update($update);

                $deskripsi_log = 'status pengajuan di-revert ke ACKNOWLEDGE oleh ' . $this->userdata['detail_user']['nama_detuser'];
                Modules::run('base/event/update', $m, $deskripsi_log, null, $id, $m);

                $this->result['status'] = 1;
                $this->result['message'] = 'Status berhasil di-revert ke ACKNOWLEDGE.';
                display_json($this->result);
                return;
            }

            throw new \Exception('Tipe revert tidak valid.');
        } catch (\Exception $e){
            $this->result['message'] = $e->getMessage();
        }

        display_json($this->result);
    }

}