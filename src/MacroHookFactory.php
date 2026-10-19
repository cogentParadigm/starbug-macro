<?php
namespace Starbug\Macro;

use Psr\Container\ContainerInterface;

class MacroHookFactory implements MacroHookFactoryInterface {
  protected $container;
  protected $hooks = [];
  public function __construct(ContainerInterface $container, $hooks = []) {
    $this->container = $container;
    $this->hooks = $hooks;
  }
  public function get($hook): ?MacroHook {
    if (isset($this->hooks[$hook])) {
      return $this->container->get($this->hooks[$hook]);
    }
    return null;
  }
}
