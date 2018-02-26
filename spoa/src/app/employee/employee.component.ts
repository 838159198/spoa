import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { EmployeeService } from './employee.sevice';
@Component({
    selector: 'app-employee',
    templateUrl: './employee.component.html',
})
export class EmployeeComponent implements OnInit {
    fill = '添加用户';
    employeeLists: any[];
    constructor(protected router: Router, protected empService: EmployeeService ) {

    }

    ngOnInit() {
        this.empService.getEmployees('http://shouji.com/api/employeeAll').then(
            res => {
                console.log(res);
                this.employeeLists = res.jobLists;
            }
        );
    }
    onCreate(value) {
        this.empService.create('http://shouji.com/api/employee', value).then(
            res => {
                if (res.success === true) {
                    alert('增加成功');
                    location.reload();
                } else {
                    alert('增加失败');
                }
            }
        );
    }
}
