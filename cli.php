<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
/* 各个环境配置文件加载方式：
 * 注意：DP_ 为项目缩写（按项目自定义）
 * DP_ENVIRONMENT_MAIN = develop 是开发环境 
 * DP_ENVIRONMENT_MAIN = test 是测试环境
 * DP_ENVIRONMENT_MAIN = grey 是灰度环境
 * DP_ENVIRONMENT_MAIN = product 是生产环境
 */

if(php_sapi_name() !== 'cli'){
	exit();
}
define('APP_MODE','develop');  //加载config_develop.php
define( 'APP_PATH', dirname(__FILE__).'/Application/' );
define('APP_URL', isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'] : '');
// 引入ThinkPHP入口文件
//require './ThinkPHP/ThinkPHP.php';
require dirname( __FILE__).'/ThinkPHP/ThinkPHP.php';
