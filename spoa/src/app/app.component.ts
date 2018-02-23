import { Component, OnInit } from '@angular/core';
import { MenuService } from './menu.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'app';
  menus: any;
  constructor(protected menuService: MenuService ) {

  }
  ngOnInit() {
    this.menus = this.menuService.getMenuList();
  }

}
