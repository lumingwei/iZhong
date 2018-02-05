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
    public function get_play_action(){
        $act_list   = M("Action")->select('action_id,action_code,action_name');
        $goods_list = M("PlayGoods")->where(['play_id'=>$this->play_id,'use_time'=>['gt'=>0]])->getField('goods_id,goods_code');
/*        $action_goods = [
            'sow'       => array('apple_seed','pear_seed','watermelon_seed'),
            'water'     => array('water_can'),
            'fertilize' => array('muck'),
            'worm'      => array('insecticide'),
            'shave'     => array('clipper'),
        ];*/
        $goods_action = [
            'apple_seed'      => 'sow',
            'pear_seed'       => 'sow',
            'watermelon_seed' => 'sow',
            'water_can'       => 'water',
            'muck'            => 'fertilize',
            'insecticide'     => 'worm',
            'clipper'         => 'shave'
        ];
        $ag  = array();
        if(!empty($act_list) && !empty($goods_list)){
            foreach($goods_list as $v){
                $ag[$goods_action[$v['goods_code']]][] = $v;
            }
            foreach($act_list as $k =>$v){
                $act_list[$k]['goods_list'] = isset($ag[$v['action_code']])?:array();
            }
        }
        $this->json_return($act_list);
    }
}