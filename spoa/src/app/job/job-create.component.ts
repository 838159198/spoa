import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { JobService } from './job.service';

@Component({
    selector: 'app-job-create',
    templateUrl: './job-create.component.html',
})

export class JobCreateComponent {
    jobname: string;
    jobcode: string;
    joblist: any;
    constructor(protected router: Router, protected jobService: JobService) {

    }
    array(): void {
        this.joblist.push({name: this.jobname, code: this.jobcode});
        return  this.joblist;
    }
    submit() {
        this.joblist = {name: this.jobname, code: this.jobcode};
        console.log(this.jobcode + this.jobcode);
        this.jobService.create('http://shouji.com/api/create', this.joblist).then(
            res => {
                console.log(res);
            }
        );
        // this.router.navigate(['/jobList']); // 跳转
    }

}
