# Whitelist plugin for Craft CMS

Allow CP access based on an IP whitelist, including CIDR support.

<img src="src/icon.svg" alt="Icon" width="200">

## Requirements

This plugin requires Craft CMS 3.1 or later.

## Installation

Install from the Plugin Store or composer:

```bash
composer require onstuimig/craft-whitelist
php ./craft install/plugin whitelist
```

## Config
```php
<?php

return [
    'enabled' => true,

	// List all IPs or CIDR blocks allowed to access the CP
	'whitelist' => [
		'::1',
		'127.0.0.1'
	]
];
```

## License

Copyright © [Onstuimig](https://onstuimig.nl/)

See [license](https://github.com/onstuimig/craft-whitelist/blob/main/LICENSE.md)
