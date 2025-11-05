# CHANGELOG

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-11-05

### Added
- `LogWriterInterface` for abstraction of log writing operations (Dependency Inversion Principle)
- `LogFormatterInterface` for abstraction of message formatting (Open/Closed Principle)
- `FileLogWriter` implementation with proper error handling and directory creation
- `DefaultLogFormatter` implementation with working context interpolation
- Comprehensive test suite with 12 tests and 37 assertions covering:
  - Context interpolation (simple and complex types)
  - All PSR-3 log levels
  - Custom writers and formatters via Dependency Injection
  - Edge cases and error handling
  - Stringable objects support
- Support for complex context types: arrays, objects, Stringable, null, boolean
- Automatic log directory creation with proper error messages
- File locking (LOCK_EX) for thread-safe concurrent writes
- Explicit error handling with custom error handler (no @ operators)
- **DRY error handling** - Extracted `createErrorHandler()` method to eliminate code duplication
- Readonly class optimization for FileLogWriter
- **Final classes** - Logger and LoggerTest marked as final (composition over inheritance)
- **Full PHPDoc documentation** - All public methods have @throws annotations
- Example demonstrating Dependency Injection usage

### Changed
- **PHP 8.4+ requirement** - Upgraded from PHP 8.3 to 8.4
- **BREAKING:** Logger constructor signature changed to accept `LogWriterInterface|string` as first parameter
  - Old: `new Logger(string $logFile, string $logLevel)`
  - New: `new Logger(string|LogWriterInterface $logFileOrWriter, string $logLevel, ?LogFormatterInterface $formatter)`
  - Backward compatible when passing string path
- **BREAKING:** Refactored architecture following SOLID principles:
  - Single Responsibility: Logger, Writer, Formatter separated
  - Open/Closed: Extensible via interfaces
  - Liskov Substitution: Interface implementations are substitutable
  - Interface Segregation: Minimal, focused interfaces
  - Dependency Inversion: Logger depends on abstractions
- **Code style improvements**:
  - All exceptions use imported class names (no fully qualified names in code)
  - Modern ECS configuration with fluent API and prepared sets
  - No Yoda style comparisons (natural order: `$var === value`)
  - Final classes enforced by ECS
- **Updated dependencies**:
  - PHPUnit: 11.2 → 12.4
  - PHPStan: 1.11 → 2.1
  - ECS: 12.1 → 12.6
  - PHP-CS-Fixer: 3.59 → 3.89
- Improved error messages with detailed context from error_get_last()
- Enhanced test coverage including integration tests and edge cases
- Code organized into Contract/ and Infrastructure/ namespaces (DDD-inspired)

### Fixed
- **CRITICAL:** Fixed broken context interpolation - original implementation created nested arrays instead of flat key-value pairs
- Fixed missing type casting for context values (objects, arrays, null, boolean)
- Fixed missing error handling for file operations
- Fixed thread-safety issues by adding LOCK_EX flag
- Fixed missing validation for log directory creation
- **Code quality:** Eliminated DRY violation in FileLogWriter error handling

### Removed
- **SECURITY:** Removed all @ error suppression operators in favor of explicit error handling
- Removed incompatible Slevomat Coding Standard sniffs from ECS configuration

## [0.0.1] - 2024-07-05

### Added
- Project initiation
- Basic PSR-3 Logger implementation
- PHPUnit test setup
- PHPStan static analysis (level max)
- ECS code style checking
- MIT License
