# Starbug macro

A token parsing and replacement engine. Text may contain placeholders like
`[user:email]`, and `Macro` substitutes them from registered hooks and
caller-supplied data.

## Installation

```
composer require starbug/macro
```

## Token syntax

A token is written `[type:name]`.

- `type` may not contain whitespace, `[`, `]` or `:`.
- `name` may not contain `[` or `]`. It may contain spaces and colons, so
  `[url:admin/menu_items/update/]` is valid.
- Tokens do not nest.

`search()` returns each distinct type/name pair, grouped by type, with the token
text as the value:

```php
<?php
use Starbug\Macro\Macro;
use Starbug\Macro\MacroHookFactory;

$macro = new Macro(new MacroHookFactory($container, [
  "site" => MacroSiteHook::class
]));

$macro->search("Hello [user:email] from [site:name]");
// [
//   "user" => ["email" => "[user:email]"],
//   "site" => ["name" => "[site:name]"],
// ]
```

## Replacing tokens

`replace()` takes the text and an optional array of values keyed by type, then
name:

```php
<?php
$macro->replace("Hello [user:email]", [
  "user" => ["email" => "john@doe.com"]
]);
// "Hello john@doe.com"
```

Falsy values are substituted. `0`, `"0"`, `""`, `false` and `null` all replace
their token, so a zero renders as `0` instead of leaking the token.

Tokens that neither a hook nor the data resolves are replaced with an empty
string, so internal token syntax never reaches end users.

Names are matched exactly: `[event:start-date]` and `[event:start_date]` are
different tokens.

## Hooks

A hook supplies the values for one token type. Extend `MacroHook` and register
it under the type name:

```php
<?php
namespace Acme\Macro;

use Starbug\Macro\MacroHook;

class SiteHook extends MacroHook {
  public function replace($macro, $name, $token, $data) {
    return $this->settings->get($name == "name" ? "site_name" : $name);
  }
}
```

```php
<?php
// etc/di.php
return [
  "macro.hooks" => [
    "site" => SiteHook::class
  ]
];
```

The hook receives `($macro, $name, $token, $data)`. `MacroHookFactory` reads
`macro.hooks` and resolves each class from the container. The base
`MacroHook::replace()` returns the token unchanged.

## Resolution order

For each token:

1. If a hook is registered for the type, its return value is used.
2. `$data[$type][$name]` overrides that value when the key exists, including
   falsy values.
3. Otherwise the token becomes an empty string.

## Caveats

- Replacement is a single pass. A value that contains a token is not expanded
  again.

## Testing

```
composer install
composer phpunit
```

Coding standard:

```
composer phpcs
composer phpcbf
```
