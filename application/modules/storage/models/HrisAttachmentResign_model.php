<?php
namespace Model\Storage;
use \Model\Storage\Conf as Conf;

class HrisAttachmentResign_model extends Conf{
    public $table = 'hris_attachment_resign';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
