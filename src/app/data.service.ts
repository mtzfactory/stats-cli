import { Injectable } from '@angular/core';
import { Http, Response } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';
// environment
import { environment } from '../environments/environment';
// DATA INTERFACES
import {DataInterface} from './data.interface';

@Injectable()
export class DataService {
  private baseUrl:string;

  constructor(private http: Http) {
    if (environment.production) {
      this.baseUrl = 'assets/php/counters3.php';
    }
    else {
      this.baseUrl  = 'http://mtzfactory.dev/php/counters3.php';
    }
  }

  load(cell:string, days:number, table:string) : Observable<DataInterface> {
    let data$ = this.http.get(`${this.baseUrl}?nodo=${cell}&days=${days}&table=${table}`)
      .map((res:Response) => res.json());
    return data$;
  }
}
