<?php

namespace App\Common\Utils;

use Carbon\Carbon;

class SiteVersion
{
    const MAJOR  = 1;
    const MINOR  = 0;
    
    public static function get()
    {
        [$commits, $hash, $time] = explode("\n", file_get_contents(__DIR__.'/../../../git_version.txt'));
        
        $commitVersion = $commits;
        $commitVersion = $commitVersion > 0 ? $commitVersion : 0;
        $commitVersion = str_pad($commitVersion, 2, '0', STR_PAD_LEFT);
        $version       = sprintf('%s.%s', self::MAJOR, self::MINOR);
        $time          = Carbon::createFromTimestamp($time)->format('jS M - g:i a') . ' (UTC)';

        return (Object)[
            'version'   => $version,
            'commits'   => $commits,
            'hash'      => $hash,
            'hash_min'  => substr($hash, 0, 7),
            'time'      => $time,
        ];
    }
}
