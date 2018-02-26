import { Component, OnInit, Input } from '@angular/core';

@Component({
    selector: 'app-alert',
    templateUrl: './alert.component.html',
    styleUrls: [],
})
export class AlertComponent implements OnInit {
    display: boolean;
    @Input()
    content: any;
    constructor() {
        // this.display = true;
    }
    ngOnInit() {

    }
    open() {
        this.display = true;
    }
    close () {
        this.display = false;
    }


}
