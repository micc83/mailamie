# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.5] - 2021-08-10

### Changed
- Update composer dependencies
- Update friendsofphp/php-cs-fixer to v3.0.2
- Update zbateson/mail-mime-parser to v1.3

### Fixed
- Remove double dots from email content rows start (fix #13)
- Parse null subject, textBody and htmlBody to empty strings on Mailamie\Emails\Message creation (fix #12)

## [1.0.4] - 2021-04-02

### Added
- Added github/licensed to check license on dependencies
- Added codecov badge to readme file (Fix #6)

### Changed
- Bump axios from 0.19.2 to 0.21.1 (#8)

## [1.0.3] - 2020-09-29

### Changed
- Changed composer.json to require only needed React dependencies

### Removed
- Removed todo file and created issues for missing items

## [1.0.2] - 2020-09-28

### Fixed
- Fixed issue with assets path

## [1.0.1] - 2020-09-28

### Fixed
- Fixed issue with bin paths

## [1.0.0] - 2020-09-28

### Added
- First commit
