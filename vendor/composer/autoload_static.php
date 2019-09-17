<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaa6aa6a06e0d81cedbf8b2f9d442b27d
{
    public static $prefixLengthsPsr4 = array (
        'd' => 
        array (
            'devskyfly\\robocmd\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'devskyfly\\robocmd\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitaa6aa6a06e0d81cedbf8b2f9d442b27d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitaa6aa6a06e0d81cedbf8b2f9d442b27d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}