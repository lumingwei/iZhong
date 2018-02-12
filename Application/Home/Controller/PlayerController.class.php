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

           $action_goods = [
            'sow'       => array('apple_seed','pear_seed','watermelon_seed'),
            'water'     => array('water_can'),
            'fertilize' => array('muck'),
            'worm'      => array('insecticide'),
            'shave'     => array('clipper'),
           ];
           $insert       = array();
           foreach($action_goods as $k=>$v){
               $data = array();
               $data['player_id']   = $name;
               $data['action_code'] = $k;
               $data['cd']          = 0;
               $insert[] = $data;
           }
           M("PlayerAction")->addAll($insert);
       }
       if(!empty($ress)){
           $this->json_return();
       }else{
           $this->json_return(array(),1,'failed');
       }
   }
    public function get_play_action(){
        $act_list   = M("Action")->select('action_id,action_code,action_name');
        $goods_list = M("PlayGoods")->where(['play_id'=>$this->player_id,'use_time'=>['gt'=>0]])->getField('goods_id,goods_code');
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

    public function get_message(){
        $message_list   = M("PlayerMessage")->where(['play_id'=>$this->player_id])->order('id desc')->select();
        !empty($message_list) && $message_list = array();
        $this->json_return($message_list);
    }

    public function action()
    {
        $pg_id       = !empty($_REQUEST['pg_id'])       ? intval($_REQUEST['pg_id']) : '';
        $tree_id     = !empty($_REQUEST['tree_id'])     ? intval($_REQUEST['tree_id'])  : '';

        if(empty($pg_id) || empty($tree_id)){
            $this->json_return(array(),1,'参数缺失!');
        }

        $goods = M("PlayerGoods")->where(['pg_id'=>$pg_id,'player_id'=>$this->player_id])->find();
        if(!empty($goods)){
           if(empty($goods['use_time'])){
               $this->json_return(array(),1,'物品不可用!');
           }
        }else{
            $this->json_return(array(),1,'物品不存在!');
        }
        $action_code = !empty($goods['action_code'])? $goods['action_code'] : '';
        $cd  = M("PlayerAction")->where(['player_id'=>$this->player_id,'action_code'=>$action_code])->getField('cd');
        if(empty($cd)){
            switch($action_code){
                case 'sow':
                    $has_tree = M("Trees")->where(['player_id'=>$this->player_id])->getField('tree_id');
                    if(!empty($has_tree)){
                        $this->json_return(array(),3,'您已经有果树了,无需再播种了~');
                    }
                    break;
                case 'water':
                    M("Trees")->where(['tree_id'=>$tree_id])->setInc('age', 10);
                    M("PlayerAction")->where(['player_id'=>$this->player_id,'action_code'=>$action_code])->setInc('cd', 10);
                    break;
                case 'fertilize':
                    M("Trees")->where(['tree_id'=>$tree_id])->setInc('age', 300);
                    M("PlayerAction")->where(['player_id'=>$this->player_id,'action_code'=>$action_code])->setInc('cd', 1800);
                    break;
                case 'worm':
                    $has_worm = M("TreeWorm")->where(['tree_id'=>$tree_id,'life_time'=>['gt',0]])->getField('id');
                    if(!empty($has_worm)){
                        if(rand(1,10)<=8){
                            $this->json_return(array(),5,'除虫成功!');
                        }else{
                            $this->json_return(array(),5,'除虫失败!');
                        }
                        M("TreeWorm")->where(['tree_id'=>$tree_id,'life_time'=>['gt',0]])->setField('life_time',0);
                    }else{
                        $this->json_return(array(),5,'并没有害虫!');
                    }
                    M("PlayerAction")->where(['player_id'=>$this->player_id,'action_code'=>$action_code])->setInc('cd', 1800);
                    break;
            }
        }else{
            $this->json_return(array(),4,'操作尚未冷却!');
        }
        M('PlayerGoods')->where(['pg_id'=>$pg_id])->setDec('use_time',1);
        $this->json_return();
    }

}