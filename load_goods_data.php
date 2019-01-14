<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * //加载 模拟的 商品 跟 店铺信息进数据库
 */
require 'vendor/autoload.php';
use Medoo\Medoo;
include("utils.php");


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

$goods_array = [];
array_push($goods_array,['name'=>"洛芙缇2018秋冬装新款 冬季大码显瘦时尚百搭加绒长袖连衣裙"]);
array_push($goods_array,['name'=>"雪莲2018秋冬新款中长款纯色圆领羊绒女裙"]);
array_push($goods_array,['name'=>"菲梦伊2018假两件连衣裙女秋冬新款针织网布贴花上衣网纱半身裙打底裙"]);
array_push($goods_array,['name'=>"波菲熊针织连衣裙长袖2018秋冬装新款女装中长款韩版时尚套装女两件套套装裙毛衣女宽松高领网纱打底裙"]);
array_push($goods_array,['name'=>"果妍针织连衣裙 秋冬季女装2018新款长袖千鸟格中长款气质裙"]);
array_push($goods_array,['name'=>"忆思暮春装女装连衣裙2018秋冬新款大码女装韩版修身显瘦性感中长款时尚套装女蕾丝针织女长袖毛呢连衣裙"]);
array_push($goods_array,['name'=>"德媛连衣裙长袖2018秋冬新款半高领针织打底衫+亮丝V领背心套装女毛衣网红两件套套装裙"]);
array_push($goods_array,['name'=>"FEMLY法曼丽 长袖秋装连衣裙职业OL2018新款半开领修身提花裙女裙 宝蓝色"]);
array_push($goods_array,['name'=>"桜泣高端套装裙子女2018秋冬装圆领新款时尚气质韩版长袖修身中长款针织毛衣连衣裙两件套潮2019春"]);
array_push($goods_array,['name'=>"芭米欣长袖连衣裙时尚套装女2018秋冬新款女装韩版套头毛衣中长款高领针织连衣裙过膝打底长裙"]);
array_push($goods_array,['name'=>"北极绒针织连衣裙女2018秋冬季新款修身高领毛衣裙长袖裙加厚打底子中长款连衣裙毛衣外套"]);
array_push($goods_array,['name'=>"择调连衣裙2018秋冬新款女装秋季新款韩版秋装女时尚套装女士性感蕾丝长袖针织连衣裙两件套套装裙子"]);
array_push($goods_array,['name'=>"毛呢大衣女中长款外套女2018秋冬季赫本风流行新品中长款大码女装宽松显瘦呢子女冬装 波斯菲儿"]);
array_push($goods_array,['name'=>"迪仕霸新品秋装连衣裙女加厚2018秋冬新款女装韩版休闲修身中长款大码套装长袖卫衣女假两件套套装裙"]);
array_push($goods_array,['name'=>"波菲熊毛呢连衣裙长袖2018秋冬装新款时尚套装女韩版中长款过膝气质修身显瘦针织打底毛衣两件套套装裙"]);
array_push($goods_array,['name'=>"慕伊卡针织连衣裙2018秋冬季新款大码女装韩版修身时尚套装女假两件裙子中长款套头毛衣女长袖打底连衣裙"]);
array_push($goods_array,['name'=>"芭米欣长袖连衣裙时尚套装女2018秋冬季新款女装韩版宽松高领中长款毛衣裙假两件套针织打底裙"]);
array_push($goods_array,['name'=>"XZOO针织连衣裙2018秋冬女装新品套装针织毛衣裙子两件套套装女"]);
array_push($goods_array,['name'=>"连衣裙2018秋冬新款女装秋天长袖秋装女韩版修身时尚裙子套装女"]);
array_push($goods_array,['name'=>"比凯瑞棉服女中长款2018秋冬新款女装韩版修身时尚棉袄棉衣女外套女冬"]);
array_push($goods_array,['name'=>"芳帛针织连衣裙长裙打底裙女2018秋冬新款中长款毛衣裙毛线裙过膝加厚"]);
array_push($goods_array,['name'=>"恰凡针织衫女2018秋冬装新款高领加厚毛衣打底衫女长袖套头韩版宽松针织打底衫"]);
array_push($goods_array,['name'=>"婩泺儿女装针织打底连衣裙长袖针织衫女2018秋冬新款女装韩版套头毛衣女中长款裙子秋冬加厚毛衣裙打底衫"]);
array_push($goods_array,['name'=>"裳黎连衣裙2018秋冬女新款宽松长袖高领中长款针织打底衫毛衣裙子女冬款"]);
array_push($goods_array,['name'=>"袁素 半高领毛衣女套头针织衫女加厚宽松韩版2018秋冬新款学生潮针织衫套头拼色打底衫女士外搭外套"]);
foreach($goods_array as $key=>$item){
  $input_str = $item['name'];

  //根据空格切分 输入
  $arr = explode(" ",$input_str);

  $all_words = [];

  foreach($arr as $item){
    $all_words = array_merge(pullword($item,$redis),$all_words);
  }
  $goods_array[$key]['words'] = $all_words;
}

//超小数据集
//$shop_num = 2;
//$shop_goods_num = 25;

//小数据集1万商品，每家店铺500个商品,20家店铺
//$shop_num = 20;
//$shop_goods_num = 500;

//中数据集10万商品，每家店铺1千个商品,100家店铺
$shop_num = 100;
$shop_goods_num = 1000;

//大数据集
//100万商品，每家店铺5千个商品,200家店铺
//$shop_num = 200;
//$shop_goods_num = 5000;

for($i = 20; $i < $shop_num; $i++){
  $shop_id = $i + 1;

  for($j = 0; $j < ($shop_goods_num/count($goods_array)); $j++){
    foreach($goods_array as $item){

      $goods = ['goods_name' => $item['name'],'shop_id'=>$shop_id];
      $database->insert('goods',$goods);
      $goods_id = $database->id();

      $goods_word_base = ['goods_id' => $goods_id,'shop_id'=>$shop_id];
      //循环获取批量数据
      $goods_word_arr = [];

      foreach($item['words'] as $value){

        $tmp_arr = $goods_word_base;
        $tmp_arr['word_id'] = $database->get('word',"word_id",['word'=>$value]);
        array_push($goods_word_arr,$tmp_arr);

      }
      $database->insert('goods_word',$goods_word_arr);

    }
  }

}


exit;


