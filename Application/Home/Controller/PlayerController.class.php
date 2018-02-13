<?php
namespace Home\Controller;
use Think\Controller;
use Util\RndChinaName;
class PlayerController extends BaseController {
   public function create_play(){
       $name_obj = new rndChinaName();
       $name = $name_obj->getName(2);
       $data  = array();
       $data['name'] = $name;
       $data['pwd'] = '123456';
       $data['money'] = 30;
       $data['apple']  = 0;
       $data['pear']  = 0;
       $data['watermelon']  = 0;
       $data['add_time']  = time();
       $data['describe']  = '';
       $play_id = M("Player")->add($data);
       if(!empty($play_id)){
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
                       $data['player_id'] = $play_id;
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
               $data['player_id']   = $play_id;
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
        $act_list   = M("Action")->field('action_id,action_code,action_name')->select();
        $goods_list = M("PlayerGoods")->where(['player_id'=>$this->player_id,'use_time'=>['gt',0]])->getField('pg_id,goods_code,use_time',true);
/*        $action_goods = [
            'sow'       => array('apple_seed','pear_seed','watermelon_seed'),
            'water'     => array('water_can'),
            'fertilize' => array('muck'),
            'worm'      => array('insecticide'),
            'shave'     => array('clipper'),
        ];*/
        $ag  = array();
        if(!empty($act_list) && !empty($goods_list)){
            foreach($goods_list as $v){
                $ag[$this->goods_action[$v['goods_code']]][] = $v;
            }
            foreach($act_list as $k =>$v){
                $act_list[$k]['goods_list'] = isset($ag[$v['action_code']])?$ag[$v['action_code']]:array();
            }
        }
        $this->json_return($act_list);
    }

    public function get_message(){
        $pn = !empty($_REQUEST['page_num'])?intval($_REQUEST['page_num']):1;
        $ps = !empty($_REQUEST['page_size'])?intval($_REQUEST['page_size']):10;
        $message_list   = M("PlayerMessage")->where(['play_id'=>$this->player_id])->page($pn, $ps)->order('id desc')->select();
        $total_count    = M("PlayerMessage")->where(['play_id'=>$this->player_id])->count();
        $total_page     = !empty($total_count)?ceil($total_count/$ps):0;
        $message_list   = !empty($message_list)?$message_list:array();
        $res = array(
            'list'=>$message_list,
            'page_num'=>$pn,
            'page_size'=>$ps,
            'total_count'=>$total_count,
            'total_page'=>$total_page
        );
        $this->json_return($res);
    }

    public function action()
    {
        $pg_id       = !empty($_REQUEST['pg_id'])       ? intval($_REQUEST['pg_id'])    : 0;
        $tree_id     = !empty($_REQUEST['tree_id'])     ? intval($_REQUEST['tree_id'])  : 0;
        if(empty($pg_id)){
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

        $action_code = !empty($this->goods_action[$goods['goods_code']])? $this->goods_action[$goods['goods_code']] : '';
        $cd  = M("PlayerAction")->where(['player_id'=>$this->player_id,'action_code'=>$action_code])->getField('cd');
        if($action_code != 'sow' && empty($tree_id)){
            $this->json_return(array(),1,'参数缺失!');
        }
        if(empty($cd)){
            switch($action_code){
                case 'sow':
                    $has_tree = M("Trees")->where(['player_id'=>$this->player_id])->getField('tree_id');
                    if(!empty($has_tree)){
                        $this->json_return(array(),3,'您已经有果树了,无需再播种了~');
                    }else{
                        $data  = array();
                        $data['player_id'] = $this->player_id;
                        M("Trees")->add($data);
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

    public function shop()
    {
       $goods_list = M('Goods')->select();
       empty($goods_list) && $goods_list = array();
       $this->json_return($goods_list);
    }

    public function buy(){
        $buy_num       = !empty($_REQUEST['buy_num'])      ? intval($_REQUEST['buy_num'])   : 1;
        $goods_id      = !empty($_REQUEST['goods_id'])     ? intval($_REQUEST['goods_id'])  : 0;
        $now           = time();
        if(empty($goods_id) || empty($buy_num)){
            $this->json_return(array(),1,'参数缺失!');
        }
        $goods = M('Goods')->where(['goods_id'=>$goods_id])->find();
        $money = M('Player')->where(['id'=>$this->player_id])->getField('money');
        $money = intval($money);
        if(!empty($goods)){
            if($money<$goods['shop_price']*$buy_num){
                $this->json_return(array(),1,'金币不足!');
            }
            $insert                 = array();
            for($i=0;$i<$buy_num;$i++){
                $data               = array();
                $data['player_id']  = $this->player_id;
                $data['goods_code'] = $goods['goods_code'];
                $data['use_time']   = $goods['use_time'];
                $data['add_time']   = $now;
                $insert[]           = $data;
            }
            M("PlayerGoods")->addAll($insert);
        }else{
            $this->json_return(array(),1,'商品不存在!');
        }
        $this->json_return(array(),0,'交易成功!');
    }

    public function player_info(){
        $player_info = M("Player")->where(['player_id'=>$this->player_id])->find();
        $this->json_return($player_info);
    }
}