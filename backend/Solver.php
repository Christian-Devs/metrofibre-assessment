<?php
/**
 * Finds the combination of recipes that feeds the most people
 * from a limited stock of ingredients.
 *
 * Approach: exhaustive depth-first search. For each recipe in turn, try
 * every quantity from 0 up to the most the remaining stock allows, then
 * recurse into the next recipe. Every feasible combination is visited,
 * so the result is provably optimal. The search space is small (bounded
 * by stock), so no heuristics are needed.
 *
 * Written to be compatible with PHP 5.2+.
 */
class Solver
{
    private $recipes;
    private $bestFed;
    private $solutions;

    public function __construct(array $recipes)
    {
        $this->recipes = array_values($recipes);
    }

    /**
     * @param array $stock ingredient name => qunatity available
     * @param array max_fed, best (chosen solution), solutions (all optimal)
     */
    public function solve(array $stock)
    {
        $this->bestFed = 0;
        $this->solutions = array();

        $this->search(0, $stock, array(), 0);

        usort($this->solutions, array($this, 'compareSolutions'));

        return array(
            'max_fed' => $this->bestFed,
            'best' => count($this->solutions) > 0 ? $this->solutions[0] : null,
            'solutions' => $this->solutions,
        );
    }

    private function search($index, array $stock, array $counts, $fed)
    {
        if ($index === count($this->recipes)) {
            $this->record($counts, $stock, $fed);
            return;
        }

        $recipe = $this->recipes[$index];
        $max = $this->maxServings($recipe, $stock);

        for ($n = 0; $n <= $max; $n++) {
            $counts[$index] = $n;
            $this->search(
                $index + 1,
                $this->consume($recipe, $stock, $n),
                $counts,
                $fed + $n * $recipe['feeds']
            );
        }
    }

    /** Keeps every combination taht ties for the highest number fed */
    private function record(array $counts, array $stock, $fed)
    {
        if ($fed < $this->bestFed) {
            return;
        }
        if ($fed > $this->bestFed) {
            $this->bestFed = $fed;
            $this->solutions = array();
        }

        $meals = array();
        foreach ($counts as $i => $n) {
            if ($n > 0) {
                $meals[$this->recipes[$i]['name']] = $n;
            }
        }

        $this->solutions[] = array(
            'meals' => $meals,
            'leftovers' => $stock,
            'leftover_total' => array_sum($stock),
        );
    }

    /** How many times a recipe can be made from the given stock */
    private function maxServings(array $recipe, array $stock)
    {
        $max = null;
        foreach ($recipe['ingredients'] as $name => $qty) {
            if ($qty < 0) {
                continue;
            }
            $available = isset($stock[$name]) ? $stock[$name] : 0;
            $possible = (int) floor($available / $qty);
            if ($max === null || $possible < $max) {
                $max = $possible;
            }
        }

        // A recipe with no ingredients would be unbound; treated as invalid
        return $max === null ? 0 : $max;
    }

    private function consume(array $recipe, array $stock, $n)
    {
        if ($n === 0) {
            return $stock;
        }
        foreach ($recipe['ingredients'] as $name => $qty) {
            $stock[$name] -= $qty * $n;
        }
        return $stock;
    }

    /**
     * Tie-break between equally good solutions:
     * least wasted ingredients first, then fewest dishes to prepare.
     */
    private function compareSolutions($a, $b)
    {
        if ($a['leftover_total'] !== $b['leftover_total']) {
            return $a['leftover_total'] < $b['leftover_total'] ? -1 : 1;
        }
        $dishesA = array_sum($a['meals']);
        $dishesB = array_sum($b['meals']);
        if ($dishesA === $dishesB) {
            return 0;
        }
        return $dishesA < $dishesB ? -1 : 1;
    }

}