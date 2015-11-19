<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class User_model extends CI_Model {
  private $table_name = "user";
  private $trans_table = "user_transaction";
  public function __construct(){
      parent::__construct();
  }

  // поиск пользователя
  public function searchUser($user_id){
    $query = $this->db->get_where($this->table_name,array("id"=>$user_id));
    if ($query->num_rows() >  0){
        return $query->row();
    }
    else{
      return NULL;
    }
  }
  public function listTrans($user_name){
    $this->db->select('amount,`user_transaction.id`');
    $this->db->from($this->trans_table);
    $this->db->join($this->table_name, 'user_transaction.user_id = user.id');
    $this->db->where('name', $user_name);
    $query = $this->db->get();
    if ($query->num_rows() >  0){
        return $query->result_array();
    }
    else{
      return NULL;
    }
  }
  private function getBalance($user_id){
     $balance = NULL;
     $this->db->select('balance');
     $query = $this->db->get_where($this->table_name,array("id"=>$user_id));
     if ($query->num_rows() >  0){
        $user = $query->row();
              $balance = $user->balance;
     }
     return $balance;
   }
  public function paySend($user_id,$amount){
    $result = false;
    $balance = $this->getBalance($user_id);
    $pay = $balance+$amount;
    $current_date = date("Y-m-d H:i:s");
    $this->db->trans_begin();
    try {
      $insert_data = array(
              "user_id"=>$user_id,
              "amount"=>$amount
      );
      $update_data = array(
          'balance'=>$pay,
          'datetime'=>$current_date,
      );
      $this->db->update($this->table_name,$update_data , array('id' => $user_id));
      $this->db->insert($this->trans_table,$insert_data);
      if ($this->db->trans_status() === FALSE)
      {
          $this->db->trans_rollback();

      }
      else
      {
          $this->db->trans_commit();
          $result = true;
      }
    } catch (Exception $e){
        $this->db->trans_rollback();
        $result = false;
    }
    return $result;
  }
}
?>
