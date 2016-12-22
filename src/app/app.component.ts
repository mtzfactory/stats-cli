import { Component, OnInit } from '@angular/core';
import { Title } from '@angular/platform-browser';
// forms
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
// personal
import { DataService } from './data.service';
import { DataInterface } from './data.interface';

@Component({
  selector: 'mtz-app',
  templateUrl: './app.component.html',
  styleUrls: [ './app.component.css' ]
})
export class AppComponent implements OnInit {
  public isGsmSelected:boolean = true;
  public formCell:FormGroup;
  public dataStore:DataInterface;
  public errorMessage:string;
  public elapsedTime:number;
  public totalElapsedTime:number;
  public auxElapsedTime:number = 0;
  private timerId1:any = null;
  private timerId2:any = null;

  constructor(private dataService: DataService, private fb: FormBuilder, private titleService: Title) {}

  ngOnInit(){
    this.createForm();
  }

  createForm() {
    this.formCell = this.fb.group({
      cellName: ['', Validators.required],
      cellDays: ['40', Validators.required]
    });
  }

  // https://github.com/angular/angular/blob/master/modules/playground/src/async/index.ts
  startTimer(): void {
    let x = 0;
    this.stopTimer();
    this.dataStore = null;
    this.totalElapsedTime = null;
    this.auxElapsedTime = 0;
    this.timerId1 = setInterval(() => { x++; this.elapsedTime = x * 5; }, 1000);
    this.timerId2 = setInterval(() => { this.auxElapsedTime++; }, 1);
  };

  stopTimer(): void {
    if (this.timerId1 != null) {
      clearInterval(this.timerId1);
      this.timerId1 = null;
      this.elapsedTime = null;
      clearInterval(this.timerId2);
      this.totalElapsedTime = this.auxElapsedTime;
      this.timerId2 = null;
    }
  };

  onSubmit(form: any): void {
    //console.log('> cell:', form.cellName || 'null');
    if (form.cellName && form.cellDays) {
      this.errorMessage = null;
      let table:string = this.isGsmSelected ? 'gsm_nokia_report' : 'umts_nokia_report';
      this.startTimer();
      this.dataService.load(form.cellName, form.cellDays, table)
        .subscribe(
          data => { this.dataStore = data; },
          error => {
            this.stopTimer();
            this.titleService.setTitle("stats by mtzFactory");
            this.errorMessage = "No se han podido cargar los datos.";
          },
          () => {
            this.stopTimer();
            this.titleService.setTitle("cell: " + form.cellName.toUpperCase());
            this.formCell.controls['cellName'].reset();
          }
        );
      //this.formCell.reset();
    }
  }
}
