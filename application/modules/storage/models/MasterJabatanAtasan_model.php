<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class MasterJabatanAtasan_model extends Conf{
	
	public $table = 'jabatan_atasan';
	protected $primaryKey = 'kode_jabatan';
	public $timestamps = false;

}
