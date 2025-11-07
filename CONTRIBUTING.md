# Contributing to TheTradeVisor

Thank you for considering contributing to TheTradeVisor! This document outlines the process and guidelines for contributing.

## 🤝 Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for all contributors.

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18.x
- Git
- Basic understanding of Laravel and Vue.js

### Setting Up Development Environment

1. **Fork the Repository**
   ```bash
   # Fork on GitHub, then clone your fork
   git clone git@github.com:YOUR_USERNAME/TheTradeVisor.git
   cd TheTradeVisor
   ```

2. **Add Upstream Remote**
   ```bash
   git remote add upstream git@github.com:ORIGINAL_OWNER/TheTradeVisor.git
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

4. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate
   php artisan passport:install
   ```

5. **Start Development Server**
   ```bash
   composer dev
   # Or run services individually
   ```

## 📝 How to Contribute

### Reporting Bugs

Before creating a bug report:
- Check existing issues to avoid duplicates
- Verify the bug exists in the latest version
- Collect relevant information (error messages, logs, screenshots)

When creating a bug report, include:
- **Clear title** describing the issue
- **Steps to reproduce** the bug
- **Expected behavior** vs actual behavior
- **Environment details** (OS, PHP version, browser)
- **Screenshots or logs** if applicable

### Suggesting Enhancements

Enhancement suggestions are welcome! Please:
- Check if the enhancement has already been suggested
- Provide a clear use case
- Explain why this enhancement would be useful
- Consider implementation details if possible

### Pull Requests

#### Before Submitting

1. **Check existing PRs** to avoid duplicates
2. **Create an issue** first for major changes
3. **Follow coding standards** (see below)
4. **Write tests** for new features
5. **Update documentation** as needed

#### PR Process

1. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

2. **Make Your Changes**
   - Write clean, documented code
   - Follow existing code style
   - Add tests for new functionality
   - Update relevant documentation

3. **Commit Your Changes**
   ```bash
   git add .
   git commit -m "feat: add amazing new feature"
   ```

   Use conventional commit messages:
   - `feat:` New feature
   - `fix:` Bug fix
   - `docs:` Documentation changes
   - `style:` Code style changes (formatting)
   - `refactor:` Code refactoring
   - `test:` Adding or updating tests
   - `chore:` Maintenance tasks

4. **Keep Your Branch Updated**
   ```bash
   git fetch upstream
   git rebase upstream/main
   ```

5. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create Pull Request**
   - Go to GitHub and create a PR from your fork
   - Fill out the PR template completely
   - Link related issues
   - Request review from maintainers

#### PR Requirements

- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] Tests added/updated and passing
- [ ] No new warnings generated
- [ ] Dependent changes merged

## 💻 Coding Standards

### PHP/Laravel

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard
- Use Laravel best practices
- Use type hints and return types
- Write descriptive variable and method names
- Add PHPDoc blocks for classes and methods

Example:
```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Service for managing user operations
 */
class UserService
{
    /**
     * Get active users with their accounts
     *
     * @return Collection<User>
     */
    public function getActiveUsers(): Collection
    {
        return User::with('accounts')
            ->where('is_active', true)
            ->get();
    }
}
```

### JavaScript

- Use ES6+ syntax
- Follow Airbnb JavaScript style guide
- Use meaningful variable names
- Add JSDoc comments for functions

Example:
```javascript
/**
 * Fetch user data from API
 * @param {number} userId - The user ID
 * @returns {Promise<Object>} User data
 */
async function fetchUserData(userId) {
    const response = await axios.get(`/api/users/${userId}`);
    return response.data;
}
```

### CSS/TailwindCSS

- Use TailwindCSS utility classes
- Follow mobile-first approach
- Keep custom CSS minimal
- Use consistent spacing

### Database

- Use descriptive migration names
- Add foreign key constraints
- Include rollback methods
- Add indexes for frequently queried columns

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/UserTest.php

# Run with coverage
php artisan test --coverage
```

### Writing Tests

- Write tests for all new features
- Test both success and failure cases
- Use descriptive test names
- Follow AAA pattern (Arrange, Act, Assert)

Example:
```php
public function test_user_can_create_trading_account(): void
{
    // Arrange
    $user = User::factory()->create();
    $this->actingAs($user);

    // Act
    $response = $this->post('/accounts', [
        'broker' => 'TestBroker',
        'account_number' => '12345',
    ]);

    // Assert
    $response->assertStatus(201);
    $this->assertDatabaseHas('trading_accounts', [
        'user_id' => $user->id,
        'broker' => 'TestBroker',
    ]);
}
```

## 📚 Documentation

### Code Documentation

- Add PHPDoc blocks for all classes and public methods
- Document complex algorithms or business logic
- Keep comments up-to-date with code changes

### User Documentation

- Update README.md for user-facing changes
- Add examples for new features
- Update API documentation if applicable

## 🔍 Code Review Process

### For Contributors

- Be open to feedback
- Respond to review comments promptly
- Make requested changes or discuss alternatives
- Keep discussions professional and constructive

### For Reviewers

- Be respectful and constructive
- Focus on code, not the person
- Explain reasoning behind suggestions
- Approve when requirements are met

## 🎯 Priority Areas

We especially welcome contributions in:

- **Bug fixes** - Always appreciated
- **Test coverage** - Help us reach 80%+
- **Documentation** - Improve clarity and examples
- **Performance** - Optimize slow queries or processes
- **Accessibility** - Improve WCAG compliance
- **Internationalization** - Add language support

## ❓ Questions?

- **General questions**: Open a GitHub Discussion
- **Bug reports**: Create an issue
- **Security issues**: Email hello@thetradevisor.com
- **Feature requests**: Create an issue with [Feature Request] tag
- **Website**: https://thetradevisor.com/

## 📜 License

By contributing, you agree that your contributions will be licensed under the MIT License.

## 🙏 Recognition

Contributors will be recognized in:
- GitHub contributors page
- Release notes for significant contributions
- Project documentation (with permission)

Thank you for contributing to TheTradeVisor! 🎉
