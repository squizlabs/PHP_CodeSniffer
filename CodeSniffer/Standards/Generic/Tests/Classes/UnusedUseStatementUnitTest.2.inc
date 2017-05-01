<?php

namespace Widget {
  use Oof\Bar;
  use Foo\NotUsed;

  class Bla extends Bar {}
}

namespace {
  use Xuq\Used;
  use Qux\Unused;

  new Used;
}