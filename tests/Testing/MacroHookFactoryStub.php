<?php
namespace Starbug\Macro\Tests\Testing;

use Starbug\Macro\MacroHookFactoryInterface;

/**
 * A MacroHookFactoryInterface double backed by a plain array of hooks.
 */
class MacroHookFactoryStub implements MacroHookFactoryInterface {
  private $hooks;

  public function __construct($hooks = []) {
    $this->hooks = $hooks;
  }

  public function get($hook) {
    return isset($this->hooks[$hook]) ? $this->hooks[$hook] : null;
  }
}
