<?php
/**
 *
 * 城市管理
 * @author lmw
 *
 */
namespace Home\Controller;
use Think\Controller;
class CityController extends BaseController {
    //城市管理页面
    public function index(){
        $return = [
            'city_base' => array(),
            'own_city'  => array()
        ];
        if(empty($_SESSION['player_id'])){
            exit('尚未登录!');
        }
        if(empty($_REQUEST['city_id'])){
            exit('参数异常!');
        }
        $city_ids         = array();
        $city_id          = isset($_REQUEST['city_id'])?intval($_REQUEST['city_id']):0;
        $player_city_ids  = M("city")->where(['player_id'=>$_SESSION['player_id']])->getField('city_id',true);
        $city_ids[]       = $city_id;
        if(!empty($player_city_ids)){
            $city_ids = array_merge($city_ids,$player_city_ids);
        }
        //获取玩家所有的城市
        $city_list = M("City")->where(['city_id'=>['in',$city_ids]])->order('level desc')->select();
        if(!empty($city_list)){
            foreach($city_list as $city){
                foreach($city as $k => $v){
                    if($k == 'city_id'){
                        continue;
                    }elseif($k == 'strategy_id'){
                        $key_cn = $this->getStrategyCn($v);
                    }elseif($k == 'scale'){
                        $key_cn = $this->getScaleCn($v);
                    }else{
                        $key_cn = $this->getCityCn($k);
                    }
                    if($v['city_id'] == $city_id){
                        $return['city_base']   =[
                            'key_cn'  => $key_cn,
                            'val'  => $v,
                            'key_code'  => $k,
                        ];
                    }else{
                        $return['own_city'][]  =[
                            'key_cn'  => $key_cn,
                            'val'  => $v,
                            'key_code'  => $k,
                        ];
                    }
                }
            }
        }
        $this->json_return($return);
    }
    protected function getCityCn($filed_name = ''){
        $cn = [
            'strategy_id'=>'治理方略',
            'city_name'  =>'城市名称',
            'support'    =>'支持率',
            'prestige'   =>'声望',
            'population' =>'人口',
            'yukio'      =>'军用民夫',
            'recruit'    =>'新兵',
            'forces'     =>'兵力',
            'gold'       =>'黄金',
            'foods'      =>'粮食',
            'wood'       =>'木材',
            'stone'      =>'石头',
            'iron'       =>'铁',
            'knife'      =>'刀',
            'pike'       =>'枪',
            'bow'        =>'弓箭',
            'sword'      =>'剑',
            'level'      =>'等级',
            'scale'      =>'规模',
        ];
        return isset($cn[$filed_name])?$cn[$filed_name]:'';
    }
    protected function getStrategyCn($strategy_id = 0){
        $cn = [
            1    =>'修养生息',
            2    =>'秣兵历马 ',
            3    =>'中庸之道'
        ];
        return isset($cn[$strategy_id])?$cn[$strategy_id]:'无';
    }
    protected function getScaleCn($scale = 1){
        $cn = array(
            1=>'乡镇',
            2=>'县城',
            3=>'郡城',
            4=>'州城',
            5=>'王城',
            6=>'帝都',
        );
        return isset($cn[$scale])?$cn[$scale]:'';
    }
}