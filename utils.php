<?php
/**
 * Created by PhpStorm.
 * User: loken_mac
 * Date: 13/01/2019
 * Time: 3:18 PM
 * 分词算法实现.
 */

/*
 * 只用正向最大匹配法有点问题,
 * 如果碰到不认识的词就会停止分词,会有比较大的问题.
 * 所以再加上反向最大词匹配算法.
 * 这里应该可以有一个设计,如果最大反向回来的时候,碰到不认识的词,他的位置如果跟正向最大的位置只差2~3个字符,
 * 那这个2~3个字符就很可能是一个新词,但也不是百分百的.
 *
 * */
//

function pullword($str, $redis){

  $charset = "UTF-8";
  //词条 redis集合
  $set_name = "word_set";

  //最大的词有10个字符,这里考虑了英文单词.
  $max_word_len = 10;

  $finish_word = [];

  $search_str = $str;

  $remain_str = $search_str;

  /* 正向最大词匹配 */
  //无限循环,符合特定条件才退出
  for(; ; ){

    //如果待切分的短语 少于最大词长度
    if(mb_strlen($remain_str,$charset) < $max_word_len){
      $word_len = mb_strlen($remain_str,$charset);
    }else{
      $word_len = $max_word_len;
    }

    $maybe_word = mb_substr($remain_str, 0, $word_len, $charset);

    //判断分词是否完成
    $pullword_finish = false;

    //这个标示如果是true,则 maybe_word 里肯定有一个词.否者没有词则退出分词,分词结束
    $is_mark = false;

    for($i = 0; $i < $word_len; $i++){
      $tmp_word = mb_substr($maybe_word, 0, $word_len - $i, $charset);
      $word_exist = $redis->sIsMember($set_name, $tmp_word);

      //找到词,退出循环
      if($word_exist){
        $is_mark = true;
        array_push($finish_word, $tmp_word);

        //去除已经匹配到的短语
        $remain_str = substr($remain_str, strlen($tmp_word));
        if(mb_strlen($remain_str,$charset) <= 0){
          //分词已经完成
          $pullword_finish = true;
        }

        break;
      }else{

      }

    }

    //$maybe_word 里没有一个词,分词完成
    if(false == $is_mark){
      break;
    }

    if($pullword_finish){
      break;
    }

  }

  /* 逆向最大词匹配 */
  for(; ; ){

    //如果待切分的短语 少于最大词长度,那就从待切分短语的开头读取字符串

    //如果待切分的短语 大于最大词长度,那就从 (待切分短语长度-最大词长度[10]) 位置读取 最大词长度[10] 个字符出来匹配

    //如果待切分的短语 少于最大词长度
    if(mb_strlen($remain_str, $charset) <= $max_word_len){
      $maybe_word = mb_substr($remain_str, 0, $max_word_len, $charset);
    }else{
      $maybe_word = mb_substr($remain_str, mb_strlen($remain_str, $charset) - $max_word_len, $max_word_len, $charset);
    }

    //判断分词是否完成
    $pullword_finish = false;

    //这个标示如果是true,则 maybe_word 里肯定有一个词.否者没有词则退出分词,分词结束
    $is_mark = false;

    $word_len = mb_strlen($maybe_word, $charset);

    for($i = 0; $i < $word_len; $i++){
      $tmp_word = mb_substr($maybe_word, $i, $word_len - $i, $charset);
      $word_exist = $redis->sIsMember($set_name, $tmp_word);

      //找到词,退出循环
      if($word_exist){
        $is_mark = true;
        array_push($finish_word, $tmp_word);

        //词在待切分短语的偏移位置
        $tmp_word_position = mb_strlen($remain_str, $charset) - mb_strlen($tmp_word, $charset);

        //去除已经匹配到的短语
        $remain_str = mb_substr($remain_str, 0, $tmp_word_position, $charset);
        if(mb_strlen($remain_str, $charset) <= 0){
          //分词已经完成
          $pullword_finish = true;
        }

        break;
      }else{

      }

    }

    //$maybe_word 里没有一个词,分词完成
    if(false == $is_mark){
      break;
    }

    if($pullword_finish){
      break;
    }
  }



  return $finish_word;

}