import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { MealPlannerService } from './meal-planner.service';

describe('MealPlannerService', () => {
    let service: MealPlannerService;
    let http: HttpTestingController;

    beforeEach(() => {
        TestBed.configureTestingModule({ imports: [HttpClientTestingModule] });
        service = TestBed.inject(MealPlannerService);
        http = TestBed.inject(HttpTestingController);
    });

    afterEach(() => http.verify());

    it('requests the default solution with GET', () => {
        service.solveDefault().subscribe();

        const request = http.expectOne('/api.php');
        expect(request.request.method).toBe('GET');
        request.flush({});
    });

    it('posts custom stock wrapped in an ingredients object', () => {
        service.solve({ Dough: 4, Meat: 4 }).subscribe();

        const request = http.expectOne('/api.php');
        expect(request.request.method).toBe('POST');
        expect(request.request.body).toEqual({ ingredients: { Dough: 4, Meat: 4 } });
        request.flush({});
    });
});