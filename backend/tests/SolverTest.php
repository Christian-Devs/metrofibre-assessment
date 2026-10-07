<?php
/**
 * Minimal dependency-free tests for Solver (PHP 5.2+ compatible).
 * Run: php tests/SolverTest.php   (exits non-zero on failure)
 */
require dirname(__FILE__) . '/../Solver.php';
$data = require dirname(__FILE__) . '/../data.php';

$failures = 0;

function check($label, $condition)
{
    global $failures;
    if ($condition) {
        echo "PASS  $label\n";
    } else {
        echo "FAIL  $label\n";
        $failures++;
    }
}

/** Independently re-checks a solution against the stock and recipes. */
function isFeasible(array $solution, array $recipes, array $stock, $expectedFed)
{
    $fed = 0;
    foreach ($recipes as $recipe) {
        $name = $recipe['name'];
        $n = isset($solution['meals'][$name]) ? $solution['meals'][$name] : 0;
        $fed += $n * $recipe['feeds'];
        foreach ($recipe['ingredients'] as $ingredient => $qty) {
            $stock[$ingredient] -= $qty * $n;
        }
    }
    foreach ($stock as $remaining) {
        if ($remaining < 0) {
            return false;
        }
    }
    return $fed === $expectedFed && $stock == $solution['leftovers'];
}

// --- Assessment data ---
$solver = new Solver($data['recipes']);
$result = $solver->solve($data['ingredients']);

check('feeds 12 people with the assessment data', $result['max_fed'] === 12);
check('finds all 5 optimal combinations', count($result['solutions']) === 5);
check('best solution wastes only 1 ingredient', $result['best']['leftover_total'] === 1);

$allFeasible = true;
foreach ($result['solutions'] as $solution) {
    if (!isFeasible($solution, $data['recipes'], $data['ingredients'], 12)) {
        $allFeasible = false;
    }
}
check('every reported solution is feasible and feeds 12', $allFeasible);

// --- Edge cases ---
$empty = array();
foreach ($data['ingredients'] as $name => $qty) {
    $empty[$name] = 0;
}
$result = $solver->solve($empty);
check('zero stock feeds nobody', $result['max_fed'] === 0);

$noRecipes = new Solver(array());
$result = $noRecipes->solve($data['ingredients']);
check('no recipes feeds nobody', $result['max_fed'] === 0);

$result = $solver->solve(array('Dough' => 4, 'Meat' => 4));
check('missing ingredients are treated as zero stock', $result['max_fed'] === 2);

$greedyTrap = new Solver(array(
    array('name' => 'Big', 'feeds' => 3, 'ingredients' => array('X' => 3)),
    array('name' => 'Small', 'feeds' => 2, 'ingredients' => array('X' => 2)),
));
$result = $greedyTrap->solve(array('X' => 4));
check('beats a greedy strategy (2 Small = 4, not 1 Big = 3)', $result['max_fed'] === 4);

echo $failures === 0 ? "\nAll tests passed.\n" : "\n$failures test(s) failed.\n";
exit($failures === 0 ? 0 : 1);