<?php

//上传配置
return [
    /**
     * 上传地址,默认是本地上传
     */
    'uploadurl' => 'ajax/upload',
    /**
     * CDN地址
     */
    'cdnurl'    => '',
    /**
     * 文件保存格式
     */
    'savekey'   => '/uploads/{year}{mon}{day}/{filemd5}{.suffix}',
    /**
     * 最大可上传大小
     */
    'maxsize'   => '20mb',
    /**
     * 可上传的文件类型
     */
    'mimetype'  => 'jpg,png,bmp,jpeg,gif,zip,rar,xls,xlsx,mp4,avi,mp3',
    /**
     * 是否支持批量上传
     */
    'multiple'  => false,

    'oss' => [
        'accessKey_id' => 'LTAI4Fi1YassKAWKVJS4gHn5',
        'access_secret' => 'nhhTIgDKyP3ni0mwSZCg33aGHaFTiC',
        'bucket' => 'jsu173',
        'directory_name' => 'upload/',
        'endpoint' => 'oss-cn-shanghai.aliyuncs.com'
    ]
];
