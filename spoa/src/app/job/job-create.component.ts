import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
    selector: 'app-job-create',
    templateUrl: './job-create.component.html',
})

export class JobCreateComponent {
    jobname: string;
    jobcode: string;
    joblist: any;
    constructor(protected router: Router) {

    }
    array(): void {
        this.joblist.push({name: this.jobname, code: this.jobcode});
        return  this.joblist;
    }
    submit() {
        console.log(this.jobcode + this.jobcode);
        this.router.navigate(['/jobList']); // 跳转
    }

}
