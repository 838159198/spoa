import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router'; // 路由
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { AppComponent } from './app.component';
import { MenuService } from './menu.service';
import { JobComponent } from './job/job.component';
import { EmptyComponent } from './empty.component';
import { EmployeeComponent } from './employee/employee.component';
import { JobCreateComponent } from './job/job-create.component';
import { JobListComponent } from './job/job-list.component';
import { JobService } from './job/job.service';
import { PersonComponent } from './employee/person.component';
import { EmployeeService } from './employee/employee.sevice';
import { EmptyModule } from './empty/empty.module';
import { AlertComponent } from './alert/alert.component';




// 路由
export const ROUTES: Routes = [

  {path: 'index', component: EmptyComponent},
  {path: 'job'  , component: JobComponent},
  {path: 'jobCreate/:id', component: JobCreateComponent},
  {path: 'jobCreate', component: JobCreateComponent},
  {path: 'jobList', component: JobListComponent},
  {path: 'employee', component: EmployeeComponent},
  {path: '**', component: EmptyComponent}, // **通配符，路径为空或找不到该路径都会采用通配符路径
];


@NgModule({
  declarations: [
    AppComponent,
    JobComponent,
    EmptyComponent,
    EmployeeComponent,
    JobCreateComponent,
    JobListComponent,
    PersonComponent,
    AlertComponent,

  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    EmptyModule,
    RouterModule.forRoot(ROUTES),
  ],
  providers: [MenuService, JobService, EmployeeService],
  bootstrap: [AppComponent]
})
export class AppModule { }
