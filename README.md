# Install
`composer require vqhteam/ytdownload`

# Use

```
<?php
use Vqhteam\Ytdownload\YTDownload;
require_once "vendor/autoload.php";
$get = YTDownload::getLink("Youtube Video Id");
var_dump($get);
?>
```
