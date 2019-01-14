<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * 加载数据库词条数据进redis集合,采用首字符做前缀hash,也就是redis集合.
 * 例如, 女 作为集合的名称."女人","女式","女" 等词条作为集合的元素
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
$count = $database->count('goods_word');
//每次取10000条
$page_size = 1000;

$current = 0;

//redis 词条 集合前缀名称 word_set_女
$prefix_name = "shop_";

for($current = 0; $current < $count; $current += $page_size){
  $arr = $database->select("goods_word",['relative_id','goods_id','shop_id'],["LIMIT" => [$current,$page_size]]);
  foreach($arr as $item){
    $redis_set_name = $prefix_name.$item['shop_id'];
    $redis->sAdd($redis_set_name,$item['goods_id']);
  }
  exit;
}


exit;


