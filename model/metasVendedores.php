<?php
class ModelGerencialMetasVendedores extends Model {
    
    public function cadastrarMeta($meta) {
        $this->db->query("INSERT INTO erp_vendedores_metas 
            (user_id, valor_da_meta, data_fechamento) 
            VALUES ('" . (int)$meta['user_id'] . "', '" . (float)$meta['valor_da_meta'] . "', '" . $this->db->escape($meta['data_fechamento']) . "')");
        
        return $this->db->countAffected() > 0;
    }

    public function getVendedores() {
        $query = $this->db->query("SELECT u.user_id, u.firstname, u.lastname, u.status  
            FROM erp_user u 
            INNER JOIN erp_user_group g ON u.user_group_id = g.user_group_id
            WHERE g.user_group_id = 13 
              AND u.store_id = 1 
              AND u.status = 1");
        return $query->rows;
    }

    public function getDatas() {
        $query = $this->db->query("SELECT m.user_id, m.valor_da_meta, m.data_fechamento, u.firstname, u.lastname 
            FROM erp_vendedores_metas m
            INNER JOIN erp_user u ON m.user_id = u.user_id
            INNER JOIN erp_user_group g ON u.user_group_id = g.user_group_id
            WHERE g.user_group_id = 13 
              AND u.store_id = 1 
              AND u.status = 1
              ORDER BY m.valor_da_meta DESC
              ");
        return $query->rows;
    }

    public function atualizarMeta($meta) {
        $this->db->query("UPDATE erp_vendedores_metas 
            SET valor_da_meta = '" . (float)$meta['valor_da_meta'] . "',
                data_fechamento = '" . $this->db->escape($meta['data_fechamento']) . "'
            WHERE user_id = '" . (int)$meta['user_id'] . "' ");
        
        return $this->db->countAffected() >= 0;
    }
}
?>
