import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router'; // 路由
import { FormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { MenuService } from './menu.service';
import { JobComponent } from './job/job.component';
import { EmptyComponent } from './empty.component';
import { EmployeeComponent } from './employee/employee.component';
import { JobCreateComponent } from './job/job-create.component';
import { JobListComponent } from './job/job-list.component';

// 路由
export const ROUTES: Routes = [
  {path: '', component: EmptyComponent},
  {path: 'index', component: EmptyComponent},
  {path: 'job'  , component: JobComponent},
  {path: 'jobCreate', component: JobCreateComponent},
  {path: 'jobList', component: JobListComponent},
  {path: 'employee', component: EmployeeComponent},

];


@NgModule({
  declarations: [
    AppComponent,
    JobComponent,
    EmptyComponent,
    EmployeeComponent,
    JobCreateComponent,
    JobListComponent,
  ],
  imports: [
    BrowserModule,
    FormsModule,
    RouterModule.forRoot(ROUTES),
  ],
  providers: [MenuService],
  bootstrap: [AppComponent]
})
export class AppModule { }
