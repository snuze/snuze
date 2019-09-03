# Snuze Changelog

## v0.8.0 (September 3, 2019)

Enhancements/Changes:

- Add support for persisting accounts

- Only the properties of a UserAccount (i.e. the attributes publicly available
about any Reddit user) are persisted. AccountMapper::persist() accepts any Account
subtype, but if you pass a MyAccount, properties specific to MyAccount will be
disregarded.

- Remove IF NOT EXISTS from CREATE TABLE DDL statements

Bug Fixes:

- Account::REGEX_VALID_NAME now accepts hyphens

Miscellaneous:

- Documentation cleanup and corrections

- Add CHANGES.md changelog file
