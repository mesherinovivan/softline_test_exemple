<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/// Класс логирования встроенный в CI позволяет писать логи в файл, использовал его)
class Integration extends CI_Controller {

  function __construct(){
            parent::__construct();
            $this->load->model('user_model','user');
  }
  function __validateParams($a,$b,$md5,$solt){
           $result = false;
           ///echo md5($a.$b.$solt);
           if (md5($a.$b.$solt) === $md5){
              $result = true;
           }
           return $result;
  }

  function __savePay($user_id,$amount,$md5,$solt){
    $result = false;

    if ($this->__validateParams($user_id,$amount,$md5,$solt)){
      /// будем считать что мы передаем корректные значения
      /// а не злобные инекции.

         $search = $this->user->searchUser($user_id);
         if ($search != NULL){
             $is_pay = $this->user->paySend($user_id,$amount);
             if ($is_pay){
               $result = true;
               log_message('info',
                          sprintf("Оплата на сумму %f от пользователя %s получена!"
                          ,$amount,$user_id));
             }
             else{
               log_message('error',
                          sprintf("Не удалось принять оплату от пользователя %d,
                                  на сумму %f, ошибка обновления записи в БД!"
                          ,$user_id,$amount));
             }
         }
         else{
           log_message('error',
                      sprintf("Пользователь с id %s не найден в БД!"
                      ,$user_id));
         }
       }
       else{
         log_message('error',
                    sprintf("Не корректные параметры запроса id= %s, amount = %s , invalid md5=%s
                    valid md5 = %s"
                    ,$user_id,$amount,$md5,md5($user_id.$amount.$solt)));
      }
    return $result;
  }
	public function index(){
		$this->load->view('payments');
	}

	public function translist(){
    $username = $this->input->get("username");
    $data = $this->user->listTrans($username);
    echo json_encode($data) ;
  }


  /// обработчик первого провайдера
  /// url http://localhost/index.php/integration/provider1/<user_id>/<amount>/<md5>
  public function provider1($a=0,$b=0,$md5=0){
    $response = "<answer>1<answer>";
    /* если принципиально нужно через GET[] то просто надо
     $a = $this->input->get("a"),
     $b = $this->input->get("b"),
     $md5 = $this->input->get("md5");
     и url тогда меняется на
     http://localhost/index.php/integration/provider1/?a=<user_id>&b=<amount>&md5=<md5>
     можно конечно еще и обработчик один сделать, проверять какие параметры передались
     и в зависимости от них выбирать нужные ответ, но вообще бы конечно передавать еще и какой нибудь
     id платежной системы, так было бы проще.
    */
    $solt = $this->config->item("SOLT1");
    $result = $this->__savePay($a,$b,$md5,$solt);
    if (!$result){
       $response = "<answer>0<answer>"; 
    }
    /// можно конечно вывести в шаблон по людски, но ибо обрабатывать ответ не надо
    /// и это тестовое задание, можно просто вывести так думаю
    echo $response;
  }
  /// обработчик второго провайдера
  /// url http://localhost/index.php/integration/provider2/<user_id>/<amount>/<md5>
  public function provider2($x=0,$y=0,$md5=0){
    /// если принципиально нужно через GET[] то просто надо
    // заменить на строковое представление а не ЧПУ
    $response = "OK";
    $solt = $this->config->item("SOLT2");
    $result = $this->__savePay($x,$y,$md5,$solt);
    if (!$result){
       $response = "ERROR";
    }
    /// можно конечно вывести в шаблон по людски, но ибо обрабатывать ответ не надо
    /// и это тестовое задание, можно просто вывести так думаю
    echo  $response;

  }
}
