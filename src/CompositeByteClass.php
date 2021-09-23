<?php

declare(strict_types = 1);

namespace ClayFreeman\IRCParser;

/**
 * Enumerates the byte classes used to match data in the lexer input stream.
 */
class CompositeByteClass implements ByteClassInterface {

  use ByteClassTrait;

  /**
   * Constructs a CompositeByteClass object.
   *
   * @param \ClayFreeman\IRCParser\ByteClassInterface[] $classes
   *   A list of byte classes used to create the composite byte class.
   */
  public function __construct(array $classes = []) {
    $this->value = implode(array_map(fn($class) => $class->value(), $classes));
  }

}
