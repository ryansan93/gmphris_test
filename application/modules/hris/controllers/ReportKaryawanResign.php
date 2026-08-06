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

class ReportKaryawanResign extends Public_Controller {

    private $pathView = 'hris/report_karyawan_resign/';
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

            $m_conf     = new \Model\Storage\Conf();
            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/report_karyawan_resign/js/report_karyawan_resign.js",
                "assets/xlsx/js/xlsx.full.min.js",
                "assets/html2pdf/html2canvas.min.js",
                "assets/html2pdf/jspdf.umd.min.js",
                
                
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/report_karyawan_resign/css/report_karyawan_resign.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Report Karyawan Resign';
            $content['jabatan']         = $m_conf->hydrateRaw("select * from jabatan")->toArray();

            // cetak_r($content['struktur'], 1);

            $data['title_menu']     = 'HRIS - Report Karyawan Resign';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }

    public function load_data()
    {
        
        $data['list']  = $this->get_report_resign();
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

        $data['list']  = $this->get_report_resign($need);
        echo $this->load->view($this->pathView . 'v_list', $data, TRUE);
    }
    

    public function get_report_resign($need = null)
    {
        $m_conf      = new \Model\Storage\Conf();

        $sql = " select 	
                    hur.id,
                    hur.nik, 
                    hur.alasan_resign, 
                    hur.tanggal_pengajuan, 
                    hur.tanggal_resign, 
                    hur.status, 
                    hur.document,
                    hur.jenis_resign,
                    hur.ack_by,
                    hur.ack_date,
                    hur.approved_by,
                    hur.approve_reject_date,
                    hur.clearance_date,
                    hur.verification_by,
                    hur.verification_clearance_date,
                    hur.nonactive_user_by,
                    hur.nonactive_user_date,
                    k.nama as nama_karyawan,
                    j.nama as nama_jabatan,
                    u.nama_unit,
                    u.nama_wilayah
                from hris_usulan_resign hur 
                INNER JOIN karyawan k ON k.nik = hur.nik
                    AND k.id = (
                        SELECT MAX(k2.id)
                        FROM karyawan k2
                        WHERE k2.nik = hur.nik
                    )
                INNER JOIN jabatan j ON k.jabatan = j.kode
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
            $where[] = " hur.id = '".addslashes($id)."' ";

        }

        if ($jenis == 'FILTER' && is_array($dataNeed)) {
            $jenis     = $dataNeed['jenis'] ?? null;
            $jabatan   = $dataNeed['jabatan'] ?? null;
            $status   = $dataNeed['status'] ?? null;

            if ($jenis) {
                $where[] = "hur.jenis_resign = '" . addslashes($jenis) . "' ";
            }

            if ($jabatan) {
                $where[] = "k.jabatan = '".addslashes($jabatan)."'";
            }

            if ($status) {
                $where[] = "hur.status = '".addslashes($status)."'";
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY hur.id DESC";   
        

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

        $data['list']       = $this->get_report_resign($need);
        $data['lampiran']   = $this->get_attachment_usulan($params['id_data']);
        $data['clearance']  = $this->get_attachment_clearance($params['id_data']);
        // cetak_r($data, 1);

        echo $this->load->view($this->pathView . 'v_detail', $data, TRUE);

    }


    public function get_attachment_usulan($id)
    {
        $m_conf      = new \Model\Storage\Conf();

        $sql = " select nama_file, file_path from hris_attachment_resign where usulan_id = ". $id ;
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

    // public function data_export_excel($need)
    // {
    //     $data = $this->get_report_resign($need);

    //     $id = [];
    //     foreach($data as $d){
    //         $id =  $d['id'];
    //     }

    //     cetak_r($id, 1);
    //     $attachment = [];

    // }

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



        $data = $this->get_report_resign($need);
        // $data = $this->data_export_excel($need);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Laporan Resign Karyawan');


        // ==========================
        // COLUMN WIDTH
        // ==========================

        foreach(range('A','U') as $column)
        {
            $sheet->getColumnDimension($column)->setWidth(20);
        }



        // ==========================
        // HEADER LAPORAN
        // ==========================

        $sheet->mergeCells('A1:U1');
        $sheet->setCellValue('A1', 'LAPORAN KARYAWAN RESIGN');

        $sheet->mergeCells('A2:U2');
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
            'Unit',
            'Wilayah',
            'Jenis Resign',
            'Document',
            'Tanggal Pengajuan',
            'Tanggal Resign',
            'Alasan Resign',
            'Status',

            'Ack By',
            'Ack Date',

            'Approved By',
            'Approve Date',

            'Clearance Date',

            'Verification By',
            'Verification Clearance Date',

            'Nonactive User By',
            'Nonactive User Date',
        ];


        $sheet->fromArray($header, null, 'A4');


        $sheet->getStyle('A4:U4')->getFont()
            ->setBold(true);

        $sheet->getStyle('A4:U4')->getAlignment()
            ->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );



        // ==========================
        // DATA
        // ==========================

        $row = 5;

        foreach ($data as $key => $d) {


            switch ((int)$d['status']) {

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
                    $status = 'Rejected';
                    break;

                default:
                    $status = '-';
                    break;
            }



            $sheet->fromArray([


                $key + 1,


                $d['nik'],


                ucwords(strtolower($d['nama_karyawan'])),


                $d['nama_jabatan'] ?? '-',


                $d['nama_unit'] ?? '-',


                $d['nama_wilayah'] ?? '-',


                $d['jenis_resign'] ?? '-',


                $d['document'] ?? '-',



                !empty($d['tanggal_pengajuan'])
                    ? date('d-m-Y', strtotime($d['tanggal_pengajuan']))
                    : '',



                !empty($d['tanggal_resign'])
                    ? date('d-m-Y', strtotime($d['tanggal_resign']))
                    : '',



                $d['alasan_resign'] ?? '-',


                $status,



                // ACK
                $d['ack_by'] ?? '-',

                !empty($d['ack_date'])
                    ? date('d-m-Y H:i', strtotime($d['ack_date']))
                    : '-',



                // APPROVE
                $d['approved_by'] ?? '-',

                !empty($d['approve_reject_date'])
                    ? date('d-m-Y H:i', strtotime($d['approve_reject_date']))
                    : '-',



                // CLEARANCE
                !empty($d['clearance_date'])
                    ? date('d-m-Y H:i', strtotime($d['clearance_date']))
                    : '-',



                // VERIFICATION
                $d['verification_by'] ?? '-',


                !empty($d['verification_clearance_date'])
                    ? date('d-m-Y H:i', strtotime($d['verification_clearance_date']))
                    : '-',



                // NONACTIVE
                $d['nonactive_user_by'] ?? '-',


                !empty($d['nonactive_user_date'])
                    ? date('d-m-Y H:i', strtotime($d['nonactive_user_date']))
                    : '-',


            ], null, 'A'.$row);


            $row++;

        }



        // ==========================
        // STYLE TABLE
        // ==========================

        $sheet->getStyle('A4:U'.($row-1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(
                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            );


        $sheet->setAutoFilter('A4:U'.($row-1));

        $sheet->freezePane('A5');



        // ==========================
        // EXPORT
        // ==========================

        $filename = "laporan_resign_karyawan.xlsx";


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        header('Content-Disposition: attachment;filename="'.$filename.'"');

        header('Cache-Control: max-age=0');


        $writer = new Xlsx($spreadsheet);

        $writer->save('php://output');

        exit;
    }

}