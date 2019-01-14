<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 12/01/2019
 * Time: 3:43 PM
 * //加载dict 目录下的字典进mysql数据表.
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

function load_dict_txt($dict_txt){
  global $database;
  $charset = "UTF-8";

  $word_file = @fopen($dict_txt, 'r');
  if(!$word_file){
    return 'file open fail';
  }else{
    while(!feof($word_file)){
      $word = fgets($word_file);
      $clean_word = str_replace(array("\r\n", "\r", "\n"), '', $word);
      if( empty($clean_word) ){
         //词条可能是空,跳过
         continue;
      }

      if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $clean_word) > 0){
        /*
         * 问题思考记录
         *
         * 问:女式服装这种词条也没什么问题, 先注释掉这段代码.
         * 答:还是有问题, 如果用不输入 女式 空格 服装,就会匹配不到.
         **/

        //细胞词库有点问题,需要特定的规则过滤词条

        /* 词条规则一 */
        //以女或男的开头的,超过3个字都可以考虑抛弃
        $header_str = mb_substr($clean_word,0,1,$charset);
        if(mb_strlen($clean_word,$charset) >= 3 && ( '女' == $header_str || '男' == $header_str )){
          continue;
        }

        /* 词条规则二 */
        //词条由4个中文字符组成,而且前面两个字符在mysql字典中作为一个词存在,
        //那这词条应该就是符合词,抛弃
        //todo,待开发


        $ret = $database->insert("word", ["word" => $clean_word]);
        if("23000" != $ret->errorCode() && "00000" != $ret->errorCode()){
          //todo,插入失败,写日志.
        }
      }else{
        //如果词条是英文的 hello word,中间有空格,那一个单词就是一个词
        $clean_word_arr = explode(" ",$clean_word);

        foreach($clean_word_arr as $item){
          $ret = $database->insert("word", ["word" => $item]);
          if("23000" != $ret->errorCode() && "00000" != $ret->errorCode()){
            //todo,插入失败,写日志.
          }
        }
      }


      //查询词条存在不存在
      /*
       * todo,
       * 可以优化的地方,词条过多(超1000万),可以考虑用hash管理词条, 而不是 b-tree.
       * 但是mysql管理词条比较方便
       * */

    }
    fclose($word_file);
  }

}

$dict_dir = "./dict/";

if(is_dir($dict_dir)){
  $mydir = dir($dict_dir);
  while($file = $mydir->read()){
    if($file != "." && $file != ".."){
      load_dict_txt($dict_dir . $file);
    }
  }
  $mydir->close();
}

exit;


