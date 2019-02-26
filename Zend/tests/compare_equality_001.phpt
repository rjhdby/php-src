--TEST--
Equality of compare operations in compile and run time
--FILE--
<?php
$input = [
	0,
	1,
	2,
	-1,
	2.0,
	2.1,
	-2.0,
	-2.1,
	0.00000000000000000000000000000000000000000000000001,
	PHP_INT_MAX,
	PHP_INT_MIN,
	PHP_INT_MAX * 2,
	PHP_INT_MIN * 2,
	INF,
	NAN,
	[],
	[1, 2],
	[1, 2, 3],
	[1 => 2, 0 => 1],
	[1, 'a' => 2],
	[1, 4],
	[1, 'a'],
	[1, 2 => 2],
	[1, [ 2 ]],
	null,
	false,
	true,
	"",
	" ",
	"banana",
	"Banana",
	"banan",
	"0",
	"200",
	"20",
	"20a",
	" \t\n\r\v\f20",
	"2e1",
	"2e150",
	"10000000000000000000000000000000000000000000000000000",
	"-10000000000000000000000000000000000000000000000000000",
	"0.1",
	"-0.1",
	"1e-1",
	"-20",
	"-20.0",
	"0x14",
	(string) PHP_INT_MAX * 2,
	(string) PHP_INT_MIN * 2,
];

function makeParam($param){
	return str_replace("\n", "", var_export($param, true));
}

$c = 0;
$f = 0;

function prepareLine($op1, $op2, $cmp, $type){
	$cmp = (int) $cmp;
	$op1_p = makeParam($op1);
	$op2_p = makeParam($op2);
	return "\$c++;if ($cmp !== (int)($op1_p $type $op2_p)){echo \"$op1_p $type $op2_p\n\";\$f++;};\n";
}

$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'compare_equality_temp.php';
$file = fopen($filename, "w");

fwrite($file, "<?php\n");
$var_cnt = count($input);

foreach($input as $var) {
	for ($i = 0; $i < $var_cnt; $i++) {
		fwrite($file, prepareLine($var, $input[$i], $var == $input[$i], '=='));
		fwrite($file, prepareLine($var, $input[$i], $var != $input[$i], '!='));
		fwrite($file, prepareLine($var, $input[$i], $var === $input[$i], '==='));
		fwrite($file, prepareLine($var, $input[$i], $var !== $input[$i], '!=='));
		fwrite($file, prepareLine($var, $input[$i], $var > $input[$i], '>'));
		fwrite($file, prepareLine($var, $input[$i], $var >= $input[$i], '>='));
		fwrite($file, prepareLine($var, $input[$i], $var < $input[$i], '<'));
		fwrite($file, prepareLine($var, $input[$i], $var <= $input[$i], '<='));
		fwrite($file, prepareLine($var, $input[$i], $var <=> $input[$i], '<=>'));
	}
}

fclose($file);

include($filename);

if($c === 0) {
	echo "Completely failed\n";
} else {
	echo "Failed: $f\n";
}
?>
===DONE===
--CLEAN--
<?php
$fl = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'compare_equality_temp.php';
@unlink($fl);
?>
--EXPECTF--
Failed: 0
===DONE===