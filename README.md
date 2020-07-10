# Guzzle Bundle Alibaba Cloud API Gateway HTTP Request Signer Plugin

[![Latest version](https://img.shields.io/packagist/v/ion-bazan/guzzle-bundle-aliyun-signer-plugin.svg)](https://packagist.org/packages/ion-bazan/guzzle-bundle-aliyun-signer-plugin)
[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/IonBazan/GuzzleBundleAliyunSignerPlugin/Tests)](https://github.com/IonBazan/GuzzleBundleAliyunSignerPlugin/actions)
[![PHP version](https://img.shields.io/packagist/php-v/ion-bazan/guzzle-bundle-aliyun-signer-plugin.svg)](https://packagist.org/packages/ion-bazan/guzzle-bundle-aliyun-signer-plugin)
[![Codecov](https://img.shields.io/codecov/c/gh/IonBazan/GuzzleBundleAliyunSignerPlugin)](https://codecov.io/gh/IonBazan/GuzzleBundleAliyunSignerPlugin)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2FIonBazan%2FGuzzleBundleAliyunSignerPlugin%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/IonBazan/GuzzleBundleAliyunSignerPlugin/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/IonBazan/GuzzleBundleAliyunSignerPlugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/IonBazan/GuzzleBundleAliyunSignerPlugin/?branch=master)
[![Downloads](https://img.shields.io/packagist/dt/ion-bazan/guzzle-bundle-aliyun-signer-plugin.svg)](https://packagist.org/packages/ion-bazan/guzzle-bundle-aliyun-signer-plugin)
[![License](https://img.shields.io/packagist/l/ion-bazan/guzzle-bundle-aliyun-signer-plugin.svg)](https://packagist.org/packages/ion-bazan/guzzle-bundle-aliyun-signer-plugin)

# Description

This Guzzle Bundle plugin integrates [ion-bazan/aliyun-http-signer](https://github.com/IonBazan/aliyun-http-signer) with Symfony.

# Requirements

 - PHP 7.2 or above
 - [Guzzle Bundle](https://github.com/8p/EightPointsGuzzleBundle)
 
# Installation

Use [Composer](https://getcomposer.org/) to install the plugin using:

```bash
composer require ion-bazan/guzzle-bundle-aliyun-signer-plugin
```

# Usage

## Enable the Bundle

### Symfony 2.x and 3.x

Register the bundle in `app/AppKernel.php` file:

```php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new IonBazan\Bundle\GuzzleBundleAliyunSignerPlugin\AliyunRequestSignerPlugin(),
]);
```

### Symfony 4+

To register the plugin in Symfony 4 and newer, replace `registerBundles()` method in `src/Kernel.php`:

```php
public function registerBundles(): iterable
{
    $contents = require $this->getProjectDir().'/config/bundles.php';
    foreach ($contents as $class => $envs) {
        if ($envs[$this->environment] ?? $envs['all'] ?? false) {
            if ($class === \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle::class) {
                yield new $class([
                    new \IonBazan\Bundle\GuzzleBundleAliyunSignerPlugin\AliyunRequestSignerPlugin(),
                ]);
            } else {
                yield new $class();
            }
        }
    }
}
```

## Configuration

```yaml
# config/packages/eight_points_guzzle.yaml
eight_points_guzzle:
    clients:
        my_client:
            base_url: "http://target.url"
            plugin:
                aliyun_signer:
                    app_id: 'Your AppID'
                    secret: 'Your Secret'
```

# Bugs & issues

If you found a bug or security vulnerability, please [open an issue](https://github.com/IonBazan/GuzzleBundleAliyunSignerPlugin/issues/new)

# Contributing

Please feel free to submit Pull Requests adding new features or fixing bugs.

Please note that code must follow PSR-1, PSR-2, PSR-4 and PSR-7.  
