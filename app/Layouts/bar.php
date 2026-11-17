<?php

use App\Exceptions\ClassException;
use App\Lib\Logger;
use App\Models\Category;

try {
    $catObjs = Category::all('cat');
} catch (ClassExecption $e) {
    Logger::getLogger()->critical("No Results Found: " . ['exception' => $e]);
    echo "No Results Found: ";
    die;
}
?>

<h1>Categories</h1>
    <li><a href='index.php'>View All</a></li>
    <?php
    foreach ($catObjs as $catObj): ?>
        <li>
        <a href='index.php?id=<?php echo $catObj->get('id');
         ?>'>
                <?php echo $catObj->get('cat') ?>
</a>
</li>
<?php endforeach; ?>

</ul>