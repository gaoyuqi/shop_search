<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * 搜索商品
 */

require 'vendor/autoload.php';
use Medoo\Medoo;

include("utils.php");

$redis = new Redis();
$redis->pconnect('192.168.1.101', 6379, 1);//长链接，本地host，端口为6379，超过1秒放弃链接
date_default_timezone_set('PRC');

$database = new Medoo(
  ['database_type' => 'mysql',
    'database_name' => 'shop_search',
    'server' => '192.168.0.113',
    'username' => 'root',
    "charset" => "utf8",
    'password' => '88888888']
);

//用户输入的短语
$input_str = "2018秋冬女装新品XZOO";

echo "<h1>搜索词: $input_str</h1>";

$start_time = explode(' ',microtime());
echo "<h1>开始时间: ".date("Y-m-d H:i:s",$start_time[1]).":".$start_time[0]."</h1>";

//根据空格切分 输入
$arr = explode(" ",$input_str);

$all_words = [];

foreach($arr as $item){
  $all_words = array_merge(pullword($item,$redis),$all_words);
}

echo "<h1>分词结果: </h1>";
echo "<h2>";
foreach($all_words as $item){
  echo $item.' | ';
}
echo "</h2>";
echo "\r\n";

//todo,生成唯一的查询ID,返回给客户端,保存在cookie,分页可以使用
$search_id = 12345;

$words_unm = count($all_words);

//todo,如果拆分不到词,退出
if( 0 == $words_unm ){
  echo "匹配不到商品";
  exit;
}

$new_set = [];
//只要有一个集合为空,就是搜不到商品
foreach($all_words as $item){
  $word_godds_set = "word_".$item."_goods";
  $key_exist = $redis->keys($word_godds_set);
  if(!$key_exist){
    echo "匹配不到商品";
    exit;
  }
}

//如果只有一个词,不用求交集
if( 1 == $words_unm ){
  $search_set = "word_".$all_words[0]."_goods";
}else{
  $search_set = "search_set_".$search_id;
  //循环求出每个词关联的商品ID 的交集
  for( $i=0; $i < $words_unm ;$i+=2 ){
    //如果下一个元素存在
    if( empty($all_words[$i+1]) ){
      $word_godds_set_1 = "word_".$all_words[$i]."_goods";
      $redis->sInterStore($search_set,$word_godds_set_1,$search_set);
    }else{
      $word_godds_set_1 = "word_".$all_words[$i]."_goods";
      $word_godds_set_2 = "word_".$all_words[$i+1]."_goods";
      $redis->sInterStore($search_set,$word_godds_set_1,$word_godds_set_2);
    }
  }
}

/*
 * 程序走到这里, $search_set 就是 搜索结果了,也就是相关商品ID的集合.
 * 如有需要,希望通过店铺ID对这个集合再次过滤,可以利用 load_shop_info_to_redis.php 的程序,再次求交集
 * */

//todo,用 sort 命令 把 $search_set 排序 ,然后把排序结果缓存进list结构,然后就可以进行分页查询了.

$result = $redis->sMembers($search_set);

echo "<h1>相关商品ID:</h1>";

echo "<h2>";
for( $i=0;$i<10;$i++ ){
  echo $result[$i],'   '.','.'   ';
}
echo "........";
echo "</h2>";

echo "<h1>搜索完成</h1>";
$end_time = explode(' ',microtime());
echo "<h1>结束时间: ".date("Y-m-d H:i:s",$end_time[1]).":".$end_time[0]."</h1>";


exit;






