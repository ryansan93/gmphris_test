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

class ReportCutiKaryawan extends Public_Controller {

    private $pathView = 'hris/report_cuti_karyawan/';
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

        // if ( $this->hakAkses['a_view'] == 1 ) {

            $m_conf     = new \Model\Storage\Conf();
            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/report_cuti_karyawan/js/report_cuti_karyawan.js",
                "assets/xlsx/js/xlsx.full.min.js",
                "assets/html2pdf/html2canvas.min.js",
                "assets/html2pdf/jspdf.umd.min.js",
                
                
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/report_cuti_karyawan/css/report_cuti_karyawan.css",
            ));

            $m_karyawan 			= new \Model\Storage\Karyawan_model();

            $d_karyawan 			= $m_karyawan->select(
                                        'karyawan.*',
                                        'jabatan.nama as nama_jabatan'
                                    )->join('jabatan', 'jabatan.kode', '=', 'karyawan.jabatan')
                                    ->where('karyawan.status', 1)
                                    ->orderBy('karyawan.level', 'asc')
                                    ->get();

            $data_karyawan  		= $d_karyawan->toArray();

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Report Cuti Karyawan';
            $content['jabatan']         = $m_conf->hydrateRaw("select * from jabatan")->toArray();
            $content['karyawan']        = $data_karyawan;

            // cetak_r($content['struktur'], 1);

            $data['title_menu']     = 'HRIS - Report Cuti Karyawan';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        // } else {
        //     showErrorAkses();
        // }
    }

    public function load_data()
    {
        
        $data['list']  = $this->get_report_cuti();
        // cetak_r($data, 1);

        echo $this->load->view($this->pathView . 'v_list', $data, TRUE);
    }

    public function filter_data()
    {
        $params = $_POST;
        $need = [
            'jenis' => 'FILTER',
            'data'  => $params,
        ];
        // cetak_r($params, 1);

        $data['list']  = $this->get_report_cuti($need);
        echo $this->load->view($this->pathView . 'v_list', $data, TRUE);
    }
    

    public function get_report_cuti($need = null)
    {
        $m_conf      = new \Model\Storage\Conf();

        $sql = " select 
                    hpc.id, 
                    hpc.nik, 
                    hpc.jenis_cuti, 
                    hpc.tanggal_mulai, 
                    hpc.tanggal_selesai, 
                    hpc.alasan,
                    hpc.status_pengajuan,
                    hpc.jumlah_hari,
                    hpc.ack_by,
                    hpc.ack_date,
                    hpc.approve_by,
                    hpc.approve_date,
                    hpc.keterangan_reject,
                    k.nama as nama_karyawan, 
                    j.nama as nama_jabatan
                    from hris_pengajuan_cuti hpc
                    inner join karyawan k on k.nik = hpc.nik and k.status = 1
                    inner join jabatan j on k.jabatan = j.nama 
                    OUTER APPLY
                    (
                        SELECT
                            nama_unit = STUFF
                            (
                                (
                                    SELECT ', ' +
                                        CASE 
                                            WHEN x.is_all = 1 THEN 'All'
                                            ELSE MAX(x.nama)
                                        END
                                    FROM
                                    (
                                        SELECT 
                                            uk2.unit,
                                            CASE 
                                                WHEN uk2.unit = 'all' THEN 1 
                                                ELSE 0 
                                            END AS is_all,
                                            w.kode,
                                            w.nama
                                        FROM unit_karyawan uk2
                                        LEFT JOIN wilayah w
                                            ON TRY_CAST(uk2.unit AS INT) = w.id
                                        WHERE uk2.id_karyawan = k.id
                                    ) x
                                    GROUP BY 
                                        x.is_all,
                                        x.kode
                                    FOR XML PATH('')
                                ),
                                1,2,''
                            ),
                            nama_wilayah = STUFF
                            (
                                (
                                    SELECT ', ' +
                                        CASE
                                            WHEN wk2.wilayah = 'all' THEN 'All'
                                            ELSE w.nama
                                        END
                                    FROM wilayah_karyawan wk2
                                    LEFT JOIN wilayah w
                                        ON TRY_CAST(wk2.wilayah AS INT) = w.id
                                    WHERE wk2.id_karyawan = k.id
                                    FOR XML PATH('')
                                ),
                                1,2,''
                            )
                    ) u ";

        $jenis     = $need['jenis'] ?? null;
        $dataNeed  = $need['data'] ?? null;

        $where = [];

        if (($jenis == 'DETAIL' ) && !empty($dataNeed)) {
            $id  = $dataNeed['id_data'] ?? null;
            $where[] = " hpc.id = '".addslashes($id)."' ";
        }

        if ($jenis == 'FILTER' && is_array($dataNeed)) {
            $jenis     = $dataNeed['jenis'] ?? null;
            $jabatan   = $dataNeed['jabatan'] ?? null;
            $bulan     = $dataNeed['bulan'] ?? null;
            $karyawan  = $dataNeed['karyawan'] ?? null;
            $status    = $dataNeed['status'] ?? null;
            $tahun     = $dataNeed['tahun'] ?? null;

            if ($jenis) {
                $where[] = "hpc.jenis_cuti = '" . addslashes($jenis) . "' ";
            }

            if ($jabatan) {
                $where[] = "k.jabatan = '".addslashes($jabatan)."'";
            }

            if ($bulan && $bulan !== 'all') {
                $where[] = "MONTH(hpc.tanggal_mulai) = '" . addslashes($bulan) . "' ";
            }

            if ($tahun) {
                $where[] = "YEAR(hpc.tanggal_mulai) = '" . addslashes($tahun) . "' ";
            }

            if ($karyawan) {
                $where[] = "k.nik = '".addslashes($karyawan)."'";
            }

            if ($status) {
                $where[] = "hpc.status_pengajuan = '".addslashes($status)."'";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY hpc.id DESC";   

        // cetak_r($need, 1);
        

        $d_conf = $m_conf->hydrateRaw($sql);

		$data = [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}
		return $data;
    }

    public function show_detail_data()
    {
        $params = $_POST;

        $need = [
            'jenis' => 'DETAIL',
            'data'  => $params,
        ];

        $data['list']       = $this->get_report_cuti($need);
        $data['lampiran']   = $this->get_attachment_pengajuan($params['id_data']);
        // cetak_r($data, 1);


        echo $this->load->view($this->pathView . 'v_detail', $data, TRUE);

    }


    public function get_attachment_pengajuan($id)
    {
        $m_conf      = new \Model\Storage\Conf();

        $sql = " select nama_file, file_path from hris_attachment_cuti where pengajuan_id = ". $id ;
        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

		$data = [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}
		return $data;
    }

    public function get_attachment_clearance($id)
    {
        $m_conf      = new \Model\Storage\Conf();

        $sql = " select * from hris_document_clearance where usulan_id = ". $id ;
        // cetak_r($sql, 1);

        $d_conf = $m_conf->hydrateRaw($sql);

		$data = [];
		if ($d_conf->count() > 0) {
			$data = $d_conf->toArray();
		}
		return $data;
    }

    public function export_excel()
    {
        if (ob_get_level()) {
            ob_end_clean();
        }

        $params = $_GET;

        $need = [
            'jenis' => 'FILTER',
            'data'  => $params,
        ];

        $data = $this->get_report_cuti($need);


        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Laporan Cuti Karyawan');


        // ==========================
        // COLUMN WIDTH
        // ==========================

        foreach(range('A','R') as $column)
        {
            $sheet->getColumnDimension($column)->setWidth(20);
        }



        // ==========================
        // HEADER LAPORAN
        // ==========================

        $sheet->mergeCells('A1:Q1');
        $sheet->setCellValue('A1', 'LAPORAN CUTI KARYAWAN');

        $sheet->mergeCells('A2:Q2');
        $sheet->setCellValue('A2', 'PT. GRIYA MITRA POULTRY');


        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(16);

        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );


        $sheet->getStyle('A2')->getFont()
            ->setBold(true)
            ->setSize(12);

        $sheet->getStyle('A2')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );



        // ==========================
        // HEADER TABLE
        // ==========================

        $header = [
            'No',
            'NIK',
            'Nama Karyawan',
            'Jabatan',
            'Jenis Cuti',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jumlah Hari',
            'Alasan',
            'Status Pengajuan',

            'Ack By',
            'Ack Date',

            'Approve By',
            'Approve Date',

            'Reject By',
            'Reject Date',

            'Keterangan Reject',
        ];


        $sheet->fromArray($header, null, 'A4');


        $sheet->getStyle('A4:Q4')->getFont()
            ->setBold(true);

        $sheet->getStyle('A4:Q4')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );



        // ==========================
        // DATA
        // ==========================

        $row = 5;

        foreach ($data as $key => $d) {


            $status_pengajuan = (int)$d['status_pengajuan'];


            switch ($status_pengajuan) {

                case 1:
                    $status = 'Draft';
                    break;

                case 2:
                    $status = 'Acknowledge';
                    break;

                case 3:
                    $status = 'Approved';
                    break;

                case 4:
                    $status = 'Rejected Atasan';
                    break;

                case 5:
                    $status = 'Rejected HRD';
                    break;

                default:
                    $status = '-';
                    break;
            }


            // default kosong
            $ack_by        = '-';
            $ack_date      = '-';

            $approve_by    = '-';
            $approve_date  = '-';

            $reject_by     = '-';
            $reject_date   = '-';



            switch ($status_pengajuan) {

                case 2: // ACKNOWLEDGE

                    $ack_by   = $d['ack_by'];
                    $ack_date = $d['ack_date'];

                    break;



                case 3: // APPROVED

                    $ack_by        = $d['ack_by'];
                    $ack_date      = $d['ack_date'];

                    $approve_by    = $d['approve_by'];
                    $approve_date  = $d['approve_date'];

                    break;



                case 4: // REJECT ATASAN

                    if (!empty($d['keterangan_reject'])) {

                        $reject_by   = $d['ack_by'];
                        $reject_date = $d['ack_date'];

                    }

                    break;



                case 5: // REJECT HRD

                    if (!empty($d['keterangan_reject'])) {

                        $reject_by   = $d['approve_by'];
                        $reject_date = $d['approve_date'];

                    }

                    break;
            }



            $sheet->fromArray([

                $key + 1,

                $d['nik'],

                ucwords(strtolower($d['nama_karyawan'])),

                $d['nama_jabatan'],

                ucwords(str_replace('_', ' ', $d['jenis_cuti'])),

                !empty($d['tanggal_mulai'])
                    ? date('d-m-Y', strtotime($d['tanggal_mulai']))
                    : '',

                !empty($d['tanggal_selesai'])
                    ? date('d-m-Y', strtotime($d['tanggal_selesai']))
                    : '',

                $d['jumlah_hari'],

                $d['alasan'],

                $status,


                // ACK
                $ack_by,
                $ack_date,


                // APPROVE
                $approve_by,
                $approve_date,


                // REJECT
                $reject_by,
                $reject_date,


                // KETERANGAN
                $d['keterangan_reject'] ?? '-',


            ], null, 'A'.$row);


            $row++;
        }

        // ==========================
        // STYLE TABLE
        // ==========================
        $sheet->getStyle('A4:Q'.($row-1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );
        $sheet->setAutoFilter('A4:Q'.($row-1));
        $sheet->freezePane('A5');

        // ==========================
        // EXPORT
        // ==========================

        $filename = "laporan_cuti_karyawan.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }
}