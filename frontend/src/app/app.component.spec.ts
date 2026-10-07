import { HttpErrorResponse } from '@angular/common/http';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { FormsModule } from '@angular/forms';
import { of, throwError } from 'rxjs';
import { AppComponent } from './app.component';
import { MealPlannerService } from './meal-planner.service';
import { SolveResponse } from './models';

const response: SolveResponse = {
  ingredients: { Dough: 4, Meat: 4 },
  recipes: [{ name: 'Pie', feeds: 1, ingredients: { Dough: 2, Meat: 2 } }],
  result: {
    max_fed: 2,
    best: { meals: { Pie: 2 }, leftovers: { Dough: 0, Meat: 0 }, leftover_total: 0 },
    solutions: [{ meals: { Pie: 2 }, leftovers: { Dough: 0, Meat: 0 }, leftover_total: 0 }],
  },
};

describe('AppComponent', () => {
  let planner: jasmine.SpyObj<MealPlannerService>;
  let fixture: ComponentFixture<AppComponent>;
  let element: HTMLElement;

  beforeEach(async () => {
    planner = jasmine.createSpyObj('MealPlannerService', ['solveDefault', 'solve']);
    planner.solveDefault.and.returnValue(of(response));

    await TestBed.configureTestingModule({
      declarations: [AppComponent],
      imports: [FormsModule],
      providers: [{ provide: MealPlannerService, useValue: planner }],
    }).compileComponents();

    fixture = TestBed.createComponent(AppComponent);
    element = fixture.nativeElement;
    fixture.detectChanges();
  });

  it('shows the number of people fed on load', () => {
    expect(element.querySelector('.headline strong')?.textContent).toContain('2');
    expect(element.textContent).toContain('2 × Pie');
  });

  it('renders one input per ingredient', () => {
    expect(element.querySelectorAll('.stock input').length).toBe(2);
  });

  it('sends the current stock when calculating', () => {
    planner.solve.and.returnValue(of(response));

    fixture.componentInstance.solve();

    expect(planner.solve).toHaveBeenCalledWith({ Dough: 4, Meat: 4 });
  });

  it('shows the API error message when a request is rejected', () => {
    planner.solve.and.returnValue(
      throwError(() => new HttpErrorResponse({ status: 400, error: { error: 'Dough is invalid.' } }))
    );

    fixture.componentInstance.solve();
    fixture.detectChanges();

    expect(element.querySelector('.error')?.textContent).toContain('Dough is invalid.');
  });
});