--TEST--
Phar: attempt to create a Phar with relative path
--SKIPIF--
<?php if (!extension_loaded("phar")) die("skip"); ?>
--INI--
phar.require_hash=0
phar.readonly=0
--FILE--
<?php
chdir(dirname(__FILE__));
try {
	$p = new Phar('brandnewphar.phar');
	$p['file1.txt'] = 'hi';
	var_dump(strlen($p->getStub()));
	$p->setStub("<?php
spl_autoload_register(function(\$class) {
    include 'phar://' . str_replace('_', '/', \$class);
});
Phar::mapPhar('brandnewphar.phar');
include 'phar://brandnewphar.phar/startup.php';
__HALT_COMPILER();
?>");
	var_dump($p->getStub());
} catch (Exception $e) {
	echo $e->getMessage() . "\n";
}
?>
===DONE===
--CLEAN--
<?php
unlink(dirname(__FILE__) . '/brandnewphar.phar');
?>
--EXPECTF--
int(6641)
string(%d) "<?php
spl_autoload_register(function($class) {
    include 'phar://' . str_replace('_', '/', $class);
});
Phar::mapPhar('brandnewphar.phar');
include 'phar://brandnewphar.phar/startup.php';
__HALT_COMPILER(); ?>
"
===DONE===
