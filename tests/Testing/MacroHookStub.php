<?php
namespace Starbug\Macro\Tests\Testing;

use Starbug\Macro\MacroHook;

/**
 * A MacroHook double that returns either a fixed value or the result of a
 * callback.
 */
class MacroHookStub extends MacroHook {

  /**
   * The value or callback this hook responds with.
   *
   * @var mixed a value to return, or a callable receiving ($name, $token, $data)
   */
  private $value;

  /**
   * Construct the hook double.
   *
   * @param mixed $value a value to return, or a callable receiving
   *   ($name, $token, $data)
   */
  public function __construct($value = null) {
    $this->value = $value;
  }

  public function replace($macro, $name, $token, $data) {
    if (is_callable($this->value)) {
      return call_user_func($this->value, $name, $token, $data);
    }
    return null === $this->value ? $token : $this->value;
  }
}
