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

class StrukturOrganisasi extends Public_Controller {

    private $pathView = 'hris/struktur_organisasi/';
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

            $m_so = new \Model\Storage\StrukturOrganisasi_model();

            $this->add_external_js(array(
                "assets/jquery/easy-autocomplete/jquery.easy-autocomplete.min.js",
                "assets/select2/js/select2.min.js",
                "assets/hris/struktur_organisasi/js/struktur_organisasi.js",
                "assets/xlsx/js/xlsx.full.min.js",
                // "assets/html2pdf/html2pdf.bundle.min.js",
                "assets/html2pdf/html2canvas.min.js",
                "assets/html2pdf/jspdf.umd.min.js",
                
                
            ));
            $this->add_external_css(array(
                "assets/jquery/easy-autocomplete/easy-autocomplete.min.css",
                "assets/jquery/easy-autocomplete/easy-autocomplete.themes.min.css",
                "assets/select2/css/select2.min.css",
                "assets/hris/struktur_organisasi/css/struktur_organisasi.css",
            ));

            $data                       = $this->includes;
            $content['akses']           = $this->hakAkses;
            $content['title_panel']     = 'HRIS - Struktur Organisasi';

            $data_so                    = $m_so->data_struktur_organisasi();
            // $content['struktur']        = $this->buildTree($data_so);
            $content['unit']            = $m_so->get_unit();
            $content['perwakilan']      = $m_so->get_perwakilan();
            $content['struktur']        = $this->treeBuilder($data_so);

            // cetak_r($content['struktur'], 1);

            $data['title_menu']     = 'HRIS - Struktur Organisasi';

