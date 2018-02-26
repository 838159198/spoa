import { Component, OnInit, ViewChild } from '@angular/core';
import { JobComponent } from './job.component';
import { JobService } from './job.service';
import { Router } from '@angular/router';
import { AlertComponent } from '../alert/alert.component';
// import { JobCreateComponent } from './job-create.component';


@Component({
    selector: 'app-create-list',
    templateUrl: './job-list.component.html'
})

export class JobListComponent implements OnInit {
    jobLists: any[];
    content: any;
    @ViewChild(AlertComponent) alertCom: AlertComponent;
    constructor(protected jobService: JobService, protected router: Router) {

    }
    ngOnInit() {
       this.jobService.getjobs('http://shouji.com/api/spoa2').then(
           res => {
               this.jobLists = res.jobLists;
                console.log(this.jobLists);
            }
        );


    }
    // 删除
    onDelete(e) {
        this.jobService.delete('http://shouji.com/api/delete/id/' + e).then(
            res => {
                if (res.success === true) {
                    alert('删除成功');
                    location.reload();
                } else {
                    alert('删除失败');
                }
            }
        );
    }
    // 修改
    onModify(e) {
        this.router.navigate(['/jobCreate/' + e ]); // 跳转
    }

    ontan() {
        this.content = '我是个列表！';
        this.alertCom.open();
    }

}
