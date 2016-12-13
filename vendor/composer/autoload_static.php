<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7a8fccfdb08bb451a4e92bbb612df564
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LMQ\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LMQ\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes/LMQ',
        ),
    );

    public static $prefixesPsr0 = array (
        'K' => 
        array (
            'Kafka\\' => 
            array (
                0 => __DIR__ . '/..' . '/nmred/kafka-php/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7a8fccfdb08bb451a4e92bbb612df564::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7a8fccfdb08bb451a4e92bbb612df564::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7a8fccfdb08bb451a4e92bbb612df564::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
