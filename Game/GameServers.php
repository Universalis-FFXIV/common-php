<?php

namespace App\Common\Game;

use App\Common\Exceptions\CompanionMarketServerException;
use Delight\Cookie\Cookie;
use App\Common\Service\Redis\Redis;

class GameServers
{
    const DEFAULT_SERVER = 'Phoenix';
    
    const MARKET_SERVER = 'Balmung';

    const LIST_DC_REGIONS = [
        'Elemental' => 1,
        'Gaia'      => 1,
        'Mana'      => 1,

        'Aether'    => 2,
        'Primal'    => 2,
        'Crystal'   => 2,

        'Chaos'     => 3,
        'Light'     => 3,

        'LuXingNiao' => 4,
        'MoGuLi' => 4,
        'MaoXiaoPang' => 4,
    ];

    const LIST_DC = [
        // NA
        'Aether' => [
            'Adamantoise',
            'Cactuar',
            'Faerie',
            'Gilgamesh',
            'Jenova',
            'Midgardsormr',
            'Sargatanas',
            'Siren'
        ],
        'Primal' => [
            'Behemoth',
            'Excalibur',
            'Exodus',
            'Famfrit',
            'Hyperion',
            'Lamia',
            'Leviathan',
            'Ultros'
        ],
        'Crystal' => [
            'Balmung',
            'Brynhildr',
            'Coeurl',
            'Diabolos',
            'Goblin',
            'Malboro',
            'Mateus',
            'Zalera'
        ],

        // EU
        'Chaos' => [
            'Cerberus',
            'Louisoix',
            'Moogle',
            'Omega',
            'Ragnarok',
            'Spriggan',
        ],
        'Light' => [
            'Lich',
            'Odin',
            'Phoenix',
            'Shiva',
            'Zodiark',
            'Twintania',
        ],

        // JP
        'Elemental' => [
            'Aegis',
            'Atomos',
            'Carbuncle',
            'Garuda',
            'Gungnir',
            'Kujata',
            'Ramuh',
            'Tonberry',
            'Typhon',
            'Unicorn'
        ],
        'Gaia' => [
            'Alexander',
            'Bahamut',
            'Durandal',
            'Fenrir',
            'Ifrit',
            'Ridill',
            'Tiamat',
            'Ultima',
            'Valefor',
            'Yojimbo',
            'Zeromus',
        ],
        'Mana' => [
            'Anima',
            'Asura',
            'Belias',
            'Chocobo',
            'Hades',
            'Ixion',
            'Mandragora',
            'Masamune',
            'Pandaemonium',
            'Shinryu',
            'Titan',
        ],

        // CN
        "陆行鸟" => [
            "红玉海",
            "神意之地",
            "拉诺西亚",
            "幻影群岛",
            "萌芽池",
            "宇宙和音",
            "沃仙曦染",
            "晨曦王座",
        ],
        "莫古力" => [
            "白银乡",
            "白金幻象",
            "神拳痕",
            "潮风亭",
            "旅人栈桥",
            "拂晓之间",
            "龙巢神殿",
            "梦羽宝境",
        ],
        "猫小胖" => [
            "紫水栈桥",
            "延夏",
            "静语庄园",
            "摩杜纳",
            "海猫茶屋",
            "柔风海湾",
            "琥珀原",
        ],
    ];
    
    /**
     * Get the users current server
     */
    public static function getServer(string $pvodied = null): string
    {
        $server = urldecode(ucwords($pvodied ?: Cookie::get('mogboard_server')));
        $worldMap = (Array) Redis::Cache()->get('xiv_World_Map');
        return in_array($server, $worldMap) ? $server : self::DEFAULT_SERVER;
    }

    /**
     * Get a server id from a server string
     */
    public static function getServerId(string $server): int
    {
        $worldMap = (Array) Redis::Cache()->get('xiv_World_Map');
        $index = array_search(ucwords($server), $worldMap);

        if ($index === false) {
            throw new CompanionMarketServerException();
        }

        return $index;
    }

    /**
     * Get the Data Center for
     */
    public static function getDataCenter(string $server): ?string
    {
        foreach (GameServers::LIST_DC as $dc => $servers) {
            if (in_array($server, $servers)) {
                return $dc;
            }
        }

        return 'Light';
    }

    /**
     * Get the data center servers for a specific server
     */
    public static function getDataCenterServers(string $server): ?array
    {
        $dc = self::getDataCenter($server);
        return $dc ? GameServers::LIST_DC[$dc] : null;
    }

    /**
     * Get the data center server ids for a specific server
     */
    public static function getDataCenterServersIds(string $server): ?array
    {
        $servers = self::getDataCenterServers($server);

        foreach ($servers as $i => $server) {
            $servers[$i] = self::getServerId($server);
        }

        return $servers;
    }
}
