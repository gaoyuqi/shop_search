<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * 把词条跟商品的关系 存进redis集合,
 * 数据结构是这样的, 集合名称 word_(词条)_goods ,集合内的元素是商品ID
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


//无限循环,读到表末尾才放弃
$current = 0;
for(;;){
  //遍历读取 word 的词条数据

  $word_info = $database->select("word",['word_id','word'],["LIMIT" => [$current,1]]);
  //读完词条退出
  if( !$word_info ){
      break;
  }else{
    $word_info = $word_info[0];
  }
  $word_id = $word_info['word_id'];

  //查询出这个词关联的商品有多少个
  $count = $database->count('goods_word',['word_id'=>$word_id]);


  //每次取10000条
  $page_size = 1000;
  for($_current = 0; $_current < $count; $_current += $page_size){
    $word_arr = $database->select("goods_word",['goods_id'],["LIMIT" => [$_current,$page_size],'word_id'=>$word_id]);

    $goods_id_arr = [];
    //获取goods_id 的一维数组
    foreach($word_arr as $item){
      $goods_id_arr[] = $item['goods_id'];
    }

    $set_name = "word_".$word_info['word']."_goods";

    var_dump($set_name);
    var_dump($goods_id_arr);

    //$redis->sAdd($set_name,...$goods_id_arr);

  }

  $current++;

}

exit;


