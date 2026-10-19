<?php
namespace Starbug\Macro;

use function DI\autowire;
use function DI\get;

return [
  MacroInterface::class => autowire(Macro::class),
  MacroHookFactoryInterface::class => autowire(MacroHookFactory::class)
    ->constructorParameter("hooks", get("macro.hooks"))
];
