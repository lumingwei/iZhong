<?php
namespace Home\Controller;
use Think\Controller;
class TreesController extends BaseController {
    /**
     *计划任务：
     *一：维护果树的生长速度
     *二：维护果树的结果速度
     *三：维护果树的加成速度（使用物品 || 害虫）
     **/
    public function synGrowth()
    {
        $this->log(date('Y-m-d H:i:s',time())."start");
        //echo date('Y-m-d H:i:s',time())."start \n";
        $this->trees_growth();
        $this->fruits_growth();
        $this->player_cd();
        //echo date('Y-m-d H:i:s',time()).'end';
        $this->log(date('Y-m-d H:i:s',time()).'end');
        return true;
    }

    public function log($msg, $level = 'INFO')
    {
        $msg = sprintf('[%s][%s]:%s', CONTROLLER_NAME, ACTION_NAME, $msg);

        echo $msg . PHP_EOL;
        \Think\Log::write($msg, $level);
    }
    //每五秒钟执行一次 技能冷却时间调控
    public function player_cd(){
        M("PlayerAction")->where(['cd'=>['egt',60]])->setDec('cd',60);
        M("PlayerAction")->where(['cd'=>['lt',60]])->setField('cd',0);
    }
    protected function trees_growth(){
        $now          =  time();
        M("Trees")->where([])->setInc('age',10); // 生长值 + 10   gg写法 M("Trees")->setInc('age',10);
        M("Trees")->where(['stage_id'=>['in',[3,6]]])->setInc('fruits_age',10); // 结果值 + 10
        M("Trees")->where(['stage_id'=>4])->setInc('fruits_age',20); // 结果值 + 20
        M("Trees")->where(['stage_id'=>5])->setInc('fruits_age',40); // 结果值 + 40


        //1-->2  种子-->树苗
        M("Trees")->where(['stage_id'=>1,'age'=>['egt',86400]])->setField('stage_id',2);
        //2-->3  树苗-->幼树
        M("Trees")->where(['stage_id'=>2,'age'=>['egt',172800]])->setField('stage_id',3);
        //3-->4  幼树-->初果
        M("Trees")->where(['stage_id'=>3,'age'=>['egt',259200]])->setField('stage_id',4);
        //4-->5  初果-->盛果
        M("Trees")->where(['stage_id'=>4,'age'=>['egt',345600]])->setField('stage_id',5);
        //5-->6  盛果-->衰老
        M("Trees")->where(['stage_id'=>5,'age'=>['egt',432000]])->setField('stage_id',6);

        //结果
        $born_fruit_trees = M("Trees")->where(['fruits_age'=>['egt',86400]])->getField('tree_id',true);
        if(!empty($born_fruit_trees)){
            $insert                 = array();
            foreach($born_fruit_trees as $tid){
                $data               = array();
                $data['tree_id']    = $tid;
                $data['born_time']  = $now;
                $data['status']     = 1;
                $insert[]           = $data;
            }
            M("Fruits")->addAll($insert);
        }
        M("Trees")->where(['fruits_age'=>['egt',86400]])->setField('fruits_age',0);
        return true;
    }

    //1：未成熟|未收获 2：成熟|未收获  3：成熟|已收获 4：糜烂|未收获 5：糜烂|已收获
    protected function fruits_growth(){
        $now = time();
        M("Fruits")->where(['status'=>['elt',3]])->setInc('age',10); // 生长值 + 10
        //1-->2  未成熟|未收获-->成熟|未收获
        //M("Fruits")->where(['status'=>1,'age'=>['egt',7200]])->setField('status',2);
        M("Fruits")->where(['status'=>1,'age'=>['egt',7200]])->save(['status'=>2,'ripe_time'=>$now]);
        //2-->4  成熟|未收获-->糜烂|未收获
        //M("Fruits")->where(['status'=>2,'age'=>['egt',18000]])->setField('status',4);
        M("Fruits")->where(['status'=>2,'age'=>['egt',18000]])->setField(['status'=>4,'die_time'=>$now]);
        //3-->5  成熟|已收获-->糜烂|已收获
        //M("Fruits")->where(['status'=>3,'age'=>['egt',604800]])->setField('status',5);
        M("Fruits")->where(['status'=>3,'age'=>['egt',604800]])->setField(['status'=>5,'die_time'=>$now]);
        return true;
    }

    public function get_message_type()
    {
       $neutral = array(
           1    => '明天有小雨哦~',
           2    => '明天有大雨哦~',
           3    => '明天有狂风暴雨哦~'
       );
       $good    = array(
           1    => '你获得了一颗种子~',
           2    => '你获得一瓶除虫剂~',
           3    => '你获得一袋肥料~'
       );
       $bad     = array(
           1    => '果树长害虫了，生长速度受到影响~',
           2    => '健业偷了你一个果实~',
           3    => '小狗受到电击，呜呜呜~'
       );
       $message = array(
           1    => $neutral,
           2    => $good,
           3    => $bad
       );
      return $message;
    }

}