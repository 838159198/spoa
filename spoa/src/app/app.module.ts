import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router'; // 路由

import { AppComponent } from './app.component';
import { MenuService } from './menu.service';
import { JobComponent } from './job/job.component';
import { EmptyComponent } from './empty.component';
import { EmployeeComponent } from './employee/employee.component';

// 路由
export const ROUTES: Routes = [
  {path: '', component: EmptyComponent},
  {path: 'index', component: EmptyComponent},
  {path: 'job'  , component: JobComponent},
  {path: 'employee', component: EmployeeComponent},
];


@NgModule({
  declarations: [
    AppComponent,
    JobComponent,
    EmptyComponent,
    EmployeeComponent,
  ],
  imports: [
    BrowserModule,
    RouterModule.forRoot(ROUTES),
  ],
  providers: [MenuService],
  bootstrap: [AppComponent]
})
export class AppModule { }
