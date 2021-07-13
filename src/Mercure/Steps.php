<?php


namespace App\Mercure;


class Steps
{
    const DOWNLOAD_INIT = 'DOWNLOAD_INIT';
    const DOWNLOAD_PROGRESS = 'DOWNLOAD_PROGRESS';
    const DOWNLOAD_END = 'DOWNLOAD_END';
    const DECRYPT_INIT = 'DECRYPT_INIT';
    const DECRYPT_END = 'DECRYPT_END';
    const DECRYPT_FAILED = 'DECRYPT_FAILED';
}
