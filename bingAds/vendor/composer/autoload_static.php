<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf961fe457b1c4f770ea05ce985d2d34f
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Microsoft\\BingAds\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Microsoft\\BingAds\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf961fe457b1c4f770ea05ce985d2d34f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf961fe457b1c4f770ea05ce985d2d34f::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
