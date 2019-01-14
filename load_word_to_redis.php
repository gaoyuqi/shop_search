<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * 加载数据库词条数据进redis集合
 */
require 'vendor/autoload.php';
use Medoo\Medoo;

$redis = new Redis();
$redis->pconnect('192.168.1.101', 6379, 1);//长链接，本地host，端口为6379，超过1秒放弃链接

$database = new Medoo(
  ['database_type' => 'mysql',
    'database_name' => 'shop_search',
    'server' => '192.168.0.113',
    'username' => 'root',
    "charset" => "utf8",
    'password' => '88888888']
);

//遍历读取 word 的词条数据

//查询出总条数
$count = $database->count('word');
//每次取10000条
$page_size = 1000;

$current = 0;

$set_name = "word_set";

for($current = 0; $current < $count; $current += $page_size){
  $word_arr = $database->select("word","*",["LIMIT" => [$current,$page_size]]);
  foreach($word_arr as $item){
    $redis->sAdd($set_name,$item['word']);
  }
}


exit;


