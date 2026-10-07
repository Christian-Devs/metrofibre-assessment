import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { Quantities, SolveResponse } from './models';

@Injectable({ providedIn: 'root' })
export class MealPlannerService {
    private readonly url = '/api.php';

    constructor(private http: HttpClient) { }

    /** Solve using the default stock from the assessment */
    solveDefault(): Observable<SolveResponse> {
        return this.http.get<SolveResponse>(this.url);
    }

    /** Solve using custom ingredient quantities */
    solve(ingredients: Quantities): Observable<SolveResponse> {
        return this.http.post<SolveResponse>(this.url, { ingredients });
    }
}