import { Component, OnInit } from '@angular/core';
import { MenuService } from './menu.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit {
  title = 'app';
  menus: any;
  constructor(protected menuService: MenuService, protected router: Router ) {

  }
  ngOnInit() {
    this.menus = this.menuService.getMenuList();
  }
  gotoPage(value: any) {
    this.router.navigate([value]);
  }

}
