import { HttpErrorResponse } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { Observable } from 'rxjs';
import { MealPlannerService } from './meal-planner.service';
import { Quantities, Recipe, Solution, SolveResponse, SolveResult } from './models';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  readonly maxQuantity = 20;

  recipes: Recipe[] = [];
  ingredientNames: string[] = [];
  stock: Quantities = {};
  result: SolveResult | null = null;
  loading = false;
  error: string | null = null;

  constructor(private planner: MealPlannerService) { }

  ngOnInit(): void {
    this.reset();
  }

  /** Loads the assement's default stock and it's solution */
  reset(): void {
    this.run(this.planner.solveDefault(), true);
  }

  /** Solves for whatever quantities are currently entered. */
  solve(): void {
    this.run(this.planner.solve(this.stock), false);
  }

  /** Other combinations that feed the same number of people */
  get alternatives(): Solution[] {
    return this.result ? this.result.solutions.slice(1) : [];
  }

  describe(meals: Quantities): string {
    return Object.keys(meals)
      .map((name) => `${meals[name]} x ${name}`)
      .join(', ');
  }

  /** Stops the keyvalue pair from sorting alphabetically */
  keepOrder = (): number => 0;

  private run(request: Observable<SolveResponse>, replaceStock: boolean): void {
    this.loading = true;
    this.error = null;

    request.subscribe({
      next: (response) => {
        this.recipes = response.recipes;
        if (replaceStock) {
          this.stock = { ...response.ingredients };
          this.ingredientNames = Object.keys(response.ingredients);
        }
        this.result = response.result;
        this.loading = false;
      },
      error: (err: HttpErrorResponse) => {
        const message = err.error && typeof err.error.error === 'string' ? err.error.error : null;
        this.error = message || 'Could not reach the server. Is the PHP backend running?';
        this.loading = false;
      },
    });
  }
}
