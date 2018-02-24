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
    // 提交
    submit() {
        this.joblist = {name: this.jobname, code: this.jobcode};
        console.log(this.jobcode + this.jobcode);
        this.jobService.create('http://shouji.com/api/create', this.joblist).then(
            res => {
                if (res.success === true) {
                    this.router.navigate(['/jobList']); // 跳转
                } else {
                    alert('操作失败！');
                }
            }
        );

    }

}
