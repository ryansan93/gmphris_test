<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KpiKaryawan extends Public_Controller
{
	private $url;

	function __construct()
	{
		parent::__construct();
		$this->url = $this->current_base_uri;
	}

	public function index()
	{
		$akses = hakAkses($this->url);
		if ( $akses['a_view'] == 1 ) {
			$this->add_external_js(array(
				'assets/select2/js/select2.min.js',
				'assets/parameter/kpi_karyawan/js/kpi_karyawan.js'
			));
			$this->add_external_css(array(
				'assets/select2/css/select2.min.css',
				'assets/parameter/kpi_karyawan/css/kpi_karyawan.css'
			));

			$data 				= $this->includes;
			$content['akses'] 	= $akses;
			$data['title_menu'] = 'KPI Karyawan';
			$data['view'] 		= $this->load->view('hris/kpi_karyawan/v_index', $content, true);

			$this->load->view($this->template, $data);
		} else {
			showErrorAkses();
		}
	}
}