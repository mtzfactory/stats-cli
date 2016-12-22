import { Component, Input } from '@angular/core';
// personal
import { DataInterface } from './data.interface';

@Component({
  selector: 'charts-component',
  templateUrl: './charts.component.html',
  styleUrls: [ './charts.component.css' ]
})
export class ChartsComponent {
  @Input() dataStore:DataInterface;
  private chartLegend:boolean = true;
  private chartOptions:any = {
    animation: false,
    responsive: true,
    scales: {
      yAxes: [{
        ticks: { beginAtZero: true }
      }]
    },
    title: {
      display: true,
      padding: 5,
      text: 'mtzFactory'
    }
  };

  setDatasetVisibility(cell:string):void {
    this.dataStore.counters.forEach(counter => {
      counter.datasets.forEach(dataset => {
        if (dataset.label === cell) dataset.hidden = true;
      });
    });
  }

  getChartOptions(i:number):any {
    let newChartOptions = this.chartOptions;
    newChartOptions.title.text = this.dataStore.counters[i].counter.replace("G_", "").replace("U_", "").replace("_", " ");
    return newChartOptions;
  }

  dynamicColors(cell:string):string {
    let r = Math.floor(Math.random() * 255);
    let g = Math.floor(Math.random() * 255);
    let b = Math.floor(Math.random() * 255);
    return "rgb(" + r + "," + g + "," + b + ")";
  }
}
