<?php

namespace App\Helpers;

class BanderaHelper
{
    public static array $banderas = [
        // Grupo A
        'Mexico'              => '🇲🇽',
        'South Africa'        => '🇿🇦',
        'Ecuador'             => '🇪🇨',
        'New Zealand'         => '🇳🇿',
        // Grupo B
        'Argentina'           => '🇦🇷',
        'Chile'               => '🇨🇱',
        'Peru'                => '🇵🇪',
        'Australia'           => '🇦🇺',
        // Grupo C
        'Netherlands'         => '🇳🇱',
        'Senegal'             => '🇸🇳',
        'Japan'               => '🇯🇵',
        'Canada'              => '🇨🇦',
        // Grupo D
        'Spain'               => '🇪🇸',
        'Morocco'             => '🇲🇦',
        'Croatia'             => '🇭🇷',
        'Cameroon'            => '🇨🇲',
        // Grupo E
        'Portugal'            => '🇵🇹',
        'Uruguay'             => '🇺🇾',
        'Saudi Arabia'        => '🇸🇦',
        'IR Iran'             => '🇮🇷',
        // Grupo F
        'Brazil'              => '🇧🇷',
        'Switzerland'         => '🇨🇭',
        'Ivory Coast'         => '🇨🇮',
        'Serbia'              => '🇷🇸',
        // Grupo G
        'France'              => '🇫🇷',
        'USA'                 => '🇺🇸',
        'England'             => '🏴󠁧󠁢󠁥󠁮󠁧󠁿',
        'Paraguay'            => '🇵🇾',
        // Grupo H
        'Germany'             => '🇩🇪',
        'Colombia'            => '🇨🇴',
        'Ghana'               => '🇬🇭',
        'Algeria'             => '🇩🇿',
        // Grupo I
        'Belgium'             => '🇧🇪',
        'Korea Republic'      => '🇰🇷',
        'Venezuela'           => '🇻🇪',
        'Egypt'               => '🇪🇬',
        // Grupo J
        'Nigeria'             => '🇳🇬',
        'Türkiye'             => '🇹🇷',
        'Poland'              => '🇵🇱',
        // Grupo K
        'Italy'               => '🇮🇹',
        'Qatar'               => '🇶🇦',
        'Costa Rica'          => '🇨🇷',
        'Panama'              => '🇵🇦',
        // Grupo L
        'Denmark'             => '🇩🇰',
        'Slovenia'            => '🇸🇮',
        'Iraq'                => '🇮🇶',
        'Cuba'                => '🇨🇺',
        // Otras selecciones frecuentes / alias
        'United States'       => '🇺🇸',
        'Iran'                => '🇮🇷',
        'South Korea'         => '🇰🇷',
        'Turkey'              => '🇹🇷',
        'Wales'               => '🏴󠁧󠁢󠁷󠁬󠁳󠁿',
    ];

    public static function get(string $equipo): string
    {
        return self::$banderas[$equipo]
            ?? self::$banderas[trim($equipo)]
            ?? '🏳️';
    }
}
