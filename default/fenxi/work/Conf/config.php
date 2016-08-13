<?php
/**
 * User: Beyond_dream
 * Date: 2016/8/12
 * Time: 9:36
 */
return array(
    'LOAD_EXT_CONFIG' => array(
        'helper','db'
    ),
    /* 错误设置 */
    'ERROR_MESSAGE'         => '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            => '',	                    // 错误定向页面
    'SHOW_ERROR_MSG'        => true,                    // 显示错误信息
    'TRACE_EXCEPTION'       => true,                    // TRACE错误信息是否抛异常 针对trace方法

    /* 日志设置 */
    'LOG_RECORD'            => true,                    // 默认不记录日志
    'LOG_TYPE'              => 3,                       // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST'              => './data/log/log.txt',                      // 日志记录目标
    'LOG_EXTRA'             => '',                      // 日志记录额外信息
    'LOG_LEVEL'             => 'EMERG,ALERT,CRIT,ERR',  // 允许记录的日志级别
    'LOG_FILE_SIZE'         => 2097152,	                // 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  => true,                    // 是否记录异常信息日志

    'URL_CASE_INSENSITIVE'  => true,                   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 3,                       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：// 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR'     => '/',	                    // PATHINFO模式下，各参数之间的分割符号

);