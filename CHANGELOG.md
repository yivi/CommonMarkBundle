# Changelog
Most notable changes to this project will be documented in this file. Hopefully.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v1.0.0](https://github.com/yivi/CommonMarkBundle/releases/tag/v1.0.0) - 2021-08-24
### Added
- Initial release

## [v1.0.1](https://github.com/yivi/CommonMarkBundle/releases/tag/v1.0.1) - 2021-12-30
### Added
- Symfony 6 and PHP 8 compatibility

## [v1.1.0](https://github.com/yivi/CommonMarkBundle/releases/tag/v1.1.0) - 2022-02-01
### Added
- Bump "league/commonmark" to "^2.2.0"
- Now aliases are created with ConverterInterface typehint, since MarkdownConverterInterface is now deprecated
  (the old aliases are still created as long as the old interface exists, for BC) 
