<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitddd710b1ed4b88cd39874c7bec2f9dbf
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Phpml\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Phpml\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-ai/php-ml/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitddd710b1ed4b88cd39874c7bec2f9dbf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitddd710b1ed4b88cd39874c7bec2f9dbf::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
