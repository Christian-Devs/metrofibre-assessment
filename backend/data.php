<?php
/**
 * Input data for the assessment: available stock and recipe definitions.
 * Kept separate from the solver so either can change independently.
 */
return array(
    'ingredients' => array(
        'Cucumber' => 2,
        'Olives'   => 2,
        'Lettuce'  => 3,
        'Meat'     => 6,
        'Tomato'   => 6,
        'Cheese'   => 8,
        'Dough'    => 10,
    ),
    'recipes' => array(
        array(
            'name'        => 'Burger',
            'feeds'       => 1,
            'ingredients' => array('Meat' => 1, 'Lettuce' => 1, 'Tomato' => 1, 'Cheese' => 1, 'Dough' => 1),
        ),
        array(
            'name'        => 'Pie',
            'feeds'       => 1,
            'ingredients' => array('Dough' => 2, 'Meat' => 2),
        ),
        array(
            'name'        => 'Sandwich',
            'feeds'       => 1,
            'ingredients' => array('Dough' => 1, 'Cucumber' => 1),
        ),
        array(
            'name'        => 'Pasta',
            'feeds'       => 2,
            'ingredients' => array('Dough' => 2, 'Tomato' => 1, 'Cheese' => 2, 'Meat' => 1),
        ),
        array(
            'name'        => 'Salad',
            'feeds'       => 3,
            'ingredients' => array('Lettuce' => 2, 'Tomato' => 2, 'Cucumber' => 1, 'Cheese' => 2, 'Olives' => 1),
        ),
        array(
            'name'        => 'Pizza',
            'feeds'       => 4,
            'ingredients' => array('Dough' => 3, 'Tomato' => 2, 'Cheese' => 3, 'Olives' => 1),
        ),
    ),
);