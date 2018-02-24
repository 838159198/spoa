import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { JobService } from './job.service';


@Component({
    selector: 'app-job-create',
    templateUrl: './job-create.component.html',
})

export class JobCreateComponent implements OnInit {
    jobname: string;
    jobcode: string;
    joblist: any;
    jobId: number; // 更新的id
    constructor(protected router: Router, protected jobService: JobService, protected route: ActivatedRoute) {

    }
    ngOnInit() {
        this.route.params.subscribe((params) => this.jobId = params.id); // 获取url中的参数id的值
        if (this.jobId !== null && this.jobId !== undefined ) { // 获取该id的信息
            this.jobService.getjobs('http://shouji.com/api/spoa2/id/' + this.jobId ).then( res => {
                this.joblist = res.jobLists[0];
                this.jobname = this.joblist.name;
                this.jobcode = this.joblist.code;
                console.log(this.joblist);
            }
            );
        }
    }
    array(): void {
        this.joblist.push({name: this.jobname, code: this.jobcode});
        return  this.joblist;
    }
    // 提交
    submit() {
        this.joblist = {name: this.jobname, code: this.jobcode};
        console.log(this.jobcode + this.jobcode);
        if (this.jobId === undefined || this.jobId === null) {
            this.jobService.create('http://shouji.com/api/create', this.joblist).then(
            res => {
                if (res.success === true) {
                    this.router.navigate(['/jobList']); // 跳转
                } else {
                    alert('操作失败！');
                }
            }
            );
        } else {
            this.jobService.update('http://shouji.com/api/update/id/' + this.jobId, this.joblist).then(
                res => {
                    if (res.success === true) {
                            this.router.navigate(['/jobList']);
                    } else {
                        alert('更新失败');
                    }
                }
            );
        }


    }

}
