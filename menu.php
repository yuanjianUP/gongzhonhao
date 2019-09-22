<?php
/**
 * Created by PhpStorm.
 * User: jian
 * Date: 2019/8/25
 * Time: 23:36
 */
return [
    "button"=>[
        [
            "type"=>"click",
            "name"=>"今日歌曲",
            "key"=>"V1001_TODAY_MUSIC"
        ],
        [
            "name"=>"菜单",
           "sub_button"=>[
               [
                   "type"=>"view",
                   "name"=>"客服",
                   "url"=>"http://hzhrsp.natappfree.cc/kefu.php"
                ],
               [
                   "type"=>"view",
                   "name"=>"yige",
                   "url"=>"http://www.baidu.com/"
               ],
            ],
        ],
        [
            "type"=>"click",
            "name"=>"赞一下我们",
            "key"=>"V1001_GOOD"
        ]
    ]

];