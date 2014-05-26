<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Descriptions extends CI_Migration {
    public function up(){
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("user_id int(11) unsigned NOT NULL");
        $this->dbforge->add_field("desc_id varchar(255) NOT NULL");
        $this->dbforge->add_field("video_id varchar(255) NOT NULL");        
        $this->dbforge->add_field("start Double NOT NULL");
        $this->dbforge->add_field("end Double NOT NULL");
        $this->dbforge->add_field("filename varchar(255) NOT NULL DEFAULT ''");
        $this->dbforge->add_field("desc_text Text NOT NULL DEFAULT ''");
 
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        
        $this->dbforge->create_table('descriptions', TRUE);
    }
 
    public function down(){
        $this->dbforge->drop_table('descriptions');
    }
}