<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubmissionIntegrityIndexes extends Migration
{
    public function up()
    {
        $this->db->query(
            'CREATE INDEX idx_responses_link_mode ON responses (instrument_link_id, mode)'
        );

        $this->db->query(
            'CREATE INDEX idx_response_answers_response_item ON response_answers (response_id, instrument_item_id)'
        );

        $this->db->query(
            'CREATE INDEX idx_respondents_link_email ON respondents (instrument_link_id, email)'
        );

        $this->db->query(
            'CREATE INDEX idx_respondents_link_nim ON respondents (instrument_link_id, nim)'
        );
    }

    public function down()
    {
        $this->db->query('DROP INDEX idx_respondents_link_nim ON respondents');
        $this->db->query('DROP INDEX idx_respondents_link_email ON respondents');
        $this->db->query('DROP INDEX idx_response_answers_response_item ON response_answers');
        $this->db->query('DROP INDEX idx_responses_link_mode ON responses');
    }
}
