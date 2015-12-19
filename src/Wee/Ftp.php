<?php
/**
 * $Id: Ftp.php 473 2014-09-25 14:43:05Z svn $
 * @author miaokuan
 */

namespace Wee;

class Ftp
{
    protected $conn;

    public function __construct($url)
    {
        $this->conn = ftp_connect($url);
    }

    public function __call($func, $a)
    {
        $func = 'ftp_' . $func;
        if (function_exists($func)) {
            array_unshift($a, $this->conn);
            return call_user_func_array($func, $a);
        } else {
            // replace with your own error handler.
            die("$func is not a valid FTP function");
        }
    }

    public function __destruct()
    {
        ftp_close($this->conn);
    }
}

/*
FTP 函数
ftp_alloc — Allocates space for a file to be uploaded
ftp_cdup — 切换到当前目录的父目录
ftp_chdir — 在 FTP 服务器上改变当前目录
ftp_chmod — Set permissions on a file via FTP
ftp_close — 关闭一个 FTP 连接
ftp_connect — 建立一个新的 FTP 连接
ftp_delete — 删除 FTP 服务器上的一个文件
ftp_exec — 请求运行一条 FTP 命令
ftp_fget — 从 FTP 服务器上下载一个文件并保存到本地一个已经打开的文件中
ftp_fput — 上传一个已经打开的文件到 FTP 服务器
ftp_get_option — 返回当前 FTP 连接的各种不同的选项设置
ftp_get — 从 FTP 服务器上下载一个文件
ftp_login — 登录 FTP 服务器
ftp_mdtm — 返回指定文件的最后修改时间
ftp_mkdir — 建立新目录
ftp_nb_continue — 连续获取／发送文件（non-blocking）
ftp_nb_fget — Retrieves a file from the FTP server and writes it to an open file (non-blocking)
ftp_nb_fput — Stores a file from an open file to the FTP server (non-blocking)
ftp_nb_get — 从 FTP 服务器上获取文件并写入本地文件（non-blocking）
ftp_nb_put — 存储一个文件至 FTP 服务器（non-blocking）
ftp_nlist — 返回给定目录的文件列表
ftp_pasv — 返回当前 FTP 被动模式是否打开
ftp_put — 上传文件到 FTP 服务器
ftp_pwd — 返回当前目录名
ftp_quit — ftp_close 的 别名
ftp_raw — Sends an arbitrary command to an FTP server
ftp_rawlist — 返回指定目录下文件的详细列表
ftp_rename — 更改 FTP 服务器上的文件或目录名
ftp_rmdir — 删除 FTP 服务器上的一个目录
ftp_set_option — 设置各种 FTP 运行时选项
ftp_site — 向服务器发送 SITE 命令
ftp_size — 返回指定文件的大小
ftp_ssl_connect — Opens an Secure SSL-FTP connection
ftp_systype — 返回远程 FTP 服务器的操作系统类型
 */
