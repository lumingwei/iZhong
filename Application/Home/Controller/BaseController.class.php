<?php
/**
* 基类
* @author lmw
* date 2018-02-02
*/
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller
{
    protected $player_id = 1;
    protected $goods_action = [
        'apple_seed'      => 'sow',
        'pear_seed'       => 'sow',
        'watermelon_seed' => 'sow',
        'water_can'       => 'water',
        'muck'            => 'fertilize',
        'insecticide'     => 'worm',
        'clipper'         => 'shave'
    ];
    public function  __construct()
    {
        parent::__construct();
    }
    public function json_return($data = array() , $code = 0 ,$msg = 'success'){
         $return = array('data'=>$data,'code'=>$code,'msg'=>$msg);
         $this->showJsonResult($return);
    }
    public function showJsonResult($data){
        header( 'Content-type: application/json; charset=UTF-8' );
        if (isset( $_REQUEST['callback'] ) ) {
            echo htmlspecialchars( $_REQUEST['callback'] ) , '(' , json_encode( $data ) , ');';
        } else {
            echo json_encode( $data, JSON_UNESCAPED_UNICODE );
        }

        die();
    }
}