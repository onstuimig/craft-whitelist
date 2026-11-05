# Whitelist plugin for Craft CMS

Allow CP or front-end access based on an IP whitelist, including CIDR support.

<img src="src/icon.svg" alt="Icon" width="200">

## Requirements

This plugin requires Craft CMS 4.0 or later.

## Installation

Install from the Plugin Store or composer:

```bash
composer require onstuimig/craft-whitelist
php ./craft install/plugin whitelist
```

## Front-end template

When access is denied on the front-end, you can customize the error message template.  
To do this, add an `_iplock.twig` template in the root of your templates folder. The `{{ ip }}` variable is available to display the IP address the user is accessing the site from.

## Config
```php
<?php

return [
    'enabled' => true,
	'enabledFrontend' => false,

	// List all IPs or CIDR blocks allowed to access the CP and front-end
	'whitelist' => [
		'::1',
		'127.0.0.1'
	],

	// List all IPs or CIDR blocks allowed to access the front-end
	'whitelistFrontend' => [
		'10.10.0.20'
	]
];
```

## License

Copyright © [Onstuimig](https://onstuimig.nl/)

See [license](https://github.com/onstuimig/craft-whitelist/blob/main/LICENSE.md)
