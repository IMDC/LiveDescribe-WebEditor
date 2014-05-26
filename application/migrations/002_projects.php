<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Projects extends CI_Migration {
    public function up(){
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("user_id int(11) unsigned NOT NULL");
        $this->dbforge->add_field("video_id varchar(255) NOT NULL");
        $this->dbforge->add_field("project_name Text NOT NULL DEFAULT ''");
        $this->dbforge->add_field("project_description Text NOT NULL DEFAULT ''");
        $this->dbforge->add_field("date_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
       
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
               
        $this->dbforge->create_table('projects', TRUE);
    }
 
    public function down(){
        $this->dbforge->drop_table('projects');
    }
}