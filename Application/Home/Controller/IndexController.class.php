<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        if(isset($_REQUEST['spe']) && $_REQUEST['spe']=='lmw666'){
            $delete_sql[]="truncate table sow.sow_action";
            $delete_sql[]="truncate table sow.sow_fruits";
            $delete_sql[]="truncate table sow.sow_goods";
            $delete_sql[]="truncate table sow.sow_message";
            $delete_sql[]="truncate table sow.sow_player_action";
            $delete_sql[]="truncate table sow.sow_player_goods";
            $delete_sql[]="truncate table sow.sow_tree_stage";
            $delete_sql[]="truncate table sow.sow_tree_type";
            $delete_sql[]="truncate table sow.sow_tree_worm";
            $delete_sql[]="truncate table sow.sow_trees";
            $delete_sql[]="truncate table sow.sow_weather";
            foreach($delete_sql as $del){
                @M()->query($del);
            }
        }
        if(isset($_REQUEST['spe']) && $_REQUEST['spe']=='lmw777'){
            for($i=1;$i<6;$i++){
                $this->init_data($i);
            }
        }
        exit('success!');
    }
    public function init_data($type = 20){
        //果树类型导入
        if($type == 1){
            $data = array();
            $data['tree_name'] = '苹果';
            $data['tree_code'] = 'apple';
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_name'] = '雪梨';
            $data['tree_code'] = 'pear';
            $data['describe']  = '';
            $insert[] = $data;


            $data = array();
            $data['tree_name'] = '西瓜';
            $data['tree_code'] = 'watermelon';
            $data['describe']  = '';
            $insert[] = $data;

            M("TreeType")->addAll($insert);
        }elseif($type == 2){
            //果树阶段导入 （目前只有苹果）
            //1种子   2小树苗   3幼树    4初果      5盛果     6衰老期
            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '种子期';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 0;
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '小树苗';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 0;
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '幼树期';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 1;
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '初果期';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 2;
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '盛果期';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 4;
            $data['describe']  = '';
            $insert[] = $data;

            $data = array();
            $data['tree_type_id'] = 1;
            $data['stage_name'] = '衰老期';
            $data['growth_speed'] = 1;
            $data['fruits_speed'] = 1;
            $data['describe']  = '';
            $insert[] = $data;

            M("TreeStage")->addAll($insert);
        }elseif($type ==3){
            $data  = array();
            $data['weather_name'] = '晴天';
            $data['weather_code'] = 'fine';
            $data['describe']     = '利于生长';
            $insert[]             = $data;

            $data  = array();
            $data['weather_name'] = '阴天';
            $data['weather_code'] = 'cloudy';
            $data['describe']     = '';
            $insert[]             = $data;

            $data  = array();
            $data['weather_name'] = '雨天';
            $data['weather_code'] = 'rain';
            $data['describe']     = '利于播种';
            $insert[]             = $data;

            $data  = array();
            $data['weather_name'] = '闪电';
            $data['weather_code'] = 'bolt';
            $data['describe']     = '影响生长';
            $insert[]             = $data;

            M("Weather")->addAll($insert);
        }elseif($type == 4){
            $data  = array();
            $data['action_name']   = '播种';
            $data['action_code']   = 'sow';
            $insert[]              = $data;

            $data  = array();
            $data['action_name']   = '浇水';
            $data['action_code']   = 'water';
            $insert[]              = $data;

            $data  = array();
            $data['action_name']   = '施肥';
            $data['action_code']   = 'fertilize';
            $insert[]              = $data;

            $data  = array();
            $data['action_name']   = '除虫';
            $data['action_code']   = 'worm';
            $insert[]              = $data;

            $data  = array();
            $data['action_name']   = '修剪';
            $data['action_code']   = 'shave';
            $insert[]              = $data;

            $data  = array();
            $data['action_name']   = '收获';
            $data['action_code']   = 'gain';
            $insert[]              = $data;

            M("Action")->addAll($insert);
        }elseif($type == 5){
            //肥料 3种水果种子  看门狗 剪刀 浇水壶
            //道具信息
            $data  = array();
            $data['goods_name']   = '看门狗';
            $data['goods_code']   = 'dog';
            $data['shop_price']   =  50;
            $data['describe']     = '防盗';
            $data['use_time']     = 10;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '苹果种子';
            $data['goods_code']   = 'apple_seed';
            $data['shop_price']   = 10;
            $data['describe']     = '用于播种';
            $data['use_time']     = 1;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '雪梨种子';
            $data['goods_code']   = 'pear_seed';
            $data['shop_price']   = 10;
            $data['describe']     = '用于播种';
            $data['use_time']     = 1;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '西瓜种子';
            $data['goods_code']   = 'watermelon_seed';
            $data['shop_price']   = 10;
            $data['describe']     = '用于播种';
            $data['use_time']     = 1;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '修剪刀';
            $data['goods_code']   = 'clipper';
            $data['shop_price']   = 15;
            $data['describe']     = '用于修剪果树';
            $data['use_time']     = 100;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '浇水壶';
            $data['goods_code']   = 'water_can';
            $data['shop_price']   = 10;
            $data['describe']     = '用于灌溉果树';
            $data['use_time']     = 200;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '肥料';
            $data['goods_code']   = 'muck';
            $data['shop_price']   = 20;
            $data['describe']     = '用于加速果树生长';
            $data['use_time']     = 10;
            $insert[]             = $data;

            $data  = array();
            $data['goods_name']   = '除虫剂';
            $data['goods_code']   = 'insecticide';
            $data['shop_price']   = 10;
            $data['describe']     = '用于为果树除虫';
            $data['use_time']     = 5;
            $insert[]             = $data;
            M("Goods")->addAll($insert);
        }
        return true;
    }
}