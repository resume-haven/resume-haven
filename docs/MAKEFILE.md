# Makefile Documentation

## Overview

The `Makefile` in this project automates common development and Docker management tasks. It provides a simple, consistent interface for frequently used commands.

## Usage

### View All Commands

```bash
make help
```

or just:

```bash
make
```

### Run a Command

```bash
make <target>
```

Example:
```bash
make up          # Start Docker containers
make test        # Run tests
make shell       # Open container shell
```

## How It Works

### Self-Documenting Help

The Makefile uses a **comment-based documentation system** that automatically generates the help output. This means:

1. **Single source of truth** - Documentation lives with the code
2. **Always in sync** - Comments are directly above targets
3. **Easy to maintain** - Add comment, help updates automatically
4. **Organized automatically** - Grouped by category

### Comment Format

Every target should have a comment in this format:

```makefile
## GROUPE_NAME: target_name - Description of what it does
target_name:
	@echo "Doing something"
	docker-compose exec app some-command
```

**Format Breakdown:**
- `##` - Special marker (parsed by awk in help target)
- `GROUP_NAME` - Category (Docker Management, Application, etc.)
- `:` - Delimiter
- `target_name` - The make target name (must match target definition)
- `-` - Separator
- `Description` - User-friendly description

### Parsing Logic

The `help` target uses this awk command to parse:

```bash
awk '/^## /{match($$0, /^## ([^:]+): ([^ ]+) - (.+)/, arr); 
    group=arr[1]; cmd=arr[2]; desc=arr[3]; 
    if(group != prev_group && prev_group != "") print ""; 
    if(group != prev_group) {printf "$(GREEN)%s$(NC)\n", group; prev_group=group} 
    printf "  make %-15s %s\n", cmd, desc}' $(MAKEFILE_LIST)
```

**What it does:**
1. Finds all lines starting with `## `
2. Extracts: group, command name, description using regex
3. Groups by category (prints blank line between groups)
4. Formats output with colors and alignment

## Available Commands

### Docker Management

```bash
make up              # Start containers in background
make down            # Stop containers
make build           # Build Docker image
make rebuild         # Rebuild (no cache)
make restart         # Restart containers
make logs            # View logs (follow mode)
make shell           # Open container shell
make healthcheck     # Check container status
```

### Application

```bash
make install         # Install Composer dependencies
make update          # Update dependencies
make test            # Run tests
make lint            # Code style checks
make migrate         # Run migrations
make php-version     # Show PHP version/extensions
```

### Maintenance

```bash
make clean           # Remove containers/networks
make prune           # Clean Docker system
make fix-perms       # Fix file permissions
```

### Development

```bash
make dev                  # Complete setup (up + install)
make debug                # Start debug listener
make init                 # Initialize project
make reset                # Reset to fresh state
make status               # Show project status
make laravel-install      # Install Laravel framework
make laravel-strict-types # Add strict types to all PHP files
```

#### `make laravel-install`

Install Laravel framework into the project. This command:
- Creates a fresh Laravel project in a temporary directory
- Moves Laravel files to the project root
- Configures SQLite as the database
- Generates application key
- Runs initial migrations
- Preserves existing Docker and documentation files

**Usage**:
```bash
make laravel-install
```

**Note**: This target is idempotent and can be run multiple times safely.

#### `make laravel-strict-types`

Add `declare(strict_types=1)` to all PHP files in the Laravel application.

**Usage**:
```bash
make laravel-strict-types
```

This ensures type safety across the entire codebase by adding strict type declarations to:
- `app/` - Application code
- `bootstrap/` - Bootstrap files
- `database/` - Migrations, seeders, factories
- `routes/` - Route definitions
- `tests/` - Test files

**Example output**:
```
Added strict types: app/Infrastructure/Persistence/UserModel.php
Added strict types: app/Http/Controllers/Controller.php
...
Done!
```

## Adding New Commands

To add a new make command:

1. **Add the comment** above your target:
   ```makefile
   ## My Category: my-target - Does something useful
   ```

2. **Define the target**:
   ```makefile
   my-target:
   	@echo "Doing something"
   	docker-compose exec app some-command
   ```

3. **Add to .PHONY** at the end:
   ```makefile
   .PHONY: my-target
   ```

4. **Test it**:
   ```bash
   make my-target
   make help  # Should show your new command
   ```

