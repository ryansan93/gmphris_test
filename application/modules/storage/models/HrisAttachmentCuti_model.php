<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisAttachmentCuti_model extends Conf{
    public $table = 'hris_attachment_cuti';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
