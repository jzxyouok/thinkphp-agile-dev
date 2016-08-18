<?php
/**
 * Created by PhpStorm.
 * User: Uctoo-Near
 * Date: 2016/4/9
 * Time: 14:40
 */


/*
 * 解压文件；
 *type 0：模式 1 插件
 * */
function get_zip_originalsize($name,$type = 0) {
    //先判断待解压的文件是否存在
    $filename =__ROOT__.'updata/'.$name;
    $dirname = explode('.',$name);
    array_pop($dirname);
    $dirname = implode('.',$dirname);

    $path = __ROOT__;

    if(!file_exists($filename)){
        die("文件 $filename 不存在！");
    }
    $starttime = explode(' ',microtime()); //解压开始的时间

    //将文件名和路径转成windows系统默认的gb2312编码，否则将会读取不到
    $filename = iconv("utf-8","gb2312",$filename);
    $path = iconv("utf-8","gb2312",$path);
    //打开压缩包
    $resource = zip_open($filename);
    $i = 1;
    //遍历读取压缩包里面的一个个文件
    while ($dir_resource = zip_read($resource)) {
        //如果能打开则继续
        if (zip_entry_open($resource,$dir_resource)) {
            //获取当前项目的名称,即压缩包里面当前对应的文件名
            $file_name = $path.zip_entry_name($dir_resource);
            //以最后一个“/”分割,再用字符串截取出路径部分
            $file_path = substr($file_name,0,strrpos($file_name, "/"));
            //如果路径不存在，则创建一个目录，true表示可以创建多级目录
            if(!is_dir($file_path)){
                mkdir($file_path,0777,true);
            }
            //如果不是目录，则写入文件
            if(!is_dir($file_name)){
                //读取这个文件
                $file_size = zip_entry_filesize($dir_resource);
                //最大读取6M，如果文件过大，跳过解压，继续下一个
//                        if($file_size<(1024*1024*6)){
                $file_content = zip_entry_read($dir_resource,$file_size);
                file_put_contents($file_name,$file_content);
//                        }else{
//                            echo "<p> ".$i++." 此文件已被跳过，原因：文件过大， -> ".iconv("gb2312","utf-8",$file_name)." </p>";
//                        }
            }
            //关闭当前
            zip_entry_close($dir_resource);
        }
    }
    //关闭压缩包
    zip_close($resource);
    $endtime = explode(' ',microtime()); //解压结束的时间
    $thistime = $endtime[0]+$endtime[1]-($starttime[0]+$starttime[1]);
    $thistime = round($thistime,3); //保留3为小数
    //echo "<p>解压完毕！，本次解压花费：$thistime 秒。</p>";
    return $file_path;

}