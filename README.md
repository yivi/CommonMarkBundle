# CommonMarkBundle
A **Symfony 5** bundle to integrate [league/commonmark](https://github.com/thephpleague/commonmark) v2, 
allowing you to set multiple Commonmark converters.

[![Latest Stable Version](http://poser.pugx.org/yivoff/commonmark-bundle/v)](https://packagist.org/packages/yivoff/commonmark-bundle)
[![Total Downloads](http://poser.pugx.org/yivoff/commonmark-bundle/downloads)](https://packagist.org/packages/yivoff/commonmark-bundle)
[![Latest Unstable Version](http://poser.pugx.org/yivoff/commonmark-bundle/v/unstable)](https://packagist.org/packages/yivoff/commonmark-bundle)
[![License](http://poser.pugx.org/yivoff/commonmark-bundle/license)](https://packagist.org/packages/yivoff/commonmark-bundle)
![Tests](https://github.com/yivi/CommonMarkBundle/actions/workflows/bundle_tests.yaml/badge.svg)

 - [Requirements](#requirements)
 - [Installation](#installation)
 - [Configuration](#configuration)
     - [Converter type](#converter-type)
     - [Converter options](#converter-options)
     - [Converter extensions](#converter-extensions)
 - [Using the converters](#using-the-converters)
     - [As services](#as-services)
     - [Usage in templates](#usage-in-templates)

## Requirements

This bundle requires PHP 8+ and Symfony 5+.

## Installation
```sh
composer require yivoff/commonmark-bundle
```

If for some reason you are running without Symfony Flex, enable the bundle as
usual adding `Yivoff\CommonmarkBundle\YivoffCommonmarkBundle` to the bundle's array.

## Configuration
You'll need to enable at least one converter to use the bundle.
Create a **YAML** configuration file at path `config/packages/aymdev_commonmark.yaml`. 
Here is an example configuration declaring 2 converters:
```yaml
yivoff_commonmark:
   converters:
      commonmark:
         options:
            commonmark:
               enable_em: false

      github:
         type: github
         
      my_custom:
         type: custom
            extensions:
               - League\CommonMark\Extension\Autolink\AutolinkExtension
               - League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension

```

Setting up at least one converter is mandatory, but all settings are optional. By default,
 a converter without setting a `type` will be created as a CommonMark converter.

### Converter type

The `type` key can be used to choose between a *CommonMark*, a *GitHub Flavoured* or a *Custom* converter.

By default, if not `type` is chosen, CommonMark will be chosen.

### Converter options

You can use the `options` key holds the configuration passed to the converter, as an array.

Check the [CommonMark documentation](https://commonmark.thephpleague.com/2.0/configuration/) to learn more about the available options.

### Converter extensions

The CommonMark and Github Flavoured have a predefined set of extensions installed, which cannot be changed.

But `custom` starts with no extensions, and you pick and choose
[which extensions](https://commonmark.thephpleague.com/2.0/customization/extensions/) you want to enable using the
`extensions` key.

This `key` has no effect on `github` or `commonmark` type converters.

## Using the converters

### As services

Each of the defined converters is available as a service within the container.

The id is generated with the following format: `yivoff_commonmark.converters.converter_name`.

For the converters in the configuration example three services would be generated:

* `yivoff_commonmark.converters.commonmark`
* `yivoff_commonmark.converters.github`
* `yivoff_commonmark.converters.my_custom`

Additionally, the bundle registers an alias for each service, so one can use the service directly for autowiring.

Again, for the above example the registered aliases would be:

* `League\CommonMark\MarkdownConverterInterface $commonmark`
* `League\CommonMark\MarkdownConverterInterface $github`
* `League\CommonMark\MarkdownConverterInterface $myCustom`

### Usage in templates

The bundle defines a Twig filter: `commonmark`.

If you have defined multiple converters, you need to pass the name of the converter you want to use:
```twig
{{ some_markdown_content|commonmark('github') }}
```

But If you have only one converter defined, the parameter can be omitted. 

```twig
{{ some_markdown_content|commonmark }}
```
