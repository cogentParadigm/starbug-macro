<?php
namespace Starbug\Macro\Tests;

use PHPUnit\Framework\TestCase;
use Starbug\Macro\Macro;
use Starbug\Macro\Tests\Testing\MacroHookFactoryStub;
use Starbug\Macro\Tests\Testing\MacroHookStub;

class MacroTest extends TestCase {

  private function macro($hooks = []) {
    return new Macro(new MacroHookFactoryStub($hooks));
  }

  public function testSearchGroupsTokensByType() {
    $macro = $this->macro();
    $expected = [
      "user" => ["email" => "[user:email]"],
      "url" => ["admin/a b" => "[url:admin/a b]"],
      "site" => ["name" => "[site:name]"]
    ];
    $this->assertEquals($expected, $macro->search("Hello [user:email] [url:admin/a b] from [site:name]"));
  }

  public function testReplaceSubstitutesProvidedData() {
    $macro = $this->macro();
    $this->assertSame("Hello john@doe.com", $macro->replace("Hello [user:email]", ["user" => ["email" => "john@doe.com"]]));
  }

  public function testReplaceSubstitutesEveryOccurrence() {
    $macro = $this->macro();
    $this->assertSame("Gala and Gala", $macro->replace("[event:name] and [event:name]", ["event" => ["name" => "Gala"]]));
  }

  public function testFalsyValuesAreSubstituted() {
    $macro = $this->macro();
    $cases = [
      ["[event:count]", ["event" => ["count" => 0]], "0"],
      ["[event:count]", ["event" => ["count" => "0"]], "0"],
      ["[event:note]", ["event" => ["note" => ""]], ""],
      ["[data:guest_count]", ["data" => ["guest_count" => "0"]], "0"]
    ];
    foreach ($cases as list($text, $data, $expected)) {
      $this->assertSame($expected, $macro->replace($text, $data), $text . " with " . var_export($data, true));
    }
  }

  public function testFalsyDataOverridesHookValue() {
    $macro = $this->macro(["event" => new MacroHookStub("from-hook")]);
    $this->assertSame("0", $macro->replace("[event:count]", ["event" => ["count" => 0]]));
  }

  public function testReplaceUsesHookForType() {
    $hook = new MacroHookStub(function ($name, $token, $data) {
      return "site:" . $name;
    });
    $macro = $this->macro(["site" => $hook]);
    $this->assertSame("site:name", $macro->replace("[site:name]"));
  }

  public function testUnresolvedTokensAreBlanked() {
    $macro = $this->macro();
    $this->assertSame("Hello ", $macro->replace("Hello [user:email]"));
    $this->assertSame("", $macro->replace("[unknown:thing]"));
    $this->assertSame("Gala ", $macro->replace("[event:name] [event:missing]", ["event" => ["name" => "Gala"]]));
  }
}
