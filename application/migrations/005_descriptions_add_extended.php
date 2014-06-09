<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Descriptions_Add_Extended extends CI_Migration {
    public function up(){
        $fields = array('extended' => array('type' => 'TINYINT'));
        $this->dbforge->add_column('descriptions', $fields);
    }
 
    public function down(){
        $this->dbforge->drop_column('descriptions', 'extended');
    }
}