            $data['view'] = $this->load->view($this->pathView . 'v_index', $content, TRUE);
            $this->load->view($this->template, $data);

        } else {
            showErrorAkses();
        }
    }


    // public function treeBuilder($data_so) { 
    //     // cetak_r($data_so, 1);
    //     $flatToTree = function ($items, $parentNik = '') use (&$flatToTree) { 
    //         $branch = []; foreach ($items as $item) { 
    //             if ($item['atasan_nik'] == $parentNik) { 
    //                 $wilayahs   = array_map( 'trim', explode(',', $item['nama_wilayah']) ); 
    //                 $units      = array_map( 'trim', explode(',', $item['nama_unit']) ); 
    //                 $node       = [ 
    //                                 't'     => $wilayahs[0] ?? '', 'u' => ucwords(strtolower($units[0])) ?? '', 
    //                                 'role'  => $item['nama_jabatan'], 
    //                                 'name'  => ucwords(strtolower($item['nama'])), 
    //                                 'level' => (int) $item['level'], 
    //                             ]; 

    //                 $children   = $flatToTree($items, $item['nik']); 
    //                 if (!empty($children)) { 
    //                     $node['children'] = $children; 
    //                 } 
    //                 $branch[]   = $node; 
    //             } 
    //         } 
            
    //         return $branch; 
    //     }; 

    //     $tree = $flatToTree($data_so); 
    //     return $tree[0] ?? null; 
    // }

    public function treeBuilder($data_so) { 
    // cetak_r($data_so, 1);
        $flatToTree = function ($items, $parentNik = '') use (&$flatToTree) { 
            $branch = []; 
            foreach ($items as $item) { 
                if ($item['atasan_nik'] == $parentNik) { 
                    $wilayahs = array_map('trim', explode(',', $item['nama_wilayah'])); 
                    $units    = array_map('trim', explode(',', $item['nama_unit'])); 
                    
                    // ✅ tampilkan SEMUA wilayah (mis: "Jawa Timur 1, Jawa Timur 2")
                    $wilayahStr = implode(', ', array_filter($wilayahs));
                    
                    // ✅ tampilkan SEMUA unit (jika ada lebih dari 1)
                    $unitStr = implode(', ', array_map(function($u) {
                        return ucwords(strtolower($u));
                    }, array_filter($units)));
                    
                    $node = [ 
                        't'     => ucwords(strtolower($wilayahStr)) ?: '', 
                        'u'     => $unitStr ?: '', 
                        'role'  => $item['nama_jabatan'], 
                        'name'  => ucwords(strtolower($item['nama'])), 
                        'level' => (int) $item['level'], 
                    ]; 

                    $children = $flatToTree($items, $item['nik']); 
                    if (!empty($children)) { 
                        $node['children'] = $children; 
                    } 
                    $branch[] = $node; 
                } 
            } 
            return $branch; 
        }; 

        $tree = $flatToTree($data_so); 
        return $tree[0] ?? null; 
    }


    // private function buildTree($data, $parent = null)
    // {
    //     $tree = [];


    //     foreach ($data as $row) {

    //         if ($row['atasan_nik'] == $parent) {

    //             $children = $this->buildTree($data, $row['nik']);

    //             if (!empty($children)) {

    //                 if (count($children) == 1) {
    //                     $children[0]['one_child'] = 1;
    //                 }

    //                 $row['children'] = $children;
    //             }

    //             $tree[] = $row;
    //         }
           
    //     }

    //     // cetak_r($tree, 1);
    //     return $tree;
    // }

    // private function buildTreePerwakilan($data)
    // {
    //     $tree = [];
    //     if (empty($data)) {
    //         return $tree;
    //     }

    //     $nikList = array_map('strval', array_column($data, 'nik'));

    //     foreach ($data as $row) {
    //         $parentNik = isset($row['atasan_nik']) ? strval($row['atasan_nik']) : '';

    //         if ($parentNik === '' || !in_array($parentNik, $nikList, false)) {
    //             $children = $this->buildTree($data, $row['nik']);

    //             if (!empty($children)) {
    //                 if (count($children) == 1) {
    //                     $children[0]['one_child'] = 1;
    //                 }

    //                 $row['children'] = $children;
    //             }

    //             $tree[] = $row;
    //         }
    //     }

    //     // cetak_r($tree, 1);


    //     return $tree;
    // }


    public function filterStruktur()
    {
        $m_so       = new \Model\Storage\StrukturOrganisasi_model();

        $data_so    = $m_so->data_struktur_organisasi($_POST);

        // if (!empty($_POST['unit'] ?? null) || !empty($_POST['wilayah'] ?? null)) {
        //     $struktur = $this->buildTreePerwakilan($data_so);

        //     if (empty($struktur)) {
        //         log_message('debug', 'filterStruktur: buildTreePerwakilan returned empty; data count=' . count($data_so));
        //         $struktur = $this->buildTree($data_so);
        //     }

        //     $content['struktur'] = $struktur;
        // } else {
        //     $content['struktur']       = $this->buildTree($data_so);
        // }

        $content['struktur_filter']        = $this->treeBuilder($data_so);

        // cetak_r($content['struktur_filter'], 1);



        echo $this->load->view($this->pathView.'v_filter_so', $content, true);

    }

    // public function exportExcel()
    // {
    //     if (ob_get_level()) {
    //         ob_end_clean();
    //     }

    //     $m_so    = new \Model\Storage\StrukturOrganisasi_model();
    //     $data_so = $m_so->data_struktur_organisasi($_GET);
    //     $data    = $this->buildTree($data_so);


    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $sheet->setTitle('Struktur Organisasi');

    //     $row = 1;
    //     $col = 5;


    //     $this->exportTreeChart(
    //         $data,
    //         $sheet,
    //         $row,
    //         $col
    //     );

    //     foreach(range('A','Z') as $column)
    //     {
    //         $sheet->getColumnDimension($column)
    //             ->setWidth(18);
    //     }


    //     $filename = "struktur_organisasi.xlsx";


    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="'.$filename.'"');
    //     header('Cache-Control: max-age=0');


    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');

    //     exit;
    // }

    // private function exportTreeChart($data, &$sheet, &$row, $col)
    // {

    //     foreach($data as $item)
    //     {

    //         // posisi node
    //         $cell = $sheet->getCellByColumnAndRow(
    //             $col,
    //             $row
    //         )->getCoordinate();


    //         $sheet->setCellValue(
    //             $cell,
    //             $item['nama_jabatan']."\n".$item['nama']
    //         );


    //         // style box
    //         $sheet->getStyle($cell)
    //         ->getAlignment()
    //         ->setHorizontal(
    //             \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
    //         );


    //         $sheet->getStyle($cell)
    //         ->getAlignment()
    //         ->setVertical(
    //             \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    //         );


    //         $sheet->getStyle($cell)
    //         ->getAlignment()
    //         ->setWrapText(true);



    //         $sheet->getStyle($cell)
    //         ->getBorders()
    //         ->getAllBorders()
    //         ->setBorderStyle(
    //             \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //         );


    //         $sheet->getRowDimension($row)
    //             ->setRowHeight(40);



    //         // anak
    //         if(!empty($item['children']))
    //         {

    //             $childCol = $col - count($item['children']);

    //             foreach($item['children'] as $child)
    //             {
    //                 $rowChild = $row + 2;
    //                 $this->exportTreeChart(
    //                     [$child],
    //                     $sheet,
    //                     $rowChild,
    //                     $childCol
    //                 );
    //                 $childCol += 2;
    //             }
    //         }
    //     }
    // }

    // public function exportExcelList()
    // {
    //     if (ob_get_level()) {
    //         ob_end_clean();
    //     }

    //     $m_so    = new \Model\Storage\StrukturOrganisasi_model();
    //     $data_so = $m_so->data_struktur_organisasi($_GET);
    //     $data    = $this->buildTree($data_so);

    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     $sheet->setTitle('Struktur Organisasi');

    //     // Header
    //     $sheet->setCellValue('A1', 'Level');
    //     $sheet->setCellValue('B1', 'Jabatan');
    //     $sheet->setCellValue('C1', 'Nama');
    //     $sheet->setCellValue('D1', 'NIK');
    //     $sheet->setCellValue('E1', 'Atasan NIK');

    //     $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    //     $sheet->getStyle('A1:E1')->getAlignment()
    //         ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    //         ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    //     $row = 2;

    //     $this->exportListExcel($data, $sheet, $row);

    //     // Freeze Header
    //     $sheet->freezePane('A2');

    //     // Auto Width
    //     foreach (range('A', 'E') as $col) {
    //         $sheet->getColumnDimension($col)->setAutoSize(true);
    //     }

    //     // Border
    //     $sheet->getStyle('A1:E'.($row-1))
    //         ->getBorders()
    //         ->getAllBorders()
    //         ->setBorderStyle(
    //             \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //         );

    //     // Enable Outline
    //     $sheet->setShowSummaryBelow(true);
    //     $sheet->setShowSummaryRight(true);

    //     $filename = "struktur_organisasi.xlsx";

    //     if (ob_get_length()) {
    //         ob_end_clean();
    //     }

    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename="'.$filename.'"');
    //     header('Cache-Control: max-age=0');

    //     $writer = new Xlsx($spreadsheet);
    //     $writer->save('php://output');

    //     exit;
    // }

    // private function exportListExcel($data, $sheet, &$row, $depth = 0)
    // {
    //     foreach ($data as $item)
    //     {
    //         // Indentasi jabatan
    //         $jabatan = str_repeat('    ', $depth);

    //         if ($depth > 0) {
    //             $jabatan .= '└── ';
    //         }

    //         $jabatan .= $item['nama_jabatan'];

    //         $sheet->setCellValue('A'.$row, $item['level']);
    //         $sheet->setCellValue('B'.$row, $jabatan);
    //         $sheet->setCellValue('C'.$row, $item['nama']);
    //         $sheet->setCellValue('D'.$row, $item['nik']);
    //         $sheet->setCellValue('E'.$row, $item['atasan_nik']);

    //         // Vertical Align
    //         $sheet->getStyle('A'.$row.':E'.$row)
    //             ->getAlignment()
    //             ->setVertical(
    //                 \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    //             );

    //         // Outline Level
    //         $sheet->getRowDimension($row)
    //             ->setOutlineLevel($depth);

    //         // Default semua tampil
    //         $sheet->getRowDimension($row)
    //             ->setVisible(true);

    //         $sheet->getRowDimension($row)
    //             ->setCollapsed(false);

    //         $row++;

    //         // Recursive
    //         if (!empty($item['children'])) {
    //             $this->exportListExcel(
    //                 $item['children'],
    //                 $sheet,
    //                 $row,
    //                 $depth + 1
    //             );
    //         }
    //     }
    // }
    

}