## Best Practices

### 1. Use `@echo` for User Feedback

```makefile
## Docker Management: example - Example command
example:
	@echo "$(BLUE)Starting something...$(NC)"
	docker-compose exec app php -v
	@echo "$(GREEN)Done!$(NC)"
```

The `@` suppresses the command itself from being printed.

### 2. Organize Targets by Category

Group related targets together in sections:

```makefile
# ============================================================================
# DOCKER MANAGEMENT
# ============================================================================

## Docker Management: up - Start containers
up:
	...

## Docker Management: down - Stop containers
down:
	...
```

### 3. Use Colors for Clarity

Available colors:
- `$(BLUE)` - Informational messages
- `$(GREEN)` - Success messages
- `$(YELLOW)` - Warning messages
- `$(NC)` - Reset to normal

### 4. Add Dependencies Between Targets

```makefile
## Development: dev - Full setup
dev: up install
	@echo "$(GREEN)Ready!$(NC)"
```

This runs `up` and `install` before `dev`.

### 5. Use Silent Commands for Clean Output

```makefile
@docker-compose ps     # Output only the result
docker-compose ps      # Shows the command too
```

## Advanced Features

### Combining Targets

```bash
# Run multiple targets
make down build up install test
```

### Targeting Specific Containers

```makefile
## Application: bash - Run bash in container
bash:
	docker-compose exec app bash
```

### Passing Arguments

```makefile
## Application: exec - Execute command in container
exec:
	@read -p "Command: " cmd; docker-compose exec app $$cmd
```

### Conditional Execution

```makefile
## Docker Management: smart-up - Start if not running
smart-up:
	@docker-compose ps | grep -q "Up" || docker-compose up -d
```

## Troubleshooting

### "make: command not found"

**On Windows PowerShell:**
- Make is not available in PowerShell
- Use WSL: `wsl make help`
- Or use Git Bash / MSYS2

**On macOS:**
```bash
# Install via Homebrew
brew install make
```

**On Linux:**
```bash
# Install via package manager
apt-get install make        # Debian/Ubuntu
yum install make            # RHEL/CentOS
```

### "target up: No such file or directory"

Usually means `docker-compose` is not installed or not in PATH.

```bash
# Verify docker-compose
docker-compose --version

# Add to PATH if needed
export PATH="/usr/local/bin:$PATH"
```

### Makefile syntax errors

Make is **tab-sensitive**. All commands must start with TAB (not spaces).

```makefile
# ❌ Wrong (spaces)
target:
    @echo "test"

# ✅ Correct (tab)
target:
	@echo "test"
```

In VS Code, use editor setting:
```json
{
    "makefile.strictPrerequisites": false,
    "[makefile]": {
        "editor.insertSpaces": false,
        "editor.detectIndentation": false
    }
}
```

## Integration with Other Tools

### Git Hooks

```bash
# pre-commit: Run lint before committing
#!/bin/bash
make lint || exit 1
```

### CI/CD Pipeline

```yaml
# .github/workflows/test.yml
- name: Run tests
  run: make test
  
- name: Check lint
  run: make lint
```

### IDE Integration

**VS Code**:
- Extension: "makefile" by ms-vscode.makefile-tools
- Provides IntelliSense for targets
- Syntax highlighting

**JetBrains IDEs**:
- Built-in Makefile support
- Run targets from context menu

## Performance Tips

### Parallel Execution

```bash
# Run up to 4 targets in parallel
make -j4 lint test
```

### Skip Dependencies

```bash
# Run only the target, skip prerequisites
make --always-make test
```

### Dry Run

```bash
# Show what would be executed (no actual execution)
make -n test
```

## Example: Custom Makefile Extension

Create a `Makefile.local` for local overrides:

```makefile
# Makefile.local
# Custom local targets (not committed to git)

## Development: local-dev - Local development setup
local-dev: dev
	@echo "$(GREEN)Local development environment ready!$(NC)"
	@docker-compose exec app composer require --dev phpunit/phpunit
```

Include in main Makefile:
```makefile
-include Makefile.local
```

## Resources

- [GNU Make Manual](https://www.gnu.org/software/make/manual/)
- [Makefile Best Practices](https://tech.Davis-Hanson.com/Makefile)
- [Self-Documenting Makefiles](https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html)
