<?php

return [
    'enabled'             => env('APP_TERMINAL_ENABLED', false),
    // npm包管理器
    'npm_package_manager' => 'pnpm',
    // 允许执行的命令
    'commands'            => [
        // 安装包管理器的命令
        'install'      => [
            'cnpm' => 'npm install cnpm -g --registry=https://registry.npmmirror.com',
            'yarn' => 'npm install -g yarn',
            'pnpm' => 'npm install -g pnpm',
        ],
        // 查看版本的命令
        'version'      => [
            'npm'  => 'npm -v',
            'cnpm' => 'cnpm -v',
            'yarn' => 'yarn -v',
            'pnpm' => 'pnpm -v',
            'node' => 'node -v',
        ],
        // 测试命令
        'test'         => [
            'npm'  => [
                'cwd'     => base_path() . '/plugin/cmdr/public/npm-install-test',
                'command' => 'npm install',
            ],
            'cnpm' => [
                'cwd'     => base_path() . '/plugin/cmdr/public/npm-install-test',
                'command' => 'cnpm install',
            ],
            'yarn' => [
                'cwd'     => base_path() . '/plugin/cmdr/public/npm-install-test',
                'command' => 'yarn install',
            ],
            'pnpm' => [
                'cwd'     => base_path() . '/plugin/cmdr/public/npm-install-test',
                'command' => 'pnpm install',
            ],
        ],
        // 安装 WEB 依赖包
        'cmdr-install' => [
            'npm'  => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'npm install',
            ],
            'cnpm' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'cnpm install',
            ],
            'yarn' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'yarn install',
            ],
            'pnpm' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'pnpm install',
            ],
        ],
        // 构建 WEB 端
        'cmdr-build'   => [
            'npm'  => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'npm run build',
            ],
            'cnpm' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'cnpm run build',
            ],
            'yarn' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'yarn run build',
            ],
            'pnpm' => [
                'cwd'     => dirname(base_path()) . '/web',
                'command' => 'pnpm run build',
            ],
        ],
        // 设置源
        'set-registry' => [
            'npm'     => 'npm config set registry https://registry.npmjs.org/ && npm config get registry',
            'taobao'  => 'npm config set registry https://registry.npmmirror.com/ && npm config get registry',
            'tencent' => 'npm config set registry https://mirrors.cloud.tencent.com/npm/ && npm config get registry',
        ],
        'composer'     => [
            'update' => [
                'cwd'     => base_path(),
                'command' => 'composer update --no-interaction',
            ],
        ],
        'ping'         => [
            'baidu'     => 'ping www.baidu.com',
            'localhost' => 'ping 127.0.0.1 -n 6',
        ],
    ],
];
