<?php
namespace Home\Controller;
use Think\Controller;
use Util\RndChinaName;
class PlayerController extends BaseController {
   public function create_play(){
       $name_obj = new rndChinaName();
       $name = $name_obj->getName(2);
       $data = $insert = array();
       $data['name'] = $name;
       $data['pwd'] = '123456';
       $data['money'] = 30;
       $data['apple']  = 0;
       $data['pear']  = 0;
       $data['watermelon']  = 0;
       $data['add_time']  = time();
       $data['describe']  = '';
       $insert[] = $data;
       $res = M("Player")->addAll($insert);
       if(!empty($res)){
           $give_goods = [
               'apple_seed' => 1,
               'water_can'  => 2
           ];
           $search_goods = array_keys($give_goods);
           $goods_list   = M("Goods")->where(['goods_code'=>['in',$search_goods]])->getField('goods_code,use_time');
           $insert       = array();
           foreach($give_goods as $k => $v){
               for($i=0;$i<$v;$i++){
                   if(isset($goods_list[$k])){
                       $data = array();
                       $data['player_id'] = $name;
                       $data['goods_code'] = $k;
                       $data['use_time']   = $goods_list[$k];
                       $data['add_time']   = time();
                       $insert[] = $data;
                   }
               }
           }
           $ress = M("PlayerGoods")->addAll($insert);
       }
       if(!empty($ress)){
           $this->json_return();
       }else{
           $this->json_return(array(),1,'failed');
       }
   }
}