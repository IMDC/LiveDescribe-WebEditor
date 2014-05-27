<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Ratings extends CI_Migration {
    public function up(){
        $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
        $this->dbforge->add_field("user_id int(11) unsigned NOT NULL");
        $this->dbforge->add_field("project_id int(11) unsigned NOT NULL");
        $this->dbforge->add_field("video_id varchar(255) NOT NULL");        
        $this->dbforge->add_field("like_dislike int(11) NOT NULL DEFAULT 0");
 
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('project_id');
        
        $this->dbforge->create_table('ratings', TRUE);
    }
 
    public function down(){
        $this->dbforge->drop_table('ratings');
    }
}