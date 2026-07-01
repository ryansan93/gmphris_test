<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class MasterJabatan_model extends Conf{
	
	public $table = 'jabatan';
	protected $primaryKey = 'kode';
	public $timestamps = false;

}
