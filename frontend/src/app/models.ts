/** Shapes returned by the API (snake_case kept to match the JSON) */
export type Quantities = Record<string, number>;

export interface Recipe {
    name: string;
    feeds: number;
    ingredients: Quantities;
}

export interface Solution {
    meals: Quantities;
    leftovers: Quantities;
    leftover_total: number;
}

export interface SolveResult {
    max_fed: number;
    best: Solution | null;
    solutions: Solution[];
}

export interface SolveResponse {
    ingredients: Quantities;
    recipes: Recipe[];
    result: SolveResult;
}