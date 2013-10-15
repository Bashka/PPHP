<?php
namespace PPHP\tests\tools\patterns\metadata;

use PPHP\tools\patterns\metadata as metadata;

class DescribedMock implements metadata\Described{
  use metadata\TDescribed;
}