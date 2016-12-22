import { BrowserModule, Title } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { ChartsModule } from 'ng2-charts/ng2-charts';
import { AlertModule, ProgressbarModule } from 'ng2-bootstrap/ng2-bootstrap';

import { DataService } from './data.service';
import { ChartsComponent } from './charts.component';
import { AppComponent } from './app.component';

@NgModule({
  declarations: [
    AppComponent,
    ChartsComponent
  ],
  imports: [
    BrowserModule,
    ReactiveFormsModule,
    HttpModule,
    ChartsModule,
    AlertModule,
    ProgressbarModule
  ],
  providers: [ Title, DataService ],
  bootstrap: [ AppComponent ]
})
export class AppModule { }
