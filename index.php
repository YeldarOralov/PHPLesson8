<?php

include_once "db_helpers.php";
include_once "Tag.php";

//class Str{
//    static $name = "STRING";
//    public $age = 20;
//
//    public static function Upper($str){
//        return mb_strtoupper($str);
//    }
//    public static function slug($str){
//        $str = str_replace(' ', '-',$str);
//        return self::upper($str);
//    }
//}
//echo Str::slug('Hello asdasdasd!!!');
//
//class MyStatic{
//    public static $counter=0;
//    public function __construct()
//    {
//        self::$counter = db_updateCount('lessons.counters');
//    }
//}
//$one = new MyStatic();
//$two = new MyStatic();


//echo MyStatic::$counter;
//---------------------------------------------------------------------------

//$link = Tag::make('a');
//
//$link->disabled();
//$link->href('google.com');
//$link->id('main');
//Tag::a();
//Tag::div();
//Tag::span();

//$tag = Tag::input(['class'=>'hello', 'disabled'=>null])->addClass('asdasd')->appendBody('asd');

$html = Tag::html(['lang'=>'ru']);
$head = Tag::head()->appendTo($html);
$body = Tag::body()->appendTo($html);

$ul = Tag::ul()->appendTo($body);
$items = ['one', 'two', 'three'];

foreach ($items as $item){
    Tag::li()->appendBody($item)->appendTo($ul);
}

$body->addClass('clear');
echo $html;

