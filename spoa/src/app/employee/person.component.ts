import { Component, OnInit, Input, EventEmitter, Output } from '@angular/core';
@Component({
    selector: 'app-person',
    templateUrl: './person.component.html',
})
export class PersonComponent implements OnInit {
    @Input()
    type: string;
    username: string;
    password: string;
    @Output()
    whenCreate = new EventEmitter();
    ngOnInit() {
    }
    submit() {
        this.whenCreate.emit({username: this.username, password: this.password});
    }

}
