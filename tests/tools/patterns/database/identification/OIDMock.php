<?php
namespace PPHP\tests\tools\patterns\database\identification;

use PPHP\tools\patterns\database\identification\OID;
use PPHP\tools\patterns\database\identification\TOID;

class OIDMock implements OID{
  use TOID;
}