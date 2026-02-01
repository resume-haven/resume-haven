# Xdebug Debugging Guide

## Overview

ResumeHaven is configured with **Xdebug 3.5.0** for interactive debugging through Visual Studio Code. This guide explains how to set up and use debugging.

## Prerequisites

- VS Code installed
- PHP Debug extension installed (by Felix Becker)
  - Extension ID: `felixbecker.php-debug`
  - Install via: Extensions → Search "PHP Debug" → Install

## Xdebug Configuration

### Current Setup

**Location**: `docker/FrankenPHP/xdebug.ini`

```ini
zend_extension=xdebug.so

[xdebug]
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.discover_client_host=true
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.max_nesting_level=256
```

### Key Settings

| Setting | Value | Purpose |
|---------|-------|---------|
| `zend_extension` | `xdebug.so` | Load Xdebug extension |
| `mode` | `debug` | Enable debug mode |
| `start_with_request` | `yes` | Auto-start on request |
| `discover_client_host` | `true` | WSL2/Docker compatibility |
| `client_host` | `host.docker.internal` | Fallback for Windows/Docker |
| `client_port` | `9003` | Debug listener port |

## VS Code Setup

### Step 1: Install PHP Debug Extension

1. Open VS Code
2. Go to Extensions (Ctrl+Shift+X / Cmd+Shift+X)
3. Search for "PHP Debug"
4. Click Install (by Felix Becker)

### Step 2: Verify launch.json

Check `.vscode/launch.json` exists with:

```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "port": 9003,
            "pathMappings": {
                "/var/www/html": "${workspaceFolder}"
            },
            "log": true
        }
    ]
}
```

The `pathMappings` tell VS Code how to map container paths to local paths:
- `/var/www/html` (in container) → `${workspaceFolder}` (your project root)

## Starting Debugging

### 1. Start the Container

```bash
docker-compose up -d
```

Verify Xdebug is loaded:

```bash
docker-compose exec app php -v
```

You should see: `with Xdebug v3.5.0`

### 2. Start Listening in VS Code

1. Open **Run and Debug** (Ctrl+Shift+D / Cmd+Shift+D)
2. Select **"Listen for Xdebug"** from dropdown
3. Click the green **Play** button (or press F5)

Status bar will show: "Running" indicator

### 3. Set Breakpoints

Click on the line number in your code to set a breakpoint:

```php
<?php
declare(strict_types=1);

namespace App;

// Click here on line 6 to set breakpoint
$resumeData = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
];
```

Red dot appears on the line number.

### 4. Trigger Debugging

Open your browser and navigate to `http://localhost`

VS Code will:
1. Pause at any breakpoints
2. Show variable values
3. Allow stepping through code

## Debugging Features

### Variables Panel

Shows all variables in current scope:
- Local variables
- Superglobals (`$_GET`, `$_POST`, `$_SERVER`, etc.)
- Object properties

### Watch Panel

Monitor specific variables:
1. Click **+** in Watch section
2. Enter variable name: `$_POST`
3. Value updates with each step

### Debug Console

Execute PHP code in current context:

```
> $myVar
"some value"

> count($_POST)
3

> date('Y-m-d H:i:s')
"2026-02-01 20:31:00"
```

### Call Stack

View function call hierarchy:
- Current function
- Calling function
- Parent functions

### Breakpoint Types

**Line Breakpoint** (standard)
- Click line number
- Pauses at that line

**Conditional Breakpoint**
- Right-click line number → Add Conditional Breakpoint
- Specify condition: `$id > 100`
- Pauses only when true

**Logpoint**
- Right-click line number → Add Logpoint
- Logs message without pausing
- Great for debugging loops without interruption

## Workflow Example

### Debugging a Function

```php
<?php
declare(strict_types=1);

function processResume(array $data): array
{
    // Set breakpoint here (line 8)
    $validated = validateData($data);
    
    // Step Into: F11
    $formatted = formatData($validated);
    
    return $formatted;
}
```

**Debug Steps:**

1. Set breakpoint at line 8
2. Request http://localhost in browser
3. VS Code pauses at breakpoint
4. **Step Into** (F11): Enter `validateData()` function
5. **Step Over** (F10): Skip to next line in current function
6. **Step Out** (Shift+F11): Exit current function
7. **Continue** (F5): Resume execution

## Troubleshooting

### Xdebug Not Connecting

#### Check 1: Xdebug Loaded

```bash
docker-compose exec app php -v
```

Should show: `with Xdebug v3.5.0`

If missing, rebuild:

```bash
docker-compose down
docker-compose up -d --build
```

#### Check 2: Xdebug Configuration

```bash
docker-compose exec app php -i | grep xdebug
```

Should show `mode => debug => debug`

#### Check 3: VS Code Listening

- Check Run tab shows "Listen for Xdebug" is running
- Status bar should show debug icon

#### Check 4: Firewall/Network

On Windows with WSL2:
- Windows Firewall may block port 9003
- Temporarily disable to test
- Or add rule: Allow inbound on port 9003

### Breakpoints Not Hit

**Possible Causes:**

1. **Wrong file mapping**: Check `pathMappings` in `launch.json`
   - Container path must be `/var/www/html`
   - Workspace folder must be correct

2. **Code not executed**: Breakpoint is in unused code
   - Check your request actually hits that code
   - Add logpoint: `"Reached line X"`

3. **Xdebug not starting**: Set `xdebug.start_with_request=yes` in config

4. **Port conflict**: Another service using port 9003
   - Change in `launch.json`: `"port": 9004`
   - Update `xdebug.ini`: `xdebug.client_port=9004`

### Performance Impact

Debugging adds ~5-10% overhead. For production:

```bash
# Disable Xdebug in production
xdebug.mode=off
```

## Advanced Features

### Remote Debugging

For debugging on production server:

```ini
xdebug.mode=debug
xdebug.client_host=your.dev.machine.ip
xdebug.client_port=9003
```

Then listen on your machine's port 9003.

### Profiling

Generate execution profiles:

```ini
xdebug.mode=profile
xdebug.output_dir=/tmp
```

Profiles saved to container filesystem.

### Code Coverage

Generate code coverage reports:

```ini
xdebug.mode=coverage
```

Use PHPUnit to collect coverage metrics.

## Performance Tuning

### Reduce Debug Overhead

Only debug when needed:

```bash
# Disable Xdebug entirely
docker-compose exec app php -d xdebug.mode=off script.php
```

### Limit Nesting

```ini
# Default is 256, reduce for deeper inspection
xdebug.max_nesting_level=100
```

### Optimize IDE

- Close unused editor tabs
- Disable unnecessary extensions
- Increase VS Code memory: `"memory.max": 2048`

## Best Practices

1. **Use Breakpoints Strategically**
   - Place at function entry points
   - Use conditional breakpoints for loops
   - Remove after debugging

2. **Watch Important Variables**
   - Monitor business logic values
   - Check data transformations
   - Validate calculations

3. **Step Deliberately**
   - Step Into (F11) for detailed inspection
   - Step Over (F10) for function calls you trust
   - Step Out (Shift+F11) to exit functions

4. **Use Debug Console**
   - Explore object state
   - Execute quick calculations
   - Test variable changes

5. **Clean Up**
   - Remove unused breakpoints
   - Clear Watch expressions
   - Document findings in comments

## Next Steps

- [Docker Setup Guide](DOCKER.md) - Container management
- [Development Guide](DEVELOPMENT.md) - Code standards
- [Architecture Guide](ARCHITECTURE.md) - Project structure
