<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1407a96156016d0dc8c1ba64acf2cc46
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RyanBadger\\LaravelAdmin\\' => 24,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RyanBadger\\LaravelAdmin\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1407a96156016d0dc8c1ba64acf2cc46::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1407a96156016d0dc8c1ba64acf2cc46::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1407a96156016d0dc8c1ba64acf2cc46::$classMap;

        }, null, ClassLoader::class);
    }
}
